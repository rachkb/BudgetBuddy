<?php
session_start();
header('Content-Type: application/json');
require __DIR__ . "/../backend/includes/db.php";

// Simulate a logged-in user by setting session directly
$_SESSION['user_id'] = 1;

echo "Testing transaction functionality with simulated session...\n";

// Test adding a simple transaction
$testData = [
    'type' => 'expense',
    'amount' => 50.00,
    'description' => 'Test Coffee Shop',
    'category_id' => null, // Use null for no category
    'date' => '2024-01-15',
    'notes' => 'Test transaction',
    'is_recurring' => false
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

try {
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description, category_id, transaction_date, notes, is_recurring) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $type, $amount, $description, $category_id ?: null, $date, $notes, $is_recurring]);
    
    $transaction_id = $pdo->lastInsertId();
    echo "Transaction added successfully with ID: $transaction_id\n";
} catch (PDOException $e) {
    echo "Error adding transaction: " . $e->getMessage() . "\n";
}

// Test listing transactions
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($transactions) . " transactions:\n";
foreach ($transactions as $transaction) {
    echo "- ID: {$transaction['id']}, {$transaction['type']}, ₱{$transaction['amount']}, {$transaction['description']}\n";
}

echo "Test completed successfully!\n";
?>
