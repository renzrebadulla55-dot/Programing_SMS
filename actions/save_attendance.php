<?php
require_once '../includes/session.php';
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $attendance = $_POST['attendance'];
    $subject_id = $_POST['subject_id'];
    $attendance_date = $_POST['attendance_date'];
    
    try {
        $conn->beginTransaction();
        
        $stmt = $conn->prepare("INSERT INTO attendance (student_id, subject_id, attendance_date, status) VALUES (?, ?, ?, ?) 
                                ON DUPLICATE KEY UPDATE status = ?");
                                
        foreach ($attendance as $student_id => $status) {
            $stmt->execute([$student_id, $subject_id, $attendance_date, $status, $status]);
        }
        
        $conn->commit();
        
        // Fetch subject name for log
        $sub = $conn->prepare("SELECT subject_name FROM subjects WHERE id = ?");
        $sub->execute([$subject_id]);
        $sub_name = $sub->fetchColumn();
        
        log_system_action($conn, "Saved attendance for $sub_name on $attendance_date (" . count($attendance) . " students)", $_SESSION['username']);
        
        header("Location: ../pages/dashboard.php?attendance_saved=1");
    } catch(PDOException $e) {
        $conn->rollBack();
        die("Error saving attendance: " . $e->getMessage());
    }
}
?>