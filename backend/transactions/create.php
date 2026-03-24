<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

require __DIR__ . "/../includes/db.php";

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? null;

if (!$userId) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$type = $data['type'] ?? '';
$amount = $data['amount'] ?? '';
$description = $data['description'] ?? '';
$categoryId = $data['category_id'] ?? null;
$transactionDate = $data['transaction_date'] ?? date('Y-m-d');
$notes = $data['notes'] ?? '';

if (!$type || !$amount || !$description) {
    echo json_encode(['success' => false, 'error' => 'Type, amount, and description are required']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description, category_id, transaction_date, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $type, $amount, $description, $categoryId ?: null, $transactionDate, $notes]);
    
    echo json_encode([
        'success' => true,
        'id' => $pdo->lastInsertId()
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
