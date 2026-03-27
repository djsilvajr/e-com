Task: Create Unit Tests for UseCase {UseCaseName}

Context:
Follow strictly the project architecture defined below:
- Only test the UseCase layer
- Do NOT involve Service, Controller, or real Repository
- All dependencies MUST be mocked using Mockery
- UseCase depends ONLY on Interfaces

---

UseCase:
{UseCaseName}

Dependencies (Interfaces used by UseCase):
- {Interface1}
- {Interface2}
- {InterfaceN}

---

Input:
{JSON_OR_ID_INPUT}

---

Rules:

1. Test Structure
- Location: tests/Unit/{UseCaseName}Test.php
- Use PHPUnit
- Use Mockery. MUST USE ONLY ON Interfaces
- Use RefreshDatabase = FALSE (unit test only)

---

2. What to Test

Create tests covering:

### Happy Path
- Valid input
- All interfaces called correctly
- Correct data returned

### Business Rules
- Validate all decision branches inside UseCase
- Test conditionals (if/else)

### Exceptions
- When entity not found
- When business rule fails
- When interface returns invalid data

---

3. Mocking Rules

- Mock ALL interfaces used by UseCase
- NEVER use real repository or database
- Use Mockery::mock(Interface::class)
- NEVER mock domain Rules (pure business logic)

- Validate:
    - method called
    - parameters passed
    - number of calls (once, never, etc)

Example:
$repository->shouldReceive('findById')
    ->once()
    ->with($id)
    ->andReturn($mockedEntity);

---

4. Assertions

- Assert returned data structure
- Assert exceptions thrown
- Assert mocks were called correctly

---

5. Output

Generate COMPLETE test class including:
- setUp()
- mocks
- UseCase instantiation
- all test methods

---

6. Important

- DO NOT test framework behavior
- DO NOT test database
- DO NOT use Service layer
- Focus ONLY on UseCase logic
- Rules are part of domain behavior and MUST NOT be mocked
- Rule behavior must be validated in its own unit tests
- UseCase tests should rely on real Rules but focus on flow, not rule permutations
- Tests act as behavior specification
- If business behavior changes, tests MUST fail
- Test updates are REQUIRED when behavior changes intentionally
- Tests MUST be independent (no shared state between tests)
- Validate side effects (state changes, persistence calls, triggered actions)
- Follow AAA pattern:
    Arrange (setup mocks and data)
    Act (execute UseCase)
    Assert (verify results and interactions)

---

7. Style

- Clean and readable
- Use descriptive method names:
    test_should_return_data_when_valid_input()
    test_should_throw_exception_when_not_found()
    test_should_call_repository_once()

---

Goal:
Ensure 100% coverage of UseCase business logic using isolated unit tests.