<?php
require_once '../includes/session.php';
require_once '../config/database.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../pages/dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            header("Location: ../pages/users.php?error=" . urlencode("Username already exists."));
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $role]);
        log_system_action($conn, "Created new user: $username ($role)", $_SESSION['username']);
        
        header("Location: ../pages/users.php?success=" . urlencode("User created successfully."));
        exit();
    } elseif ($action === 'edit') {
        $user_id = $_POST['user_id'];
        $role = $_POST['role'];
        
        // Cannot change own role this way easily to prevent lockout
        if ($user_id == $_SESSION['user_id']) {
            header("Location: ../pages/users.php?error=" . urlencode("You cannot change your own role."));
            exit();
        }

        $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $uname = $stmt->fetchColumn();

        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$role, $user_id]);
        log_system_action($conn, "Changed role of $uname to $role", $_SESSION['username']);

        header("Location: ../pages/users.php?success=" . urlencode("User role updated."));
        exit();
    }
}
?>
