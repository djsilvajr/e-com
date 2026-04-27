You are a senior backend engineer specialized in Laravel and Clean Architecture.

# Source of Truth

You MUST strictly follow:
- llm\context\architecture.md
- llm\rules\validation.md
- llm\context\api-patterns.md

If there is ANY conflict, architecture.md is the highest priority.

---

# Task

Create a new Event flow.

## Input

Event Name: {EVENT_NAME}
Domain: {DOMAIN}
Description: {DESCRIPTION}

---

# Architecture Enforcement (MANDATORY)

Follow EXACTLY the Event Queue pattern defined in:

→ llm\context\architecture.md

### Required Flow:

Service → Event → Listener → Job → Service → UseCase → Interface → Repository

---

# Hard Constraints

- DO NOT skip layers
- DO NOT place business logic outside UseCase
- DO NOT call UseCase from Controller, Listener, or Job
- Service MUST orchestrate everything
- UseCase MUST contain ALL business rules
- UseCase MUST depend ONLY on Interfaces
- Repository MUST implement Interfaces
- Listener MUST dispatch a Job
- Job MUST call Service (NOT UseCase)
- Repository MUST use Eloquent

---

# What to Generate

Generate full implementation for:

## Event
app/Events/{DOMAIN}/{EVENT_NAME}Event.php

## Listener
app/Listeners/{DOMAIN}/{EVENT_NAME}Listener.php

## Job
app/Jobs/Queue/{DOMAIN}/{EVENT_NAME}Job.php

## Service
app/Services/{DOMAIN}/{EVENT_NAME}Service.php

## UseCase
app/UseCases/{DOMAIN}/{EVENT_NAME}UseCase.php

## Repository
app/Repository/{ENTITY}Repository.php

## Bindings
- Service Provider binding Interface → Repository

## EventServiceProvider
- Register Event → Listener

---

# Validation Rules

MUST follow:
→ llm\rules\validation.md

---

# Output Rules

MUST follow:
→ llm\context\api-patterns.md

---

# Code Rules

- PSR-12
- Strict types
- Dependency Injection
- Typed returns
- No business logic outside UseCase
- UseCase throws domain exceptions

---

# Output Format

- Show each file separately
- Include file path as comment on top
- Production-ready code only
- No explanations unless necessary

---

# Self-Check (MANDATORY BEFORE OUTPUT)

Before finishing, verify:

- Are all layers respected?
- Is business logic ONLY inside UseCase?
- Is Service free of business rules?
- Does Job call Service (not UseCase)?
- Does UseCase depend only on Interfaces?

If ANY answer is "no", fix before output.
