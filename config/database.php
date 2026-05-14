<?php
$host = "127.0.0.1;port=3307";
$db_name = "student_monitoring_db";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

function log_system_action($conn, $action, $user) {
    try {
        $stmt = $conn->prepare("INSERT INTO system_logs (action_desc, user_name) VALUES (?, ?)");
        $stmt->execute([$action, $user]);
    } catch(PDOException $e) {}
}
?>