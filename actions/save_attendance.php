<?php
require_once '../includes/session.php';
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $attendance = $_POST['attendance'];
    $today = date('Y-m-d');
    
    try {
        $conn->beginTransaction();
        
        $stmt = $conn->prepare("INSERT INTO attendance (student_id, attendance_date, status) VALUES (?, ?, ?) 
                                ON DUPLICATE KEY UPDATE status = ?");
                                
        foreach ($attendance as $student_id => $status) {
            $stmt->execute([$student_id, $today, $status, $status]);
        }
        
        $conn->commit();
        
        log_system_action($conn, "Saved attendance records for " . count($attendance) . " students on $today", $_SESSION['username']);
        
        header("Location: ../pages/dashboard.php?attendance_saved=1");
    } catch(PDOException $e) {
        $conn->rollBack();
        die("Error saving attendance: " . $e->getMessage());
    }
}
?>