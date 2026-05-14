<?php
require_once '../config/database.php';

try {
    // 1. Add role to users table
    try {
        $conn->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'admin' AFTER username");
    } catch(Exception $e) {}

    // Update admin user
    $conn->exec("UPDATE users SET role = 'admin' WHERE username = 'admin'");

    // 2. Insert Professor User
    $prof_username = 'professor-0001';
    $prof_pass = password_hash('professor123', PASSWORD_DEFAULT);
    
    // Check if exists
    $check_prof = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check_prof->execute([$prof_username]);
    if ($check_prof->rowCount() == 0) {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'professor')");
        $stmt->execute([$prof_username, $prof_pass]);
    }

    $prof_id = $conn->query("SELECT id FROM users WHERE username = 'professor-0001'")->fetchColumn();

    // 3. Add professor_id to subjects and update existing
    try {
        $conn->exec("ALTER TABLE subjects ADD COLUMN professor_id INT DEFAULT NULL AFTER id");
    } catch(Exception $e) {}
    $conn->exec("UPDATE subjects SET professor_id = $prof_id WHERE subject_name IN ('Programming 1', 'Math')");

    // 4. Create Schedules table
    $conn->exec("CREATE TABLE IF NOT EXISTS schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        professor_id INT NOT NULL,
        subject_name VARCHAR(100) NOT NULL,
        section VARCHAR(10) NOT NULL,
        day_of_week VARCHAR(20) NOT NULL,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL
    )");

    // Insert dummy schedule
    $sch_check = $conn->query("SELECT COUNT(*) FROM schedules")->fetchColumn();
    if ($sch_check == 0) {
        $conn->exec("INSERT INTO schedules (professor_id, subject_name, section, day_of_week, start_time, end_time) VALUES ($prof_id, 'Programming 1', 'A', 'Monday', '09:00:00', '11:00:00')");
        $conn->exec("INSERT INTO schedules (professor_id, subject_name, section, day_of_week, start_time, end_time) VALUES ($prof_id, 'Programming 1', 'B', 'Tuesday', '13:00:00', '15:00:00')");
        $conn->exec("INSERT INTO schedules (professor_id, subject_name, section, day_of_week, start_time, end_time) VALUES ($prof_id, 'Math', 'A', 'Wednesday', '10:00:00', '12:00:00')");
        $conn->exec("INSERT INTO schedules (professor_id, subject_name, section, day_of_week, start_time, end_time) VALUES ($prof_id, 'Math', 'B', 'Thursday', '14:00:00', '16:00:00')");
    }

    echo "Database upgraded for Roles and Schedules successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
