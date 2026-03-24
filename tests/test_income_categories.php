<?php
session_start();
header('Content-Type: application/json');
require __DIR__ . "/../backend/includes/db.php";

// Simulate a logged-in user by setting session directly
$_SESSION['user_id'] = 1;

echo "Testing income category functionality...\n";

// Test creating an income category
$incomeCategoryData = [
    'name' => 'Salary',
    'icon' => 'bi-briefcase',
    'color' => 'success',
    'type' => 'income'
];

try {
    $stmt = $pdo->prepare("INSERT INTO categories (user_id, name, icon, color, type) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $incomeCategoryData['name'], $incomeCategoryData['icon'], $incomeCategoryData['color'], $incomeCategoryData['type']]);
    
    $categoryId = $pdo->lastInsertId();
    echo "Income category 'Salary' created with ID: $categoryId\n";
} catch (PDOException $e) {
    echo "Error creating income category: " . $e->getMessage() . "\n";
}

// Test creating another income category
$freelanceCategoryData = [
    'name' => 'Freelance',
    'icon' => 'bi-laptop',
    'color' => 'info',
    'type' => 'income'
];

try {
    $stmt = $pdo->prepare("INSERT INTO categories (user_id, name, icon, color, type) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $freelanceCategoryData['name'], $freelanceCategoryData['icon'], $freelanceCategoryData['color'], $freelanceCategoryData['type']]);
    
    $categoryId = $pdo->lastInsertId();
    echo "Income category 'Freelance' created with ID: $categoryId\n";
} catch (PDOException $e) {
    echo "Error creating freelance category: " . $e->getMessage() . "\n";
}

// Test creating an expense category
$expenseCategoryData = [
    'name' => 'Groceries',
    'icon' => 'bi-cup-hot',
    'color' => 'warning',
    'type' => 'expense'
];

try {
    $stmt = $pdo->prepare("INSERT INTO categories (user_id, name, icon, color, type) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $expenseCategoryData['name'], $expenseCategoryData['icon'], $expenseCategoryData['color'], $expenseCategoryData['type']]);
    
    $categoryId = $pdo->lastInsertId();
    echo "Expense category 'Groceries' created with ID: $categoryId\n";
} catch (PDOException $e) {
    echo "Error creating expense category: " . $e->getMessage() . "\n";
}

// Test listing income categories
$stmt = $pdo->prepare("SELECT * FROM categories WHERE user_id = ? AND type = 'income' ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$incomeCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "\nIncome categories found:\n";
foreach ($incomeCategories as $category) {
    echo "- ID: {$category['id']}, Name: {$category['name']}, Icon: {$category['icon']}, Color: {$category['color']}\n";
}

// Test listing expense categories
$stmt = $pdo->prepare("SELECT * FROM categories WHERE user_id = ? AND type = 'expense' ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$expenseCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "\nExpense categories found:\n";
foreach ($expenseCategories as $category) {
    echo "- ID: {$category['id']}, Name: {$category['name']}, Icon: {$category['icon']}, Color: {$category['color']}\n";
}

// Test adding an income transaction with income category (use null for category_id first)
$incomeTransactionData = [
    'type' => 'income',
    'amount' => 3000.00,
    'description' => 'Monthly Salary',
    'category_id' => null, // Use null for now
    'date' => '2024-01-01',
    'notes' => 'January salary',
    'is_recurring' => true,
    'recurring_frequency' => 'monthly',
    'recurring_interval' => 1,
    'recurring_end_date' => '2024-12-31'
];

try {
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description, category_id, transaction_date, notes, is_recurring, recurring_frequency, recurring_interval, recurring_end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $incomeTransactionData['type'], $incomeTransactionData['amount'], $incomeTransactionData['description'], $incomeTransactionData['category_id'], $incomeTransactionData['date'], $incomeTransactionData['notes'], $incomeTransactionData['is_recurring'], $incomeTransactionData['recurring_frequency'], $incomeTransactionData['recurring_interval'], $incomeTransactionData['recurring_end_date']]);
    
    $transactionId = $pdo->lastInsertId();
    echo "\nIncome transaction added successfully with ID: $transactionId\n";
} catch (PDOException $e) {
    echo "Error adding income transaction: " . $e->getMessage() . "\n";
}

echo "\nIncome category functionality test completed!\n";
?>
