<?php

namespace App\Providers;

use App\Models\Note;
use App\Models\User;
use App\Policies\NotePolicy;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Auto-create the SQLite file if it doesn't exist (for hosting environments)
        if (DB::getDriverName() === 'sqlite') {
            $path = DB::getConfig('database');
            if ($path && $path !== ':memory:' && ! file_exists($path)) {
                touch($path);
            }
        }

        // Auto-migrate on first deploy if tables haven't been created yet
        if (! app()->runningInConsole() && ! Schema::hasTable('users')) {
            Artisan::call('migrate', ['--force' => true]);
        }

        // Policy: model-scoped authorization (students edit/delete own notes)
        Gate::policy(Note::class, NotePolicy::class);

        // Gate: simple one-off check (admins can delete ANY note)
        Gate::define('delete-any-note', fn(User $user) => $user->isAdmin());
    }
}
