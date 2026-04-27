<?php
session_start();
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST['username']); 
    $pass = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$user]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($account) {
        // User found, now checking password
        if (password_verify($pass, $account['password'])) {
            $_SESSION['user_id'] = $account['id'];
            $_SESSION['username'] = $account['username'];
            header("Location: ../pages/dashboard.php");
            exit();
        } else {
            // Password wrong - send back to login
            header("Location: ../pages/login.php?error=1");
            exit();
        }
    } else {
        // Username not found - send back to login
        header("Location: ../pages/login.php?error=1");
        exit();
    }
}
?>