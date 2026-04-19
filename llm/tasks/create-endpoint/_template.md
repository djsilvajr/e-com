# Task: Create Endpoint

##context
Read and follow API patterns in llm/context/api-patterns.md
Read and follow API architecture in llm/context/architecture.md

## Project DIR
- laravel

## Endpoint
{{METHOD}} {{URI}}

## Controller
{{CONTROLLER}}

## Request
{{REQUEST_CLASS}}

## Validation
Use rules defined in /ai/rules/validation.md

Rules:
- field: required | string | max:255

## Business Rules
{{RULES}}

If no rules are provided:
- Do NOT create new rule classes
- Keep logic inside UseCase

## Flow
{{FLOW}}

1. Create Route in routes/api.php
2. Call Controller
3. Validate request
4. Call UseCase
5. UseCase performs X
6. UseCase calls repository Y
7. Return result

Rules:
- Must implement all steps in order
- Must NOT skip or invent steps

## Architecture (CRITICAL)

Follow architecture.md strictly.

Key rules:

- Controller MUST call Service
- Service MUST call UseCase
- UseCase MUST contain ALL business logic
- UseCase MUST NOT return HTTP responses
- Repository MUST NOT contain business logic
- Validation MUST be handled only by FormRequest

DO NOT:
- Skip layers
- Put business logic in Controller or Service
- Access Repository from Controller or Service

## Expected Output (STRICT)

Return ONLY:

- Controller
- Request (FormRequest)
- UseCase
- Service
- Repository (if needed)

Rules:
- All files must be complete
- No explanations
- No comments outside code
- Code must be production-ready
