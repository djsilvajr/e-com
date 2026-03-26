# Architecture

Pattern:
Controller → Service → UseCase → Repository

## Responsibilities

### Controller
- Receives FormRequest
- Calls Service
- Returns JSON (HATEOAS)

### Service
- Delegates to UseCase

### UseCase
- Applies business rules
- Orchestrates repositories

### Repository
- Database access (Eloquent)
