# Architecture

## Pattern

Controller → Service → UseCase → Interface / Repository

---

## Flow Rules (CRITICAL)

- Controller MUST call Service
- Service MUST call UseCase
- UseCase MAY call multiple Interfaces
- UseCase MUST depend on Interfaces
- Interfaces MUST be implemented by Repositories
- Interface MUST BE BINDED to a repository by a provider
- Repository MUST use Eloquent Models

DO NOT:
- Skip layers
- Call UseCase directly from Controller
- Put business logic outside UseCase

---

## Responsibilities

### Controller

- Receives FormRequest
- MUST NOT contain business logic
- Calls Service only
- Returns JSON (HATEOAS format)

---

### Service

- Orchestrates UseCases By Dependency Injection
- MUST NOT contain business rules
- MUST NOT access Repository directly

---

### UseCase

- Contains ALL business rules
- Orchestrates Interfaces / Repository
- Can call multiple Interfaces / Repository
- MUST return structured data or return exception if an error

---

### Repository

- Handles database access using model (Eloquent)
- MUST NOT contain business rules
- MUST NOT contain validation logic
- Returns models or collections

---

## Validation

- MUST FOLLOW llm\rules\validation.md

---

## Error Handling

- UseCase MUST throw domain exceptions
- Exceptions MUST be mapped globally to HTTP responses

---

## Output Rules

- MUST follow llm\context\api-patterns.md response pattern

---

## Goal

Ensure strict separation of concerns, consistency, and predictability across the codebase.