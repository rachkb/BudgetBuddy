<?php

header("Access-Control-Allow-Origin: *");  // allow any domain
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$name || !$email || !$password) {
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Please enter a valid email address']);
    exit;
}

// Simple mock validation - in production, use database
$mockUsers = ['test@example.com']; // Mock existing users
if (in_array($email, $mockUsers)) {
    echo json_encode(['success' => false, 'error' => 'Email already exists']);
    exit;
}

// Mock successful registration
$user = [
    'id' => 1,
    'name' => $name,
    'email' => $email
];

echo json_encode(['success' => true, 'user' => $user]);
?>
