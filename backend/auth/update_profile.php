<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

require __DIR__ . "/../includes/db.php";

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['id'] ?? '';
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$phone = $data['phone'] ?? '';
$currency = $data['currency'] ?? 'PHP';

if (!$userId || !$name || !$email) {
    echo json_encode(['success' => false, 'error' => 'User ID, name, and email are required']);
    exit;
}

// Validate currency
$allowedCurrencies = ['PHP', 'USD', 'EUR', 'JPY'];
if (!in_array($currency, $allowedCurrencies)) {
    echo json_encode(['success' => false, 'error' => 'Invalid currency']);
    exit;
}

try {
    // Update user profile
    $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, currency = ?, updated_at = NOW() WHERE id = ? AND email = ?");
    $stmt->execute([$name, $phone, $currency, $userId, $email]);
    
    if ($stmt->rowCount() > 0) {
        // Get updated user data
        $stmt = $pdo->prepare("SELECT id, name, email, phone, currency, created_at FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'error' => 'User not found or no changes made']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
