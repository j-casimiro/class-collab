# class-collab — Live Demo Reference

> **How to use this:** Keep this file open on your second screen.
> Each phase has: the command or file to open → the key lines to type or highlight → a one-line talking point.
> All file paths are relative to the project root.

---

## Seed Accounts (for all demos)

| Role    | Email               | Password   |
|---------|---------------------|------------|
| Admin   | admin@example.com   | `password` |
| Student | alice@example.com   | `password` |
| Student | bob@example.com     | `password` |

---

## Phase 0 — Project Setup

> "Let's build this from a fresh Laravel install."

### Commands — run in order

```bash
# 1. Create project
laravel new class-collab --no-interaction
cd class-collab

# 2. Install Breeze (auth scaffolding)
composer require laravel/breeze --dev
php artisan breeze:install blade --no-interaction

# 3. Build frontend assets
npm install && npm run build

# 4. Set up database (SQLite is fine for demo)
touch database/database.sqlite
# In .env: DB_CONNECTION=sqlite, remove other DB_ lines

# 5. Run migrations + seed
php artisan migrate --seed
```

### Show after setup

Open **`routes/web.php`**
- Point out `require __DIR__.'/auth.php'` — Breeze injected all login/register routes for free.

---

## Phase 1 — Tour the Existing CRUD

> "The notes app already works — but there's zero protection. Anyone can edit or delete anyone's notes."

### Files to open

**`routes/web.php`** — highlight:
```php
Route::middleware('auth')->group(function () {
    Route::resource('notes', NoteController::class);
    Route::get('/notes/{note}/download', [NoteController::class, 'download'])->name('notes.download');
});
```
> "`auth` middleware means you must be logged in — but that's all. No ownership check."

**`app/Http/Controllers/NoteController.php`** — show `destroy()`:
```php
public function destroy(Note $note): RedirectResponse
{
    // ← NO authorization check here yet
    Storage::disk('private')->delete($note->file_path);
    $note->delete();
    return redirect()->route('notes.index')->with('success', 'Note deleted.');
}
```
> "Alice can DELETE Bob's note. Let's fix that — in three layers."

**`app/Models/Note.php`** — show the relationship:
```php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```
> "Every note belongs to the user who uploaded it. We'll use this to enforce ownership."

---

## Phase 2 — User Roles

> "Before we write any authorization, we need a way to tell students apart from admins."

### Step 1 — Migration

```bash
php artisan make:migration add_role_to_users_table --table=users
```

Open the new file in **`database/migrations/`**, add inside `up()`:
```php
$table->enum('role', ['student', 'admin'])->default('student')->after('password');
```
Add inside `down()`:
```php
$table->dropColumn('role');
```

```bash
php artisan migrate
```
> "Every new user gets `student` by default. We'll manually assign `admin` in the seeder."

### Step 2 — User model

Open **`app/Models/User.php`**

Add `'role'` to the `#[Fillable]` attribute (or `$fillable` array):
```php
#[Fillable(['name', 'email', 'password', 'role'])]
```

Add the helper method:
```php
public function isAdmin(): bool
{
    return $this->role === 'admin';
}
```
> "`isAdmin()` is a simple boolean helper — we'll call it from middleware, gates, and policies."

Add the relationship:
```php
public function notes(): HasMany
{
    return $this->hasMany(Note::class);
}
```

### Step 3 — Seeder

Open **`database/seeders/DatabaseSeeder.php`** — show the three accounts:
```php
User::factory()->create(['email' => 'admin@example.com', 'role' => 'admin', ...]);
User::factory()->create(['email' => 'alice@example.com', 'role' => 'student', ...]);
User::factory()->create(['email' => 'bob@example.com',   'role' => 'student', ...]);
```

```bash
php artisan db:seed
```

---

## Phase 3 — Custom Middleware

> "Middleware runs before the controller. We'll use it to block non-admins from entire routes."

### Step 1 — Create it

```bash
php artisan make:middleware EnsureUserIsAdmin
```

### Step 2 — Write the logic

Open **`app/Http/Middleware/EnsureUserIsAdmin.php`** — replace the `handle()` body:
```php
public function handle(Request $request, Closure $next): Response
{
    if (! $request->user() || ! $request->user()->isAdmin()) {
        abort(403, 'Access denied. Admins only.');
    }

    return $next($request);
}
```
> "If the user isn't an admin, the request stops here with a 403. Otherwise `$next($request)` passes it along the pipeline."

### Step 3 — Register the alias

Open **`bootstrap/app.php`** — inside `withMiddleware()`:
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin' => EnsureUserIsAdmin::class,
    ]);
})
```
> "The alias `'admin'` lets us write `->middleware('admin')` on any route."

### Step 4 — Apply to a route (live demo)

In **`routes/web.php`**, show how you'd protect a hypothetical admin dashboard:
```php
Route::get('/admin', function () {
    return 'Admin area';
})->middleware(['auth', 'admin']);
```
> "Try this URL as Alice — you get 403. Log in as admin — you get through."

---

## Phase 4 — Gates

> "Gates are closures for simple, one-off checks not tied to a model. Think: 'can this user do this action at all?'"

### Step 1 — Define the gate

Open **`app/Providers/AppServiceProvider.php`** — inside `boot()`:
```php
use Illuminate\Support\Facades\Gate;
use App\Models\Note;
use App\Models\User;

public function boot(): void
{
    Gate::define('delete-any-note', fn(User $user) => $user->isAdmin());
}
```
> "One line. This gate returns true only if the user is an admin."

### Step 2 — Enforce in the controller

Open **`app/Http/Controllers/NoteController.php`** — add to `destroy()`:
```php
public function destroy(Note $note): RedirectResponse
{
    $this->authorize('delete-any-note'); // ← Gate check
    ...
}
```
> "`$this->authorize()` calls the gate. If it returns false, Laravel throws a 403 automatically."

### Step 3 — Hide the button in Blade

Open **`resources/views/notes/index.blade.php`** — around line 63:
```blade
@can('delete-any-note')
    <form action="{{ route('notes.destroy', $note) }}" method="POST">
        @csrf @method('DELETE')
        <button type="submit">Delete</button>
    </form>
@endcan
```
> "`@can` / `@endcan` wraps UI elements — the button doesn't even render for students."

**Demo:** Log in as `alice@example.com` — Delete button is gone. Log in as `admin@example.com` — Delete button appears.

---

## Phase 5 — Policies

> "Gates are good for simple checks. Policies are better when the logic involves a specific model instance — like 'can THIS user edit THIS note?'"

### Step 1 — Generate the policy

```bash
php artisan make:policy NotePolicy --model=Note
```

### Step 2 — Write the rules

Open **`app/Policies/NotePolicy.php`** — fill in the methods:

```php
// Anyone logged in can view all notes
public function viewAny(User $user): bool { return true; }
public function view(User $user, Note $note): bool { return true; }

// Anyone logged in can upload
public function create(User $user): bool { return true; }

// Only the owner can edit
public function update(User $user, Note $note): bool
{
    return $user->id === $note->user_id;
}

// Owner OR admin can delete
public function delete(User $user, Note $note): bool
{
    return $user->id === $note->user_id || $user->isAdmin();
}

// Anyone can download
public function download(User $user, Note $note): bool { return true; }
```
> "The key difference from a Gate: the policy method receives the model instance — `$note` — so we can compare `$user->id === $note->user_id`."

### Step 3 — Register the policy

Open **`app/Providers/AppServiceProvider.php`** — add to `boot()`:
```php
Gate::policy(Note::class, NotePolicy::class);
```
> "This tells Laravel: 'whenever you authorize against a Note, use NotePolicy'."

### Step 4 — Enforce in the controller

Open **`app/Http/Controllers/NoteController.php`** — highlight these:

```php
// edit() — only owner can reach the edit form
public function edit(Note $note): View
{
    $this->authorize('update', $note); // ← Policy check, passes $note
    return view('notes.edit', compact('note'));
}

// update() — same check on the POST handler
public function update(Request $request, Note $note): RedirectResponse
{
    $this->authorize('update', $note);
    ...
}

// destroy() — policy's delete() method (owner OR admin)
public function destroy(Note $note): RedirectResponse
{
    $this->authorize('delete', $note);
    ...
}
```
> "Note how `authorize('update', $note)` passes the model instance. Laravel routes it to `NotePolicy::update($user, $note)` automatically."

### Step 5 — Conditional buttons in Blade

Open **`resources/views/notes/show.blade.php`** — around lines 44–62:
```blade
@can('update', $note)
    <a href="{{ route('notes.edit', $note) }}">Edit</a>
@endcan

@can('delete', $note)
    <form action="{{ route('notes.destroy', $note) }}" method="POST">
        @csrf @method('DELETE')
        <button>Delete</button>
    </form>
@endcan
```

Open **`resources/views/notes/index.blade.php`** — around lines 58–72:
```blade
@can('update', $note)
    <a href="{{ route('notes.edit', $note) }}">Edit</a>
@endcan

@can('delete', $note)
    <form ...>Delete</form>
@endcan
```
> "Same `@can` directive — but now it takes the model too: `@can('update', $note)`. Blade calls the policy automatically."

---

## Live Demo — End-to-End Walkthrough

> Run through this with the class to prove all three layers work.

```bash
php artisan serve
```

| Step | Action | Expected result |
|------|--------|-----------------|
| 1 | Log in as **alice@example.com** | See Alice's notes with Edit/Delete on her own |
| 2 | Navigate to a note owned by **Bob** | No Edit button visible |
| 3 | Manually visit `/notes/{bob_note_id}/edit` in the URL bar | **403 Forbidden** (Policy blocks it) |
| 4 | Log out → log in as **admin@example.com** | Delete button visible on ALL notes |
| 5 | Try to access an `->middleware('admin')` route as Alice | **403** (Middleware blocks it) |

---

## Quick Reference — Three Layers

| Layer | File | What it controls | Key syntax |
|-------|------|------------------|------------|
| **Middleware** | `app/Http/Middleware/EnsureUserIsAdmin.php` | Entire routes (before controller) | `abort(403)` / `$next($request)` |
| **Gate** | `app/Providers/AppServiceProvider.php` | Single action, no model needed | `Gate::define(...)` / `$this->authorize('name')` |
| **Policy** | `app/Policies/NotePolicy.php` | Actions on a specific model instance | `Gate::policy(...)` / `$this->authorize('action', $model)` |

---

## Key Artisan Commands (cheat sheet)

```bash
php artisan make:middleware EnsureUserIsAdmin
php artisan make:policy NotePolicy --model=Note
php artisan make:migration add_role_to_users_table --table=users
php artisan migrate
php artisan db:seed
php artisan route:list          # verify routes + middleware
php artisan serve
```
