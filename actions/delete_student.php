<?php
require_once '../includes/session.php';
require_once '../config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $conn->beginTransaction();

        // 1. Delete student's attendance first
        $stmt1 = $conn->prepare("DELETE FROM attendance WHERE student_id = ?");
        $stmt1->execute([$id]);

        // 2. Delete the student
        $stmt2 = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt2->execute([$id]);

        $conn->commit();
        header("Location: ../pages/add_student.php?deleted=1");
    } catch (Exception $e) {
        $conn->rollBack();
        die("Error deleting record: " . $e->getMessage());
    }
}