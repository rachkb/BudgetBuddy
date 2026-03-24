# Reports Page Testing Guide

## Test Checklist

### ✅ Backend API Endpoints

#### 1. Summary Endpoint (`/backend/reports/summary.php`)
**Purpose**: Calculate total income, expenses, and net savings for a date range

**Test Cases**:
- [ ] Returns correct totals for "This Month"
- [ ] Returns correct totals for "Last Month"
- [ ] Returns correct totals for "Last 3 Months"
- [ ] Returns correct totals for "This Year"
- [ ] Filters by category correctly
- [ ] Handles "All Categories" filter
- [ ] Returns 0 values when no transactions exist
- [ ] Requires authentication (user_id)

**Expected Response**:
```json
{
  "success": true,
  "data": {
    "total_income": 5000.00,
    "total_expenses": 3200.00,
    "net_savings": 1800.00,
    "transaction_count": 15,
    "period": {
      "start_date": "2026-03-01",
      "end_date": "2026-03-31"
    }
  }
}
```

#### 2. Monthly Trend Endpoint (`/backend/reports/monthly-trend.php`)
**Purpose**: Provide monthly income/expense data for chart visualization

**Test Cases**:
- [ ] Returns last 6 months of data by default
- [ ] Groups transactions by month correctly
- [ ] Calculates monthly income totals
- [ ] Calculates monthly expense totals
- [ ] Formats month labels correctly (e.g., "Mar 2026")
- [ ] Returns empty arrays when no data exists

**Expected Response**:
```json
{
  "success": true,
  "data": {
    "labels": ["Oct 2025", "Nov 2025", "Dec 2025", "Jan 2026", "Feb 2026", "Mar 2026"],
    "income": [4500, 5000, 4800, 5200, 5000, 5500],
    "expenses": [3200, 3500, 4000, 3100, 2900, 3200]
  }
}
```

#### 3. Export Endpoint (`/backend/reports/export.php`)
**Purpose**: Generate CSV file with transaction data

**Test Cases**:
- [ ] Downloads CSV file with correct filename
- [ ] Includes all required columns (Date, Description, Category, Amount, Type, Notes)
- [ ] Respects date range filter
- [ ] Respects category filter
- [ ] Formats amounts correctly (2 decimal places)
- [ ] Handles transactions without categories (shows "Uncategorized")
- [ ] UTF-8 encoding works (Excel compatible)

**Expected CSV Format**:
```csv
Date,Description,Category,Amount,Type,Notes
2026-03-24,Salary,Income,5000.00,Income,Monthly salary
2026-03-23,Groceries,Food,150.00,Expense,Weekly shopping
```

### ✅ Frontend Components

#### 1. ReportsPage.vue Component
**Test Cases**:
- [ ] Page loads without white screen
- [ ] No JavaScript errors in console
- [ ] MainLayout renders correctly
- [ ] All UI elements are visible

#### 2. Date Range Filter
**Test Cases**:
- [ ] Dropdown shows all options (This Month, Last Month, Last 3 Months, This Year)
- [ ] Selecting "This Month" loads current month data
- [ ] Selecting "Last Month" loads previous month data
- [ ] Selecting "Last 3 Months" loads 3-month data
- [ ] Selecting "This Year" loads year-to-date data
- [ ] Changing filter triggers data reload
- [ ] Loading spinner shows during data fetch

#### 3. Category Filter
**Test Cases**:
- [ ] Dropdown shows "All Categories" option
- [ ] Dropdown populates with user's categories
- [ ] Selecting specific category filters data
- [ ] Selecting "All Categories" shows all data
- [ ] Changing filter triggers data reload
- [ ] Categories load on page mount

#### 4. Generate Report Button
**Test Cases**:
- [ ] Button is clickable
- [ ] Button shows loading state when fetching data
- [ ] Button icon changes to spinner during load
- [ ] Button text changes to "Loading..." during load
- [ ] Button is disabled during load
- [ ] Clicking button refreshes report data

#### 5. Summary Cards
**Test Cases**:
- [ ] Total Income card displays correct amount
- [ ] Total Expenses card displays correct amount
- [ ] Net Savings card displays correct amount
- [ ] Amounts are formatted as PHP currency (₱)
- [ ] Net Savings shows blue color when positive
- [ ] Net Savings shows orange color when negative
- [ ] Transaction count displays correctly
- [ ] Loading state shows spinner before data loads
- [ ] Cards update when filters change

#### 6. Monthly Trend Chart
**Test Cases**:
- [ ] Chart loads without errors
- [ ] Chart displays bar chart type
- [ ] Chart shows last 6 months of data
- [ ] Income bars are green
- [ ] Expense bars are red
- [ ] Chart legend shows "Income" and "Expenses"
- [ ] Chart is responsive (adjusts to screen size)
- [ ] Tooltips show formatted currency on hover
- [ ] Y-axis shows currency format (₱)
- [ ] X-axis shows month labels
- [ ] Empty state shows when no data exists
- [ ] Loading state shows spinner while fetching

#### 7. Export Report Button
**Test Cases**:
- [ ] Button is clickable
- [ ] Clicking button downloads CSV file
- [ ] Filename includes date range
- [ ] CSV contains current filtered data
- [ ] CSV opens correctly in Excel
- [ ] CSV shows proper formatting
- [ ] Error message displays if export fails

### ✅ useReports Composable

#### 1. State Management
**Test Cases**:
- [ ] reportData initializes with zeros
- [ ] monthlyTrendData initializes with empty arrays
- [ ] isLoading starts as false
- [ ] isLoadingChart starts as false
- [ ] error starts as empty string
- [ ] dateRange defaults to "thisMonth"
- [ ] selectedCategory defaults to "all"

#### 2. Date Range Calculation
**Test Cases**:
- [ ] calculateDateRange("thisMonth") returns correct dates
- [ ] calculateDateRange("lastMonth") returns correct dates
- [ ] calculateDateRange("last3Months") returns correct dates
- [ ] calculateDateRange("thisYear") returns correct dates
- [ ] Dates are formatted as YYYY-MM-DD
- [ ] Handles month boundaries correctly
- [ ] Handles year boundaries correctly

#### 3. Data Loading
**Test Cases**:
- [ ] loadReport() fetches summary data
- [ ] loadReport() sets isLoading to true during fetch
- [ ] loadReport() sets isLoading to false after fetch
- [ ] loadReport() updates reportData on success
- [ ] loadReport() sets error message on failure
- [ ] loadMonthlyTrend() fetches chart data
- [ ] loadMonthlyTrend() updates monthlyTrendData
- [ ] exportReport() triggers CSV download

#### 4. Currency Formatting
**Test Cases**:
- [ ] formatCurrency formats as PHP currency
- [ ] formattedIncome computed property works
- [ ] formattedExpenses computed property works
- [ ] formattedSavings computed property works

## Manual Testing Steps

### Step 1: Navigate to Reports Page
1. Open browser to `http://localhost:5174`
2. Login with test user credentials
3. Click "Reports" in navigation menu
4. **Expected**: Reports page loads without errors

### Step 2: Test Default View
1. Observe the page on initial load
2. **Expected**: 
   - "This Month" is selected in date range
   - "All Categories" is selected
   - Summary cards show current month totals
   - Chart displays last 6 months
   - Transaction count is visible

### Step 3: Test Date Range Filters
1. Select "Last Month" from dropdown
2. **Expected**: Data updates to show last month's totals
3. Select "Last 3 Months"
4. **Expected**: Data updates to show 3-month totals
5. Select "This Year"
6. **Expected**: Data updates to show year-to-date totals

### Step 4: Test Category Filters
1. Select a specific category from dropdown
2. **Expected**: Data filters to show only that category
3. Select "All Categories"
4. **Expected**: Data shows all categories again

### Step 5: Test Generate Report Button
1. Click "Generate Report" button
2. **Expected**: 
   - Button shows loading state
   - Data refreshes
   - Button returns to normal state

### Step 6: Test Chart Interaction
1. Hover over chart bars
2. **Expected**: Tooltips show with formatted amounts
3. Resize browser window
4. **Expected**: Chart adjusts responsively

### Step 7: Test Export Functionality
1. Click "Export Report" button
2. **Expected**: 
   - CSV file downloads
   - Filename includes date range
   - File opens in Excel correctly
   - Data matches current filters

### Step 8: Test Error Handling
1. Stop Apache server
2. Try to load report
3. **Expected**: Error message displays
4. Restart Apache
5. Click "Generate Report"
6. **Expected**: Data loads successfully

## Known Issues & Limitations

### Current Limitations (As Per Simple Plan)
- ❌ No PDF export (CSV only)
- ❌ No custom date range picker (predefined periods only)
- ❌ No budget comparisons (actual numbers only)
- ❌ No percentage changes vs previous period
- ❌ No multiple chart types (bar chart only)

### Potential Issues to Watch For
1. **Chart.js Loading**: Lazy-loaded to prevent white screen
2. **Session Management**: Backend requires user_id from localStorage
3. **Date Calculations**: Timezone handling may vary
4. **Empty Data**: Charts should show empty state gracefully
5. **Large Datasets**: Performance may degrade with many transactions

## Success Criteria

✅ All backend endpoints return correct data
✅ All filters work and update data correctly
✅ Summary cards display accurate totals
✅ Chart visualizes data properly
✅ Export downloads valid CSV file
✅ No JavaScript errors in console
✅ No white screen issues
✅ Loading states display correctly
✅ Error messages show when appropriate
✅ Responsive design works on mobile

## Test Results

**Date Tested**: _____________
**Tested By**: _____________
**Browser**: _____________
**Result**: ☐ Pass ☐ Fail

**Notes**:
_____________________________________________
_____________________________________________
_____________________________________________
