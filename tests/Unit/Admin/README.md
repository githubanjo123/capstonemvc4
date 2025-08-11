# AdminController Unit Tests

This directory contains comprehensive unit tests for the `AdminController` class.

## Test Coverage

The test suite covers all public and private methods of the `AdminController`:

### Public Methods Tested:
- `dashboard()` - Admin dashboard display with user data
- `logout()` - Logout functionality with confirmation
- `addUser()` - Adding new users
- `addStudent()` - Adding new students
- `editUser()` - Editing existing users
- `editStudent()` - Editing existing students
- `deleteUser()` - Deleting users
- `deleteStudent()` - Deleting students

### Private Methods Tested:
- `showLogoutConfirmation()` - Logout confirmation page
- `getYearSections()` - Year-section grouping logic
- `showSuccess()` - Success message display
- `showError()` - Error message display
- `redirectToDashboard()` - Dashboard redirection

## Test Scenarios

### Dashboard Tests:
- ✅ Displays admin dashboard with current user data
- ✅ Fetches and displays students and faculty lists
- ✅ Generates year-section statistics correctly

### User Management Tests:
- ✅ Successfully adds users with valid data
- ✅ Handles user creation failures gracefully
- ✅ Rejects invalid request methods (GET instead of POST)
- ✅ Successfully edits existing users
- ✅ Handles missing user ID scenarios
- ✅ Successfully deletes users
- ✅ Validates request methods for all operations

### Authentication Tests:
- ✅ Handles logout with confirmation
- ✅ Shows logout confirmation page
- ✅ Processes logout requests correctly

### Error Handling Tests:
- ✅ Returns proper JSON responses for success/error
- ✅ Handles missing required fields
- ✅ Validates request methods
- ✅ Sets appropriate session messages

## Running the Tests

When PHP and PHPUnit are available, run the tests using:

```bash
# Run all AdminController tests
vendor/bin/phpunit tests/Unit/Admin/AdminControllerTest.php

# Run with verbose output
vendor/bin/phpunit --verbose tests/Unit/Admin/AdminControllerTest.php

# Run with coverage report
vendor/bin/phpunit --coverage-html coverage tests/Unit/Admin/AdminControllerTest.php
```

## Test Dependencies

The tests use:
- **PHPUnit** for the testing framework
- **Mock objects** for `AuthService`, `UserService`, and `View` dependencies
- **Reflection** to access private methods and inject mocks
- **Output buffering** to capture and test output
- **Superglobal manipulation** to simulate HTTP requests

## Mock Strategy

- **AuthService**: Mocked to avoid actual authentication during tests
- **UserService**: Mocked to return controlled test data
- **View**: Mocked to verify correct template calls
- **HTTP Superglobals**: Manipulated to simulate different request scenarios

## Notes

- Functions like `header()` and `exit()` are difficult to test in unit tests
- These would be better tested in integration tests
- Session handling is properly mocked and cleaned between tests
- All tests use descriptive names following the "it_should_" pattern

## Test Data

The tests use realistic but controlled test data:
- Sample user records with proper structure
- Year-level and section combinations
- Success/failure response patterns
- Various HTTP request scenarios