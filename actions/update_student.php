<?php
require_once '../includes/session.php';
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $s_no = $_POST['student_no'];
    $fn = $_POST['first_name'];
    $ln = $_POST['last_name'];
    $sec = $_POST['section'];

    $sql = "UPDATE students SET student_no = ?, first_name = ?, last_name = ?, section = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt->execute([$s_no, $fn, $ln, $sec, $id])) {
        header("Location: ../pages/add_student.php?updated=1");
    } else {
        echo "Error updating record.";
    }
}