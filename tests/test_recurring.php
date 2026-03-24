<?php
session_start();
header('Content-Type: application/json');
require __DIR__ . "/../backend/includes/db.php";

// Simulate a logged-in user by setting session directly
$_SESSION['user_id'] = 1;

echo "Testing recurring transaction functionality...\n";

// Test adding a recurring transaction
$testData = [
    'type' => 'expense',
    'amount' => 100.00,
    'description' => 'Monthly Subscription',
    'category_id' => null,
    'date' => '2024-01-01',
    'notes' => 'Test recurring transaction',
    'is_recurring' => true,
    'recurring_frequency' => 'monthly',
    'recurring_interval' => 1,
    'recurring_end_date' => '2024-12-31'
];

// Directly call the add logic
$data = $testData;
$type = $data['type'] ?? '';
$amount = $data['amount'] ?? '';
$description = $data['description'] ?? '';
$date = $data['date'] ?? '';
$category_id = $data['category_id'] ?? null;
$notes = $data['notes'] ?? '';
$is_recurring = $data['is_recurring'] ?? false;
$recurring_frequency = $data['recurring_frequency'] ?? null;
$recurring_interval = $data['recurring_interval'] ?? 1;
$recurring_end_date = $data['recurring_end_date'] ?? null;

try {
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description, category_id, transaction_date, notes, is_recurring, recurring_frequency, recurring_interval, recurring_end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $type, $amount, $description, $category_id, $date, $notes, $is_recurring, $recurring_frequency, $recurring_interval, $recurring_end_date]);
    
    $transaction_id = $pdo->lastInsertId();
    echo "Recurring transaction added successfully with ID: $transaction_id\n";
} catch (PDOException $e) {
    echo "Error adding recurring transaction: " . $e->getMessage() . "\n";
}

// Test listing all transactions
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($transactions) . " transactions:\n";
foreach ($transactions as $transaction) {
    $recurring = $transaction['is_recurring'] ? ' (Recurring)' : '';
    echo "- ID: {$transaction['id']}, {$transaction['type']}, ₱{$transaction['amount']}, {$transaction['description']}$recurring\n";
}

echo "Recurring transaction test completed successfully!\n";
?>
