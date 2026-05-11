# Task: Create Admin Frontend View

## Context (READ FIRST — MANDATORY)

Before writing a single line of code, the LLM MUST read and follow:

- `llm/context/frontend-patterns.md` — flow, layers, Blade rules, UI library, auth, **`web.permission` middleware**
- `llm/context/architecture.md` — layered architecture (Controller → Service → UseCase → Repository)
- `llm/context/api-patterns.md` — only relevant for cross-checking; this task does NOT touch the API
- `llm/rules/validation.md` — FormRequest validation rules

## Project DIR

- `laravel`

## Area (STRICT)

This task creates a page inside the **Admin area** (`/admin/*`).

- Layout: `resources/views/layouts/admin.blade.php`
- Controller namespace: `App\Http\Controllers\Web\Admin`
- View folder: `resources/views/admin/`
- Route prefix: `admin`
- Route name prefix: `admin.`
- Auth guard: `web` (session-based, NOT JWT)
- **Authorization: every route MUST be gated by `web.permission:admin`** (except login)

## View

{{VIEW_NAME}}            <!-- e.g. "products.index" -->
{{VIEW_FILE}}            <!-- e.g. "resources/views/admin/products/index.blade.php" -->
{{PAGE_TITLE}}           <!-- e.g. "Produtos" -->

## Route(s)

{{METHOD}} /admin/{{URI}}            <!-- e.g. GET /admin/products -->
Route name: admin.{{ROUTE_NAME}}     <!-- e.g. admin.products.index -->

Rules (CRITICAL):

- Register the route(s) in `routes/web.php`, inside the existing
  `Route::prefix('admin')` group.
- Place them inside the protected sub-group:
  `Route::middleware(['web.stack', 'web.permission:admin'])->group(...)`.
- Do NOT add `web.permission:admin` to login routes
  (`admin.login`, `admin.login.attempt`).
- Do NOT create new middleware aliases — `web.permission` already exists in
  `bootstrap/app.php`.

## Controller

{{CONTROLLER}}           <!-- e.g. AdminProductController -->

Rules:

- Namespace: `App\Http\Controllers\Web\Admin`.
- Receives `FormRequest` or `Request`.
- Calls a **Service** (never a Repository, never a UseCase directly).
- Returns a Blade view OR a `redirect()` (PRG pattern). **MUST NOT return JSON.**
- No business logic, no Eloquent calls.

## Request (FormRequest)

{{REQUEST_CLASS}}        <!-- e.g. AdminCreateProductRequest -->

Required for any POST / PUT / PATCH / DELETE action. Follow `llm/rules/validation.md`.

Rules (fill in as needed):

- field_a: required | string | max:255
- field_b: required | int | exists:table,column

## Service

{{SERVICE_CLASS}}        <!-- e.g. AdminProductService -->

Rules:

- Located in `App\Services\`.
- Orchestrates UseCases through dependency injection.
- No business logic.
- No Repository access.

## UseCase(s)

{{USECASE_CLASSES}}      <!-- e.g. App\Services\Product\GetProductsForAdmin -->

Rules:

- One UseCase per business operation.
- All business rules live here.
- Calls Repository Interfaces only.
- Returns structured data or throws a domain exception.

## Repository

{{REPOSITORY_INTERFACE}} / {{REPOSITORY_CLASS}}

Only create / extend if a new query is needed. Repository MUST NOT contain
business logic.

## Business Rules

{{RULES}}

If no rules are provided:

- Do NOT invent business rules.
- Keep all logic inside the UseCase.

## Flow (STRICT)

1. Route `routes/web.php` (under `web.stack` + `web.permission:admin`)
2. Admin Controller (`App\Http\Controllers\Web\Admin`)
3. FormRequest validation
4. Service
5. UseCase (business rules)
6. Repository (DB access)
7. Controller returns `view('admin.{{VIEW_NAME}}', [...])` or `redirect()->route('admin....')`

Rules:

- Must implement all steps in order.
- Must NOT skip layers (no Controller → Repository, no Controller → UseCase).
- Must NOT put business logic in Controller, Service, or Blade.

## Blade View Rules (STRICT — see frontend-patterns.md)

- `@extends('layouts.admin')`
- File lives directly under `resources/views/admin/` — no partials, no Blade
  components, no `@include`s.
- Style with **Semantic UI** classes ONLY (`ui container`, `ui menu`, `ui form`,
  `ui button`, `ui grid`, `ui segment`, `ui message`, …). No Bootstrap,
  no Tailwind.
- Do NOT use the storefront `<x-navbar />` / `<x-footer />` components.
- Every form MUST include `@csrf`.
- Display validation errors via `$errors->any()` / `$errors->first('field')`.
- Do NOT call the API from the Blade (no JWT, no fetch to `/api/*`). All data
  comes server-side from the Controller via the Service.

## Authorization (CRITICAL)

- The admin user is loaded via `Auth::guard('web')->user()`.
- Route-level protection is handled by `web.permission:admin` — do not
  re-check the role inside the Controller unless you need finer-grained
  permissions (in which case use a more specific `web.permission:{name}`).
- If a route needs an additional permission beyond `admin`, stack it:
  `->middleware(['web.stack', 'web.permission:admin', 'web.permission:user.delete'])`.

## Error Handling

- UseCase throws a domain exception (e.g. `ResourceNotFoundException`,
  `BusinessRuleException`, `InvalidParametersException`).
- Admin Controller catches it when it should be displayed inline and
  re-renders the view with an error message, OR `back()->withErrors(...)`.
- HTTP 403 from `web.permission:admin` is handled globally — do NOT swallow it.

## Expected Output (STRICT)

Return ONLY:

- Route entry in `routes/web.php`
- Admin Controller (`App\Http\Controllers\Web\Admin\{{CONTROLLER}}`)
- FormRequest (if applicable)
- Service
- UseCase(s)
- Repository / Interface (only if new query needed)
- Blade view at `resources/views/admin/{{VIEW_FILE_PATH}}.blade.php`

Rules:

- All files must be complete.
- No explanations outside code.
- No `@include`, no Blade components other than the existing `<x-navbar />` /
  `<x-footer />` (and those are storefront-only — do NOT use them here).
- Code must be production-ready.

## Do NOT

- Skip layers (Controller → Repository is forbidden).
- Put business logic in Blade, Controller, or Service.
- Use JWT or `api.permission` — this is the **web** area.
- Return JSON from a Web Controller.
- Create reusable Blade components (only `navbar` and `footer` are allowed,
  and those are storefront-only).
- Add the admin permission middleware to login routes.
- Load Bootstrap or Tailwind on the page.
