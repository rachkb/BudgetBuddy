<?php
session_start();
header('Content-Type: application/json');
require __DIR__ . "/../backend/includes/db.php";

// Simulate a logged-in user for testing
$_SESSION['user_id'] = 1;

echo "Testing transaction functionality...\n";

// Test adding a simple transaction
$testData = [
    'type' => 'expense',
    'amount' => 50.00,
    'description' => 'Test Coffee Shop',
    'category_id' => 1,
    'date' => '2024-01-15',
    'notes' => 'Test transaction',
    'is_recurring' => false
];

$ch = curl_init('http://localhost:8000/transactions/add');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Add transaction response: $response\n";
echo "HTTP Code: $httpCode\n";

// Test listing transactions
$ch = curl_init('http://localhost:8000/transactions/list');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "List transactions response: $response\n";
echo "HTTP Code: $httpCode\n";

echo "Test completed.\n";
?>
