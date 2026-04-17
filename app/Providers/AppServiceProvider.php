<?php

namespace App\Providers;

use App\Models\Note;
use App\Models\User;
use App\Policies\NotePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Policy: model-scoped authorization (students edit/delete own notes)
        Gate::policy(Note::class, NotePolicy::class);

        // Gate: simple one-off check (admins can delete ANY note)
        Gate::define('delete-any-note', fn(User $user) => $user->isAdmin());
    }
}
