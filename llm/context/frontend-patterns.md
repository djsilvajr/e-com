# Frontend Patterns

## Flow (CRITICAL)

Route → Http/Controllers/Web → Service → Repository → Model

Same layered separation as the API. Web controllers MUST NOT call repositories directly, and MUST NOT contain business logic.

```
routes/web.php
   → App\Http\Controllers\Web\*Controller
       → App\Services\*Service
           → App\Services\<Domain>\<UseCase>
               → App\Repository\Contracts\*Interface
                   → App\Repository\*Repository
                       → App\Models\*Model
```

## Layer Responsibilities

### Route (routes/web.php)

Defines URL, HTTP verb, middleware, and the controller action. No logic.

### Web Controller (App\Http\Controllers\Web)

Receives the `FormRequest` or `Request`. Calls a Service. Returns a Blade view or a redirect. Never accesses repositories and never holds business rules.

### Service (App\Services\*Service)

Orchestrates UseCases through dependency injection. No business rules.

### UseCase (App\Services\<Domain>\*)

Holds ALL business rules. Returns structured data or throws a domain exception.

### Repository (App\Repository)

Eloquent / DB access only. No validation, no business logic.

### Model (App\Models)

Eloquent model. Represents a single table.

## Application Areas

The application is split into two public-facing areas, both served by the web stack:

- `/` — public storefront (the e-commerce the end customer visits).
- `/admin` — management area used by the store owner / staff.

Both areas share the same Laravel app and the same session guard (see below), but they live under different route groups, different controller subfolders, and different Blade layouts.

### Suggested folder layout

```
app/Http/Controllers/Web/
    Storefront/   # controllers for "/"
    Admin/        # controllers for "/admin"

resources/views/
    components/
        navbar.blade.php         # storefront navbar (ONLY reusable component)
        footer.blade.php         # storefront footer (ONLY reusable component)
    layouts/
        storefront.blade.php     # base layout for "/"
        admin.blade.php          # base layout for "/admin"
    storefront/                  # storefront pages (blade, no extra components)
    admin/                       # admin pages (blade, no extra components)
        login.blade.php
```

## Componentization Policy (STRICT)

Only two Blade components exist in this project:

- `resources/views/components/navbar.blade.php`
- `resources/views/components/footer.blade.php`

Everything else MUST be written directly inside its own Blade file, using the base layout. Do NOT create partials, Livewire components, Blade components, Vue/React components, or shared includes for anything else. Each page is a self-contained `.blade.php` file that `@extends` a layout.

Rules:
- Only `navbar` and `footer` may be reused as components.
- Navbar + footer are storefront-only by default. Admin screens use their own `admin.blade.php` layout and do NOT reuse the storefront navbar/footer.
- No `@include` for page content, no `x-*` components outside of `<x-navbar />` and `<x-footer />`.

## UI Library

The project uses **Semantic UI** (CSS + JS). jQuery is required by Semantic UI's JS modules.

CDN assets to load on every layout:

```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.5.0/dist/semantic.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.5.0/dist/semantic.min.js"></script>
```

Rules:
- Use Semantic UI classes (`ui container`, `ui menu`, `ui form`, `ui button`, `ui grid`, `ui segment`, `ui message`, etc.) for ALL styling.
- Do NOT mix Bootstrap, Tailwind, or custom design systems into the pages. The legacy `layouts/app.blade.php` / `welcome.blade.php` / `login/login.blade.php` files use Tailwind/Bootstrap and are considered legacy — new pages go through `layouts/storefront.blade.php` or `layouts/admin.blade.php`.
- Inline `<style>` may be used sparingly for layout tweaks, but prefer Semantic UI utilities first.

## Authentication (CRITICAL)

The app has TWO authentication surfaces that DO NOT share the same mechanism:

| Surface | Guard | Driver  | Used by                              |
|---------|-------|---------|--------------------------------------|
| API     | `api` | `jwt`   | Everything under `routes/api.php`    |
| Web     | `web` | `session` | Storefront (`/`) and Admin (`/admin`) |

The frontend uses the Laravel **session guard (`web`)** — NOT JWT. This is intentional:

- The admin area and the storefront log in via a normal HTML form (`POST /admin/login`, etc.), authenticate using `Auth::guard('web')->attempt(...)`, and then rely on the session cookie + CSRF for subsequent requests.
- The session is started by `\Illuminate\Session\Middleware\StartSession::class` in the `web.stack` middleware group (see `bootstrap/app.php`).
- The API guard (`api`, driver `jwt`) is completely separate and MUST NOT be used by Blade pages. Do not call the `/api/*` endpoints from the storefront/admin Blades with JWT — if a Blade page needs data, the Web Controller fetches it through the Service layer server-side and passes it to the view.
- Login state is checked via `Auth::guard('web')->check()` and `Auth::guard('web')->user()`.
- Logout MUST call `Auth::guard('web')->logout()`, then `session()->invalidate()` and `session()->regenerateToken()`.

CSRF:
- Every form MUST include `@csrf`.
- `VerifyCsrfToken` middleware is part of `web.stack`.

### Protecting routes

Protected web routes are placed inside the `web.stack` middleware group. That group already wires up session + `web.auth:web` + CSRF + error sharing. Admin routes are additionally grouped under the `/admin` prefix and MUST be gated by the `web.permission` middleware (see "Authorization (Roles / Permissions)" below) — but the base auth is still the session guard `web`.

```php
Route::prefix('admin')->group(function () {
    // Public admin routes — login pages, NO permission check
    Route::get('/login', [AdminLoginController::class, 'loginView'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'loginAttempt'])->name('admin.login.attempt');

    // Protected admin routes — auth + admin role enforced
    Route::middleware(['web.stack', 'web.permission:admin'])->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    });
});
```

## Authorization (Roles / Permissions) — CRITICAL

The web side uses a dedicated middleware called `web.permission`, registered in
`bootstrap/app.php` and implemented by `App\Http\Middleware\CheckWebPermission`.
It is the **web equivalent** of the API's `api.permission` middleware:

| Surface | Alias            | Class                                       | Guard | Checks                       |
|---------|------------------|---------------------------------------------|-------|------------------------------|
| API     | `api.permission` | `App\Http\Middleware\CheckUserPermission`   | `api` | `user->hasPermission($name)` |
| Web     | `web.permission` | `App\Http\Middleware\CheckWebPermission`    | `web` | Role name OR permission name |

### How `web.permission` works

`web.permission:{name}` reads the authenticated user from the **session guard
(`web`)** and accepts the given `{name}` as **either**:

1. A **role name** (e.g. `admin`, `manager`, `user`) — matched against
   `user->roles->contains('name', $name)`.
2. A **permission name** (e.g. `user.delete`) — matched against
   `user->hasPermission($name)` (which walks roles → permissions).

Failure handling:

- **Not authenticated on the `web` guard** → `redirect()->route('admin.login')`.
- **Authenticated but missing the required role/permission** → `abort(403)`
  with a clear message. (Web Controllers MUST NOT return JSON — the 403 is
  rendered by Laravel's default error view.)

### Usage

```php
// Mirrors api.permission:user.delete — but for the web guard
Route::middleware('web.permission:admin')->group(function () { ... });

Route::middleware('web.permission:user.delete')->group(function () { ... });
```

### Admin area rule (STRICT)

**Every route under `/admin` MUST be gated by `web.permission:admin`, with the
sole exception of the login routes** (`GET /admin/login` and
`POST /admin/login`). The login routes MUST stay public so an unauthenticated
admin can actually reach the form. Everything else — dashboard, logout, all
future admin CRUD — goes inside the `['web.stack', 'web.permission:admin']`
middleware stack.

Rules:

- Do NOT add `web.permission:admin` to login routes (creates a redirect loop /
  permanent 403 for non-logged-in admins).
- Do NOT rely on hiding admin links in the navbar as the only protection — the
  middleware is the source of truth.
- Storefront routes (`/`) do NOT use `web.permission:admin`. If a storefront
  route needs gating, use `web.permission` with the appropriate role /
  permission name (e.g. `web.permission:user`).
- Never use `api.permission` on a web route — it depends on the JWT guard and
  will not see the session user.

## Validation

Web forms use the same pattern as API forms:

- Always use a `FormRequest` for POST/PUT/PATCH/DELETE actions (see `llm/rules/validation.md`).
- On validation failure, throw `App\Exceptions\InvalidParametersException` or redirect back with `withErrors()` + `withInput()`.
- Inside Blade, display errors via `$errors->any()` / `$errors->first('field')` (works because `ShareErrorsFromSession` is in `web.stack`).

## Error Handling

- UseCases throw domain exceptions (same as API).
- Web Controllers catch the exception when it should be displayed inline (invalid credentials, duplicated value, not found) and re-render the view with an error message.
- Unhandled exceptions bubble up to Laravel's exception handler.

## Redirect / Response Rules

- Successful POST → `redirect()->route('...')` (PRG pattern).
- Failed form → `back()->withErrors(...)->withInput()` OR re-render the view with an `$error` string for simple cases.
- GET pages → `view('path.to.view', [...])`.
- Web Controllers MUST NOT return JSON. JSON belongs to the API layer.

## Do NOT

- Skip layers (Controller → Repository is forbidden).
- Put business logic in Blade, Controller, or Service.
- Use JWT in the web area — the web area is session-based.
- Create reusable components other than `navbar` and `footer`.
- Load Bootstrap or Tailwind alongside Semantic UI on new pages.

## Goal

Keep the web area consistent with the API's Clean-Architecture style, enforce a tiny component surface (just navbar + footer), and make it obvious that Blade pages run on the session guard while the API runs on JWT.
