<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

require __DIR__ . "/../includes/db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'];
$name = $data['name'] ?? '';
$icon = $data['icon'] ?? 'bi-tag';
$color = $data['color'] ?? 'primary';
$type = $data['type'] ?? 'expense';

// Validate type
if (!in_array($type, ['income', 'expense'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid category type']);
    exit;
}

if (!$name) {
    echo json_encode(['success' => false, 'error' => 'Category name is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO categories (user_id, name, icon, color, type) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $name, $icon, $color, $type]);
    
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
