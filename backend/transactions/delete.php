<?php
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require __DIR__ . "/../includes/db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'not_logged_in']);
    exit;
}

$transaction_id = $_GET['id'] ?? '';

if (!$transaction_id || !is_numeric($transaction_id)) {
    echo json_encode(['success' => false, 'error' => 'invalid_transaction_id']);
    exit;
}

try {
    // First verify the transaction belongs to the user
    $stmt = $pdo->prepare("SELECT id FROM transactions WHERE id = ? AND user_id = ?");
    $stmt->execute([$transaction_id, $_SESSION['user_id']]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        echo json_encode(['success' => false, 'error' => 'transaction_not_found']);
        exit;
    }

    // Delete the transaction
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
    $stmt->execute([$transaction_id, $_SESSION['user_id']]);

    echo json_encode(['success' => true, 'message' => 'Transaction deleted successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
