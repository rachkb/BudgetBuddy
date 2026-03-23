<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
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
$categoryId = $data['id'] ?? '';

if (!$categoryId) {
    echo json_encode(['success' => false, 'error' => 'Category ID is required']);
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
    
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ? AND user_id = ?");
    $stmt->execute([$categoryId, $userId]);
    
    echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
