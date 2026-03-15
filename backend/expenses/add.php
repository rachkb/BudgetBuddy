<?php
session_start();
header('Content-Type: application/json');
require "../includes/db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'not_logged_in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$category = $data['category'] ?? '';
$amount = $data['amount'] ?? '';
$description = $data['description'] ?? '';

if (!$category || !$amount) {
    echo json_encode(['success' => false, 'error' => 'missing_fields']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO expenses (user_id, category, amount, description) VALUES (?, ?, ?, ?)");
try {
    $stmt->execute([$_SESSION['user_id'], $category, $amount, $description]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}