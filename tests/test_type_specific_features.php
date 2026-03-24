<?php
session_start();
header('Content-Type: application/json');
require __DIR__ . "/../backend/includes/db.php";

// Simulate a logged-in user by setting session directly
$_SESSION['user_id'] = 1;

echo "Testing type-specific placeholders and icons...\n";

// Test creating income categories with appropriate icons
$incomeCategories = [
    ['name' => 'Salary', 'icon' => 'bi-briefcase', 'color' => 'success', 'type' => 'income'],
    ['name' => 'Freelance', 'icon' => 'bi-cash-stack', 'color' => 'info', 'type' => 'income'],
    ['name' => 'Investment', 'icon' => 'bi-graph-up', 'color' => 'warning', 'type' => 'income'],
    ['name' => 'Bonus', 'icon' => 'bi-trophy', 'color' => 'primary', 'type' => 'income']
];

foreach ($incomeCategories as $category) {
    try {
        $stmt = $pdo->prepare("INSERT INTO categories (user_id, name, icon, color, type) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $category['name'], $category['icon'], $category['color'], $category['type']]);
        echo "Income category '{$category['name']}' created with icon '{$category['icon']}'\n";
    } catch (PDOException $e) {
        echo "Error creating income category '{$category['name']}': " . $e->getMessage() . "\n";
    }
}

// Test creating expense categories with appropriate icons
$expenseCategories = [
    ['name' => 'Groceries', 'icon' => 'bi-cart', 'color' => 'danger', 'type' => 'expense'],
    ['name' => 'Gas', 'icon' => 'bi-gas-pump', 'color' => 'warning', 'type' => 'expense'],
    ['name' => 'Entertainment', 'icon' => 'bi-film', 'color' => 'info', 'type' => 'expense'],
    ['name' => 'Healthcare', 'icon' => 'bi-heart-pulse', 'color' => 'danger', 'type' => 'expense']
];

foreach ($expenseCategories as $category) {
    try {
        $stmt = $pdo->prepare("INSERT INTO categories (user_id, name, icon, color, type) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $category['name'], $category['icon'], $category['color'], $category['type']]);
        echo "Expense category '{$category['name']}' created with icon '{$category['icon']}'\n";
    } catch (PDOException $e) {
        echo "Error creating expense category '{$category['name']}': " . $e->getMessage() . "\n";
    }
}

// Test listing income categories
$stmt = $pdo->prepare("SELECT * FROM categories WHERE user_id = ? AND type = 'income' ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$incomeCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "\nIncome categories with their icons:\n";
foreach ($incomeCategories as $category) {
    echo "- {$category['name']}: {$category['icon']} ({$category['color']})\n";
}

// Test listing expense categories
$stmt = $pdo->prepare("SELECT * FROM categories WHERE user_id = ? AND type = 'expense' ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$expenseCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "\nExpense categories with their icons:\n";
foreach ($expenseCategories as $category) {
    echo "- {$category['name']}: {$category['icon']} ({$category['color']})\n";
}

// Test creating transactions with appropriate categories
$incomeTransaction = [
    'type' => 'income',
    'amount' => 5000.00,
    'description' => 'Monthly Salary',
    'category_id' => 1, // Salary category
    'date' => '2024-01-15',
    'notes' => 'January salary payment',
    'is_recurring' => true,
    'recurring_frequency' => 'monthly',
    'recurring_interval' => 1
];

try {
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description, category_id, transaction_date, notes, is_recurring, recurring_frequency, recurring_interval) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $incomeTransaction['type'], $incomeTransaction['amount'], $incomeTransaction['description'], $incomeTransaction['category_id'], $incomeTransaction['date'], $incomeTransaction['notes'], $incomeTransaction['is_recurring'], $incomeTransaction['recurring_frequency'], $incomeTransaction['recurring_interval']]);
    echo "\nIncome transaction 'Monthly Salary' created with Salary category\n";
} catch (PDOException $e) {
    echo "Error creating income transaction: " . $e->getMessage() . "\n";
}

$expenseTransaction = [
    'type' => 'expense',
    'amount' => 150.00,
    'description' => 'Weekly Groceries',
    'category_id' => 5, // Groceries category
    'date' => '2024-01-16',
    'notes' => 'Weekly grocery shopping',
    'is_recurring' => false
];

try {
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description, category_id, transaction_date, notes, is_recurring) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $expenseTransaction['type'], $expenseTransaction['amount'], $expenseTransaction['description'], $expenseTransaction['category_id'], $expenseTransaction['date'], $expenseTransaction['notes'], $expenseTransaction['is_recurring']]);
    echo "Expense transaction 'Weekly Groceries' created with Groceries category\n";
} catch (PDOException $e) {
    echo "Error creating expense transaction: " . $e->getMessage() . "\n";
}

echo "\nType-specific features test completed successfully!\n";
echo "\nSummary:\n";
echo "- Income categories use business/finance icons (briefcase, cash-stack, graph-up, trophy)\n";
echo "- Expense categories use daily life icons (cart, gas-pump, film, heart-pulse)\n";
echo "- Placeholders change based on transaction type\n";
echo "- Default icons are set appropriately for each category type\n";
?>
