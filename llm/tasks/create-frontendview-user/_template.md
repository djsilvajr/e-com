# Task: Create Storefront (User) Frontend View

## Context (READ FIRST — MANDATORY)

Before writing a single line of code, the LLM MUST read and follow:

- `llm/context/frontend-patterns.md` — flow, layers, Blade rules, UI library, auth, **`web.permission` middleware**
- `llm/context/architecture.md` — layered architecture (Controller → Service → UseCase → Repository)
- `llm/context/api-patterns.md` — only relevant for cross-checking; this task does NOT touch the API
- `llm/rules/validation.md` — FormRequest validation rules

## Project DIR

- `laravel`

## Area (STRICT)

This task creates a page inside the **Storefront area** (`/*` — the e-commerce
the end customer browses).

- Layout: `resources/views/layouts/storefront.blade.php`
- Controller namespace: `App\Http\Controllers\Web\Storefront`
- View folder: `resources/views/storefront/`
- Route prefix: none (root `/`)
- Route name prefix: free (e.g. `home`, `product.show`, `cart.index`, …) — do NOT use the `admin.` prefix
- Auth guard: `web` (session-based, NOT JWT)
- Reusable components ALLOWED here (and ONLY here): `<x-navbar />` and `<x-footer />`

## View

{{VIEW_NAME}}            <!-- e.g. "product.show" -->
{{VIEW_FILE}}            <!-- e.g. "resources/views/storefront/product/show.blade.php" -->
{{PAGE_TITLE}}           <!-- e.g. "Detalhe do Produto" -->

## Route(s)

{{METHOD}} /{{URI}}                  <!-- e.g. GET /produtos/{slug} -->
Route name: {{ROUTE_NAME}}           <!-- e.g. product.show -->

Rules (CRITICAL):

- Register the route(s) in `routes/web.php`, **outside** the `Route::prefix('admin')` group.
- Choose the right middleware stack:
  - **Public page** (anyone can see it — home, product listing, product detail,
    contact, etc.): use the local `$webPublic` array already defined in
    `routes/web.php`. This gives you session + CSRF + error sharing **without**
    forcing authentication.
  - **Logged-in customer page** (cart, checkout, my orders, profile, etc.):
    use `web.stack` (it includes `web.auth:web`, which redirects guests to the
    login page).
  - **Specific role / permission** (e.g. only "user" role, or a custom
    permission): stack `web.permission:{name}` after `web.stack`.
- Do NOT use `web.permission:admin` on the storefront. Admin gating is
  exclusive to the `/admin` area.
- Do NOT use `api.permission` — this is the web area.

## Controller

{{CONTROLLER}}           <!-- e.g. StorefrontProductController -->

Rules:

- Namespace: `App\Http\Controllers\Web\Storefront`.
- Receives `FormRequest` or `Request`.
- Calls a **Service** (never a Repository, never a UseCase directly).
- Returns a Blade view OR a `redirect()` (PRG pattern). **MUST NOT return JSON.**
- No business logic, no Eloquent calls.

## Request (FormRequest)

{{REQUEST_CLASS}}        <!-- e.g. AddToCartRequest -->

Required for any POST / PUT / PATCH / DELETE action. Follow `llm/rules/validation.md`.

Rules (fill in as needed):

- field_a: required | string | max:255
- field_b: required | int | exists:table,column

## Service

{{SERVICE_CLASS}}        <!-- e.g. StorefrontProductService -->

Rules:

- Located in `App\Services\`.
- Orchestrates UseCases through dependency injection.
- No business logic.
- No Repository access.

## UseCase(s)

{{USECASE_CLASSES}}      <!-- e.g. App\Services\Product\GetPublicProductBySlug -->

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

1. Route `routes/web.php` (`$webPublic` for public pages, `web.stack` for logged-in customers)
2. Storefront Controller (`App\Http\Controllers\Web\Storefront`)
3. FormRequest validation (when applicable)
4. Service
5. UseCase (business rules)
6. Repository (DB access)
7. Controller returns `view('storefront.{{VIEW_NAME}}', [...])` or `redirect()->route('...')`

Rules:

- Must implement all steps in order.
- Must NOT skip layers (no Controller → Repository, no Controller → UseCase).
- Must NOT put business logic in Controller, Service, or Blade.

## Blade View Rules (STRICT — see frontend-patterns.md)

- `@extends('layouts.storefront')`.
- File lives directly under `resources/views/storefront/` (subfolders OK for
  organization, e.g. `storefront/product/show.blade.php`).
- Inside the storefront, use `<x-navbar />` and `<x-footer />` exactly once
  (top / bottom). These are the **only** two Blade components in the whole
  project. Do NOT create new ones.
- Style with **Semantic UI** classes ONLY (`ui container`, `ui menu`, `ui form`,
  `ui button`, `ui grid`, `ui segment`, `ui message`, `ui card`, `ui items`,
  …). No Bootstrap, no Tailwind.
- Every form MUST include `@csrf`.
- Display validation errors via `$errors->any()` / `$errors->first('field')`.
- Do NOT call the API from the Blade (no JWT, no fetch to `/api/*`). All data
  comes server-side from the Controller via the Service.
- For "is the user logged in?" checks in the navbar/menu, use
  `Auth::guard('web')->check()` and `Auth::guard('web')->user()`.

## Authentication / Authorization

- Public storefront pages do NOT require authentication.
- Pages that require a logged-in customer go behind `web.stack`.
- If a storefront page is reserved for a specific role/permission, add
  `web.permission:{name}` AFTER `web.stack`. Example:
  `->middleware(['web.stack', 'web.permission:user'])`.
- `web.permission:admin` is reserved for `/admin` — do NOT use it on the
  storefront.

## Error Handling

- UseCase throws a domain exception (e.g. `ResourceNotFoundException`,
  `BusinessRuleException`, `InvalidParametersException`).
- Storefront Controller catches it when it should be displayed inline and
  re-renders the view with an error message, OR `back()->withErrors(...)`.
- 404s (product not found, page not found) should rely on Laravel's default
  not-found handling unless the task specifies otherwise.

## Expected Output (STRICT)

Return ONLY:

- Route entry in `routes/web.php`
- Storefront Controller (`App\Http\Controllers\Web\Storefront\{{CONTROLLER}}`)
- FormRequest (if applicable)
- Service
- UseCase(s)
- Repository / Interface (only if new query needed)
- Blade view at `resources/views/storefront/{{VIEW_FILE_PATH}}.blade.php`

Rules:

- All files must be complete.
- No explanations outside code.
- No new Blade components (only the existing `<x-navbar />` / `<x-footer />`).
- Code must be production-ready.

## Do NOT

- Skip layers (Controller → Repository is forbidden).
- Put business logic in Blade, Controller, or Service.
- Use JWT or `api.permission` — this is the **web** area.
- Return JSON from a Web Controller.
- Create new Blade / Livewire / Vue / React components.
- Use `web.permission:admin` on a storefront route.
- Load Bootstrap or Tailwind on the page.
