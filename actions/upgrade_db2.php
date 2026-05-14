<?php
require_once '../config/database.php';

try {
    $conn->exec("ALTER TABLE students ADD COLUMN enrollment_status VARCHAR(20) DEFAULT 'Regular' AFTER section");
    echo "Database upgraded successfully!";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Columns already exist.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
