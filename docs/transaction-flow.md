# Transaction Flow Documentation

## Overview

This document details the complete flow of how transactions work in the BudgetBuddy application, including transaction aging and the add transaction process.

## Transaction Aging System

### How Transaction Age is Calculated

The application categorizes transactions based on their age relative to the current date:

#### Age Categories
- **Today**: Transactions created on the current date
- **Yesterday**: Transactions created on the previous day
- **This Week**: Transactions created within the current week (Sunday to Saturday)
- **This Month**: Transactions created within the current month
- **Older**: Transactions created before the current month

#### Implementation Details

The aging logic is typically implemented in the frontend components, specifically in:

1. **Dashboard Component**: Displays transaction summaries by age
2. **Transactions Page**: Shows filtered views by age category
3. **Date Comparison Logic**: Uses JavaScript's `Date` object to compare transaction dates

```javascript
// Example age calculation logic
const getTransactionAge = (transactionDate) => {
  const today = new Date();
  const transDate = new Date(transactionDate);
  const diffTime = Math.abs(today - transDate);
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  if (diffDays === 0) return 'today';
  if (diffDays === 1) return 'yesterday';
  if (diffDays <= 7) return 'thisWeek';
  if (diffDays <= 30) return 'thisMonth';
  return 'older';
};
```

## Add Transaction Flow

### Frontend Flow

#### 1. User Interface
- **Location**: `AddTransactionModal.vue` component
- **Trigger**: User clicks "Add Transaction" button on Dashboard or Transactions page
- **Modal Display**: Shows form with transaction fields

#### 2. Form Fields
- **Transaction Type**: Income or Expense (required)
- **Amount**: Monetary value (required)
- **Description**: Transaction description (required)
- **Category**: Category selection (required, with option to create new category)
- **Date**: Transaction date (defaults to today)
- **Recurring**: Optional recurring transaction settings
  - Frequency: Daily, Weekly, Bi-weekly, Monthly, Quarterly, Yearly
  - Interval: Number of periods between recurrences

#### 3. Category Creation Flow
When user clicks "New Category" in transaction form:
1. **Category Modal Opens**: `CategoryModal.vue` displays
2. **Type Selection**: User selects Income/Expense
3. **Category Details**: Name, Icon, Color, Budget Limit (expense only)
4. **Backend Save**: Category saved via `/backend/categories/add.php`
5. **Form Update**: New category appears in transaction category dropdown

#### 4. Form Validation
- Required field validation
- Amount must be positive number
- Category must be selected
- Date must be valid

#### 5. API Submission
- **Endpoint**: `/backend/transactions/add.php`
- **Method**: POST
- **Payload**: JSON with transaction data
- **Headers**: `Content-Type: application/json`

```javascript
const transactionData = {
  type: 'income|expense',
  amount: 1500.00,
  description: 'Monthly Salary',
  category_id: 1,
  date: '2026-03-24',
  recurring_frequency: 'monthly', // optional
  recurring_interval: 1, // optional
  user_id: 1 // from localStorage/session
};
```

### Backend Flow

#### 1. Transaction Addition (`/backend/transactions/add.php`)
- **Authentication**: Check user session or user_id from request
- **Validation**: Server-side validation of required fields
- **Database Insert**: Add transaction to `transactions` table
- **Response**: Return success/error with transaction data

#### 2. Database Schema
```sql
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT NOT NULL,
    category_id INT NOT NULL,
    date DATE NOT NULL,
    recurring_frequency ENUM('daily', 'weekly', 'biweekly', 'monthly', 'quarterly', 'yearly') NULL,
    recurring_interval INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

#### 3. Category Management (`/backend/categories/add.php`)
- **Authentication**: Verify user ownership
- **Validation**: Check category name uniqueness per user
- **Type Validation**: Ensure category type is 'income' or 'expense'
- **Database Insert**: Add category with user association
- **Budget Handling**: Set budget_limit to NULL for income categories

### Data Flow Summary

```
User Input → Frontend Validation → API Call → Backend Validation → Database Insert → Response Update
     ↓                                    ↓                        ↓
AddTransactionModal                /backend/transactions/add.php    transactions table
     ↓                                    ↓                        ↓
Category Creation (if needed)      /backend/categories/add.php    categories table
     ↓
Form Reset & UI Update
```

## Error Handling

### Frontend Errors
- **Validation Errors**: Display inline field errors
- **Network Errors**: Show "Connection failed" message
- **API Errors**: Display server error messages

### Backend Errors
- **Authentication**: Return 401/403 for unauthorized access
- **Validation**: Return 400 with specific error messages
- **Database**: Return 500 for database errors
- **Category Conflicts**: Return error for duplicate category names

## State Management

### Frontend State
- **Transaction Form**: Managed in `useTransactions.js` composable
- **Category List**: Cached in `useCategories.js` composable
- **User Session**: Stored in localStorage

### Backend State
- **Database**: Persistent storage in MySQL
- **Sessions**: PHP session management
- **User Context**: User ID from session or request

## Performance Considerations

1. **Caching**: Categories cached locally to reduce API calls
2. **Batch Operations**: Multiple transactions processed in single requests
3. **Lazy Loading**: Transaction lists loaded on demand
4. **Optimistic Updates**: UI updates immediately, rollback on error

## Security Considerations

1. **Input Sanitization**: All inputs sanitized before database insertion
2. **User Authorization**: Users can only access their own transactions
3. **CSRF Protection**: Tokens for form submissions
4. **SQL Injection Prevention**: Prepared statements used throughout

## Future Enhancements

1. **Transaction Templates**: Save frequently used transaction patterns
2. **Bulk Import**: CSV/Excel file import capabilities
3. **Advanced Filtering**: More sophisticated date and category filtering
4. **Transaction Tags**: Additional categorization beyond categories
5. **Receipt Attachments**: File upload support for transaction receipts
