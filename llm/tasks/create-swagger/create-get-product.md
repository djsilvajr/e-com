# Prompt: Auto-Generate Swagger from Laravel Endpoint

## Project

- Laravel

## Objective
Generate/update Swagger documentation based on the **actual endpoint implementation**, NOT on manual descriptions.

---

## Context

The project already contains an existing Swagger file at:

storage/api-docs/swagger.yaml

You must **update this file** without breaking or removing anything that already exists.

---

## Endpoint to Document

GET v1/product/{id}

Controller:  
ProductController@getProductById

---

## Main Rule (CRITICAL)

⚠️ You must analyze the code to determine:

- Request (fields, types, validations)
- Actual returned response
- Status codes used
- Structure (e.g., data, _links, etc.)
- Possible errors (exceptions, validations, business rules)

DO NOT use manual descriptions as the source of truth  
DO NOT invent fields  
DO NOT assume behavior  

The source of truth is the code

---

## Files You Must Analyze

- Controller
- Service
- UseCase
- Request (FormRequest)
- Repository (bound via interface in a provider)
- Exceptions
- Routes file

---

## What You Must Extract Automatically

### Request
- Fields
- Types
- Required vs optional
- Validation rules (FormRequest)

---

### Response
- Exact returned structure
- Fields present
- Types

---

### Status Codes
Map based on the code:

- 2xx → success
- 4xx → validation / business rule errors
- 5xx → unexpected errors (if applicable)

---

### Errors
Automatically identify:

- ValidationException → 422
- ModelNotFound / domain → 404
- Conflicts (e.g., duplicate SKU) → 409
- Other custom errors

---

## Technical Requirements

- Use OpenAPI 3.0.3
- Only update:
  - paths
  - components.schemas
- Reuse existing schemas whenever possible
- Follow the existing swagger.yaml patterns

---

## Consistency Rules

- DO NOT duplicate schemas
- If a similar schema exists → reuse it
- Follow existing naming conventions
- Preserve file organization

---

## Expected Output

Return ONLY:

1. Valid YAML
2. Only the sections that must be added or modified
3. No explanations
4. No comments
5. No text outside YAML

---

## Constraints

- DO NOT invent data
- DO NOT assume behavior
- DO NOT rewrite the entire file
- DO NOT modify existing endpoints
- DO NOT generate pseudo-code

---

## Source of Truth Priority

Order of trust:

1. Code (source of truth)
2. Explicit typing
3. Validations
4. UseCase flow
5. Project conventions

---

## Final Goal

Generate Swagger documentation that is **100% faithful to the real endpoint behavior**, minimizing divergence between code and documentation. Changes must be written in swagger.yaml
