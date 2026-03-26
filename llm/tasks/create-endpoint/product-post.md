# Task: Create Product Endpoint

## Endpoint
POST /v1/product

## Controller
ProductController@createProduct

---

## Request
CreateProductRequest

## Validation
Use rules defined in /ai/rules/validation.md

Rules:
- product_type_id: required | integer | min:1
- name: required | string | max:255
- sku: required | string | max:255 | unique
- description: required | string
- short_description: required | string | max:255
- brand: string | max:255
- model: string | max:255
- attributes: array
- avg_weight: numeric
- avg_dimensions: required | array
- avg_dimensions.width: required | numeric
- avg_dimensions.height: required | numeric
- avg_dimensions.length: required | numeric
- avg_dimensions.unit: required | string
- total_stock: integer
- min_stock: integer
- meta_title: string | max:255
- meta_description: string
- meta_keywords: array
- active: required | boolean
- is_featured: required | boolean
- is_new: required | boolean
- has_variants: required | boolean
- available_at: required | date

Constraint:
- Do NOT infer validation rules
- Use ONLY the rules defined above

---

## Business Rules
None

Constraint:
- Do NOT create new rule classes
- Keep logic inside UseCase

---

## Flow
1. Validate ProductType exists
2. Validate SKU uniqueness
3. Validate ProductType is not deleted
4. Validate available_at is today or future
5. Create product using repository
6. Return id, name, active

---

## Architecture Constraints
- Follow /ai/context/architecture.md
- Controller must call Service
- Service must call UseCase
- UseCase must not access framework directly
- Repository handles database (Eloquent)

---

## Response
Use HATEOAS pattern from /ai/context/api-patterns.md

---

## Reference
GET /v1/product/{id}

---

## Expected Output
- Full implementation
- All created/modified files
- Proper namespaces and imports
- Production-ready code (no pseudo-code)
