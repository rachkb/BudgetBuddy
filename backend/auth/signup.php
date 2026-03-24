<?php

header("Access-Control-Allow-Origin: *");  // allow any domain
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

header('Content-Type: application/json');
require "../includes/db.php";

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$name || !$email || !$password) {
    echo json_encode(['success' => false, 'error' => 'missing_fields']);
    exit;
}

// Check if email exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'exists']);
    exit;
}

// Insert new user
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (name, email, password, updated_at) VALUES (?, ?, ?, NOW())");
try {
    $stmt->execute([$name, $email, $hashedPassword]);
    $newUserId = $pdo->lastInsertId();
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $newUserId,
            'name' => $name,
            'email' => $email
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}