<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

require "../includes/db.php";

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? '';

if (!$userId) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}
$name = $data['name'] ?? '';
$icon = $data['icon'] ?? 'bi-tag';
$color = $data['color'] ?? 'primary';
$budgetLimit = $data['budget_limit'] ?? null;
$type = $data['type'] ?? 'expense';

if (!$name) {
    echo json_encode(['success' => false, 'error' => 'Category name is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO categories (user_id, name, icon, color, budget_limit) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $name, $icon, $color, $budgetLimit]);
    
    $categoryId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$categoryId]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'category' => $category]);
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(['success' => false, 'error' => 'Category already exists']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
