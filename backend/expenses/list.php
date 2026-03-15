<?php
session_start();
header('Content-Type: application/json');
require "../includes/db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$userId = $_SESSION['user_id'];

// Make sure your expenses table exists: id, user_id, category, amount, description, created_at
$stmt = $pdo->prepare("SELECT * FROM expenses WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($expenses);