<?php
require_once '../config/database.php';

try {
    // Add columns to students table if they don't exist
    $conn->exec("ALTER TABLE students ADD COLUMN gender VARCHAR(10) DEFAULT 'Male' AFTER last_name");
    $conn->exec("ALTER TABLE students ADD COLUMN middle_initial VARCHAR(5) DEFAULT '' AFTER first_name");
    $conn->exec("ALTER TABLE students ADD COLUMN year_level VARCHAR(20) DEFAULT '1' AFTER middle_initial");
    
    // Create system_logs table
    $conn->exec("CREATE TABLE IF NOT EXISTS system_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        action_desc VARCHAR(255) NOT NULL,
        user_name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    echo "Database upgraded successfully!";
} catch (PDOException $e) {
    // Ignore duplicate column errors
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Columns already exist.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
