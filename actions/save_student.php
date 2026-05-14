<?php
require_once '../includes/session.php';
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_no = $_POST['student_no'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $section = $_POST['section'];
    $gender = isset($_POST['gender']) ? $_POST['gender'] : 'Male';
    $middle_initial = isset($_POST['middle_initial']) ? $_POST['middle_initial'] : '';
    $year_level = isset($_POST['year_level']) ? $_POST['year_level'] : '1';
    $enrollment_status = isset($_POST['enrollment_status']) ? $_POST['enrollment_status'] : 'Regular';

    try {
        $stmt = $conn->prepare("INSERT INTO students (student_no, first_name, last_name, section, gender, middle_initial, year_level, enrollment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$student_no, $first_name, $last_name, $section, $gender, $middle_initial, $year_level, $enrollment_status]);
        
        // Log action
        log_system_action($conn, "Registered new student: $last_name, $first_name ($student_no)", $_SESSION['username']);

        header("Location: ../pages/add_student.php?success=1");
    } catch(PDOException $e) {
        header("Location: ../pages/add_student.php?error=" . urlencode($e->getMessage()));
    }
}
?>