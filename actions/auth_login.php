<?php
session_start();
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = isset($user['role']) ? $user['role'] : 'admin';
            $_SESSION['just_logged_in'] = true;
            
            // Log the login action
            log_system_action($conn, "Logged in to the system", $user['username']);
            
            header("Location: ../pages/dashboard.php");
            exit();
        } else {
            header("Location: ../pages/login.php?error=1");
            exit();
        }
    } catch(PDOException $e) {
        die("Login failed: " . $e->getMessage());
    }
}
?>