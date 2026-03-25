<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

require __DIR__ . "/../includes/db.php";

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? null;
$categoryId = $data['id'] ?? null;

if (!$userId) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

if (!$categoryId) {
    echo json_encode(['success' => false, 'error' => 'Category ID is required']);
    exit;
}

$type = $data['type'] ?? 'expense';
$name = trim($data['name'] ?? '');
$icon = $data['icon'] ?? 'bi-tag';
$color = $data['color'] ?? 'primary';
$budgetLimit = (!empty($data['budget_limit']) && is_numeric($data['budget_limit'])) ? $data['budget_limit'] : null;

if (!$name) {
    echo json_encode(['success' => false, 'error' => 'Category name is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE categories SET type = ?, name = ?, icon = ?, color = ?, budget_limit = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$type, $name, $icon, $color, $budgetLimit, $categoryId, $userId]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
