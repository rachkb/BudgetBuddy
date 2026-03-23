<?php
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require __DIR__ . "/../includes/db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$userId = $_SESSION['user_id'];

// Get query parameters
$type = $_GET['type'] ?? '';
$category_id = $_GET['category_id'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$limit = $_GET['limit'] ?? '';

try {
    $sql = "SELECT t.*, c.name as category_name, c.icon as category_icon, c.color as category_color 
            FROM transactions t 
            LEFT JOIN categories c ON t.category_id = c.id 
            WHERE t.user_id = ?";
    $params = [$userId];

    // Add filters
    if ($type && in_array($type, ['income', 'expense'])) {
        $sql .= " AND t.type = ?";
        $params[] = $type;
    }

    if ($category_id && is_numeric($category_id)) {
        $sql .= " AND t.category_id = ?";
        $params[] = $category_id;
    }

    if ($start_date) {
        $sql .= " AND t.transaction_date >= ?";
        $params[] = $start_date;
    }

    if ($end_date) {
        $sql .= " AND t.transaction_date <= ?";
        $params[] = $end_date;
    }

    $sql .= " ORDER BY t.transaction_date DESC, t.created_at DESC";

    if ($limit && is_numeric($limit)) {
        $sql .= " LIMIT ?";
        $params[] = (int)$limit;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format amount and date for frontend
    foreach ($transactions as &$transaction) {
        $transaction['amount'] = (float)$transaction['amount'];
        $transaction['date'] = date('Y-m-d', strtotime($transaction['transaction_date']));
    }

    echo json_encode($transactions);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
