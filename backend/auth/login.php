<?php

header("Access-Control-Allow-Origin: *");  // allow any domain
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

session_start();
header('Content-Type: application/json');
require "../includes/db.php";

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    echo json_encode(['success' => true, 'user' => [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email']
    ]]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid email or password']);
}