# BudgetBuddy Tests

This folder contains test files for the BudgetBuddy application. These files are excluded from version control via .gitignore.

## Available Test Files

### Database Setup
- `setup_database.php` - Creates the initial transactions table
- `update_recurring.php` - Adds recurring transaction fields to the database
- `add_notes_column.php` - Adds notes column to transactions table
- `check_structure.php` - Displays current database table structure

### Transaction Testing
- `test_transaction.php` - Tests transaction endpoints via HTTP requests
- `test_with_session.php` - Tests transaction functionality with simulated session

## Usage

Run tests using PHP CLI from the project root:

```bash
# Database setup
php tests/setup_database.php
php tests/update_recurring.php

# Check table structure
php tests/check_structure.php

# Test transactions
php tests/test_with_session.php
php tests/test_transaction.php
```

## Notes

- These files are for development and testing purposes only
- They will not be committed to version control
- Make sure the PHP backend server is running on localhost:8000 for HTTP tests
- Database connection settings are in backend/includes/db.php
