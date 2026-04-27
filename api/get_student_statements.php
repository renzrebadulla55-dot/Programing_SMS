<?php
header("Content-Type: application/json");
require_once '../config/database.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    echo json_encode(["error" => "No student ID provided"]);
    exit();
}

try {
    // 1. We prepare the real query once
    $stmt = $conn->prepare("SELECT 
        first_name, last_name, student_no,
        (SELECT COUNT(*) FROM attendance WHERE student_id = :id AND status = 'Present') as present_count,
        (SELECT COUNT(*) FROM attendance WHERE student_id = :id AND status = 'Absent') as absent_count
        FROM students WHERE id = :id");
    
    // 2. We execute it with the ID from the URL
    $stmt->execute([':id' => $id]);
    
    // 3. We fetch the result
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        echo json_encode([
            "status" => "success",
            "data" => $data
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Student not found"]);
    }

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}