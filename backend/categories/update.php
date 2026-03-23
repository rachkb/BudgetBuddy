<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

require __DIR__ . "/../includes/db.php";

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? '';

if (!$userId) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}
$categoryId = $data['id'] ?? '';
$name = $data['name'] ?? '';
$icon = $data['icon'] ?? '';
$color = $data['color'] ?? '';
$budgetLimit = $data['budget_limit'] ?? null;

if (!$categoryId || !$name) {
    echo json_encode(['success' => false, 'error' => 'Category ID and name are required']);
    exit;
}

try {
    // Verify ownership
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ? AND user_id = ?");
    $stmt->execute([$categoryId, $userId]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Category not found or access denied']);
        exit;
    }
    
    $stmt = $pdo->prepare("UPDATE categories SET name = ?, icon = ?, color = ?, budget_limit = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$name, $icon, $color, $budgetLimit, $categoryId, $userId]);
    
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$categoryId]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'category' => $category]);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(['success' => false, 'error' => 'Category name already exists']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
