# AdminController Unit Tests

This directory contains comprehensive unit tests for the `AdminController` class, which handles administrative functions in the MVC application.

## Test Coverage

The test suite covers all public and private methods of the `AdminController`:

### Public Methods
- `dashboard()` - Admin dashboard display with user data
- `logout()` - User logout handling
- `addUser()` - User creation (general users)
- `addStudent()` - Student creation
- `editUser()` - User editing (general users)
- `editStudent()` - Student editing
- `deleteUser()` - User deletion (general users)
- `deleteStudent()` - Student deletion

### Private Methods
- `showLogoutConfirmation()` - Logout confirmation page
- `getYearSections()` - Year-section calculation from student data
- `redirectToDashboard()` - Dashboard redirection
- `showSuccess()` - Success message display
- `showError()` - Error message display

## Test Scenarios

### Dashboard Tests
- ✅ Display admin dashboard with user data
- ✅ Generate year sections correctly from student data

### Authentication Tests
- ✅ Handle logout with confirmation
- ✅ Show logout confirmation page

### User Management Tests
- ✅ Add user successfully
- ✅ Handle add user failure
- ✅ Reject add user with invalid request method
- ✅ Edit user successfully
- ✅ Delete user successfully
- ✅ Reject delete user with invalid request method

### Student Management Tests
- ✅ Add student successfully
- ✅ Handle add student failure
- ✅ Edit student successfully
- ✅ Handle edit student without user ID
- ✅ Delete student successfully
- ✅ Handle delete student without user ID

### Utility Method Tests
- ✅ Show success message
- ✅ Show error message
- ✅ Redirect to dashboard (method execution verification)

## How to Run

### Option 1: Using the Test Runner Script
```bash
# From the tests/Unit/Admin directory
./run_tests.sh

# With verbose output
./run_tests.sh -v

# With coverage report
./run_tests.sh -c

# Both verbose and coverage
./run_tests.sh -v -c
```

### Option 2: Using PHPUnit Directly
```bash
# From the project root directory
vendor/bin/phpunit tests/Unit/Admin/AdminControllerTest.php

# With testdox format and verbose output
vendor/bin/phpunit tests/Unit/Admin/AdminControllerTest.php --testdox --verbose

# Using the local PHPUnit config
vendor/bin/phpunit --configuration tests/Unit/Admin/phpunit.xml
```

### Option 3: Using the Local PHPUnit Config
```bash
# From the tests/Unit/Admin directory
vendor/bin/phpunit --configuration phpunit.xml
```

## Dependencies

- **PHPUnit**: Testing framework
- **PHP**: 7.4+ recommended
- **Composer**: For dependency management

## Mock Strategy

The tests use PHPUnit's mocking capabilities to isolate the `AdminController` from its dependencies:

- **AuthService**: Mocked for authentication and user session management
- **UserService**: Mocked for user CRUD operations
- **View**: Mocked for template rendering

## Test Environment Setup

### Output Buffer Management
- Tests properly manage output buffering to capture controller output
- Each test starts with a clean output buffer
- Proper cleanup in tearDown() prevents buffer conflicts

### Superglobal Management
- `$_SESSION`, `$_GET`, `$_POST`, `$_SERVER` are reset for each test
- Tests simulate different HTTP request methods and input data

### Error Handling
- Header warnings are suppressed for unit tests (better tested in integration tests)
- Error reporting is managed per test to prevent interference

## Common Issues and Solutions

### "Headers already sent" Errors
- **Cause**: Output buffering conflicts or premature output
- **Solution**: Tests now properly manage output buffers and suppress header warnings

### Mock Expectation Failures
- **Cause**: Incorrect mock setup or parameter expectations
- **Solution**: Fixed mock expectations to match actual method call order and parameters

### Output Buffer Warnings
- **Cause**: Tests not properly cleaning up output buffers
- **Solution**: Improved buffer management with proper level tracking

## Integration vs Unit Testing

These tests are **unit tests** that focus on:
- ✅ Method behavior in isolation
- ✅ Mock dependency interactions
- ✅ Input validation and error handling
- ✅ Business logic verification

For testing HTTP headers, redirects, and full request/response cycles, use **integration tests** instead.

## Coverage Reports

When running with coverage (`-c` flag), reports are generated in:
- `coverage/` directory (HTML format)
- `coverage.txt` (text format)
- `junit.xml` (JUnit format for CI/CD)
- `testdox.txt` (human-readable test results)

## Best Practices Used

1. **Arrange-Act-Assert**: Clear test structure
2. **Mock Isolation**: Dependencies are properly mocked
3. **Clean State**: Each test starts with fresh superglobals
4. **Reflection**: Private methods are tested using reflection
5. **Output Capture**: Controller output is captured and asserted
6. **Error Suppression**: Appropriate warnings are suppressed for unit tests

## Future Improvements

- Add more edge case testing
- Implement integration tests for header/redirect functionality
- Add performance benchmarks for data processing methods
- Expand coverage to include more complex user scenarios