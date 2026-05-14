<?php
session_start();
require_once '../config/database.php';

if (isset($_SESSION['username'])) {
    log_system_action($conn, "Securely logged out", $_SESSION['username']);
}

session_destroy();
header("Location: ../pages/login.php?logout=success");
exit();
?>