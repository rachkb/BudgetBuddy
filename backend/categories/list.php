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

try {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate spending for each category
    foreach ($categories as &$category) {
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total_spent 
                              FROM expenses 
                              WHERE user_id = ? AND category = ?");
        $stmt->execute([$userId, $category['name']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $category['spent'] = (float)$result['total_spent'];
    }
    
    echo json_encode(['success' => true, 'categories' => $categories]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
