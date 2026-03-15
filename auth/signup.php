<?php
require "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['username']; // match form field
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user (include updated_at as NULL or CURRENT_TIMESTAMP)
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, updated_at) VALUES (?, ?, ?, NOW())");

    try {
        $stmt->execute([$name, $email, $hashedPassword]);
        echo "User inserted successfully!";
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}
?>