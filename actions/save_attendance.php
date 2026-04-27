<?php
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['attendance'])) {
    $date = date('Y-m-d');

    try {
        // This SQL will INSERT if new, or UPDATE if the student already has a record today
        $sql = "INSERT INTO attendance (student_id, status, attendance_date) 
                VALUES (:s_id, :status, :date)
                ON DUPLICATE KEY UPDATE status = VALUES(status)";
        
        $stmt = $conn->prepare($sql);

        foreach ($_POST['attendance'] as $student_id => $status) {
            $stmt->execute([
                ':s_id'   => $student_id,
                ':status' => $status,
                ':date'   => $date
            ]);
        }

        header("Location: ../pages/view_attendance.php?success=1");
        exit();

    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}