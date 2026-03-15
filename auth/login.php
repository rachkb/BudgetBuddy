<?php
session_start();
require "../includes/db.php"; // make sure the path is correct

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user by email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            header("Location: ../pages/dashboard.php");
            exit;
        } else {
            // Password incorrect
            header("Location: ../pages/login.html?error=invalid");
            exit;
        }
    } else {
        // Email not found
        header("Location: ../pages/login.html?error=notfound");
        exit;
    }
}
?>