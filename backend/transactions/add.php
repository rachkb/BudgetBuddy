<?php
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require __DIR__ . "/../includes/db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'not_logged_in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
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

if (!$type || !$amount || !$description || !$date) {
    echo json_encode(['success' => false, 'error' => 'missing_required_fields']);
    exit;
}

// Validate transaction type
if (!in_array($type, ['income', 'expense'])) {
    echo json_encode(['success' => false, 'error' => 'invalid_transaction_type']);
    exit;
}

// Validate amount
if (!is_numeric($amount) || $amount <= 0) {
    echo json_encode(['success' => false, 'error' => 'invalid_amount']);
    exit;
}

// Validate date
if (!DateTime::createFromFormat('Y-m-d', $date)) {
    echo json_encode(['success' => false, 'error' => 'invalid_date']);
    exit;
}

// Validate recurring transaction fields
if ($is_recurring) {
    if (!$recurring_frequency || !in_array($recurring_frequency, ['daily', 'weekly', 'biweekly', 'monthly', 'quarterly', 'yearly'])) {
        echo json_encode(['success' => false, 'error' => 'invalid_recurring_frequency']);
        exit;
    }
    
    if (!is_numeric($recurring_interval) || $recurring_interval < 1) {
        echo json_encode(['success' => false, 'error' => 'invalid_recurring_interval']);
        exit;
    }
    
    if ($recurring_end_date && !DateTime::createFromFormat('Y-m-d', $recurring_end_date)) {
        echo json_encode(['success' => false, 'error' => 'invalid_recurring_end_date']);
        exit;
    }
    
    if ($recurring_end_date && new DateTime($recurring_end_date) <= new DateTime($date)) {
        echo json_encode(['success' => false, 'error' => 'recurring_end_date_must_be_future']);
        exit;
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description, category_id, transaction_date, notes, is_recurring, recurring_frequency, recurring_interval, recurring_end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $type, $amount, $description, $category_id, $date, $notes, $is_recurring, $recurring_frequency, $recurring_interval, $recurring_end_date]);
    
    $transaction_id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true, 
        'transaction_id' => $transaction_id,
        'message' => 'Transaction added successfully'
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
