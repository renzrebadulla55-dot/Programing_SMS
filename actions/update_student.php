<?php
require_once '../includes/session.php';
require_once '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $student_no = $_POST['student_no'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $section = $_POST['section'];
    $gender = isset($_POST['gender']) ? $_POST['gender'] : 'Male';
    $middle_initial = isset($_POST['middle_initial']) ? $_POST['middle_initial'] : '';
    $year_level = isset($_POST['year_level']) ? $_POST['year_level'] : '1';
    $enrollment_status = isset($_POST['enrollment_status']) ? $_POST['enrollment_status'] : 'Regular';

    try {
        $stmt = $conn->prepare("UPDATE students SET first_name=?, last_name=?, section=?, gender=?, middle_initial=?, year_level=?, enrollment_status=? WHERE id=?");
        $stmt->execute([$first_name, $last_name, $section, $gender, $middle_initial, $year_level, $enrollment_status, $id]);
        
        log_system_action($conn, "Updated student record: $last_name, $first_name ($student_no)", $_SESSION['username']);
        
        header("Location: ../pages/add_student.php");
    } catch(PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>