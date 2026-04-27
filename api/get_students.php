<?php
header("Content-Type: application/json");
require_once '../config/database.php';

try {
    // Just get the basic info for the list
    $stmt = $conn->query("SELECT id, student_no, first_name, last_name, section FROM students ORDER BY last_name ASC");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "count" => count($students),
        "data" => $students
    ]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}