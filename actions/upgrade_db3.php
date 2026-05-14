<?php
require_once '../config/database.php';

try {
    // Create subjects table
    $conn->exec("CREATE TABLE IF NOT EXISTS subjects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subject_name VARCHAR(100) NOT NULL,
        year_level VARCHAR(20) NOT NULL
    )");

    // Insert default subjects if none exist
    $sub_check = $conn->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
    if ($sub_check == 0) {
        $conn->exec("INSERT INTO subjects (subject_name, year_level) VALUES ('Programming 1', '3')");
        $conn->exec("INSERT INTO subjects (subject_name, year_level) VALUES ('Math', '3')");
    }

    // Add subject_id to attendance
    try {
        $conn->exec("ALTER TABLE attendance ADD COLUMN subject_id INT DEFAULT NULL AFTER student_id");
    } catch(Exception $e) {}

    // Drop old unique constraint if exists, we will recreate it.
    // In MySQL, constraint dropping requires knowing the name. If it's `unique_attendance`:
    try {
        $conn->exec("ALTER TABLE attendance DROP INDEX unique_attendance");
    } catch(Exception $e) {}
    
    // Create new unique constraint
    try {
        $conn->exec("ALTER TABLE attendance ADD UNIQUE INDEX unique_attendance (student_id, subject_id, attendance_date)");
    } catch(Exception $e) {}

    // Insert 12 sample students for 3rd Year (Programming 1 & Math)
    // Section A
    $conn->exec("INSERT INTO students (student_no, first_name, last_name, section, gender, year_level, enrollment_status) VALUES ('2023-1001', 'John', 'Doe', 'A', 'Male', '3', 'Regular')");
    $conn->exec("INSERT INTO students (student_no, first_name, last_name, section, gender, year_level, enrollment_status) VALUES ('2023-1002', 'Jane', 'Smith', 'A', 'Female', '3', 'Regular')");
    $conn->exec("INSERT INTO students (student_no, first_name, last_name, section, gender, year_level, enrollment_status) VALUES ('2023-1003', 'Mark', 'Johnson', 'A', 'Male', '3', 'Regular')");
    // Section B
    $conn->exec("INSERT INTO students (student_no, first_name, last_name, section, gender, year_level, enrollment_status) VALUES ('2023-1004', 'Emily', 'Davis', 'B', 'Female', '3', 'Regular')");
    $conn->exec("INSERT INTO students (student_no, first_name, last_name, section, gender, year_level, enrollment_status) VALUES ('2023-1005', 'Chris', 'Brown', 'B', 'Male', '3', 'Regular')");
    $conn->exec("INSERT INTO students (student_no, first_name, last_name, section, gender, year_level, enrollment_status) VALUES ('2023-1006', 'Sarah', 'Wilson', 'B', 'Female', '3', 'Regular')");

    echo "Database upgraded for subjects and new unique key successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
