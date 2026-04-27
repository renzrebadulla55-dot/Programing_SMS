<?php
/**
 * STUDENT REGISTRATION BRAIN
 * This file receives data from pages/add_student.php and saves it to MySQL.
 */

// 1. Link to the database connection
// We use ../ to go out of 'actions' and into 'config'
require_once '../config/database.php';

// 2. Check if the user actually clicked the "Save" button
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 3. Collect the data from the form (name attributes in the HTML)
    $s_no    = $_POST['student_no'];
    $fname   = $_POST['first_name'];
    $lname   = $_POST['last_name'];
    $section = $_POST['section'];

    try {
        // 4. Prepare the SQL Statement (Prevents SQL Injection)
        $sql = "INSERT INTO students (student_no, first_name, last_name, section) 
                VALUES (:s_no, :fname, :lname, :section)";
        
        $stmt = $conn->prepare($sql);
        
        // 5. Execute and save the data
        $stmt->execute([
            ':s_no'    => $s_no,
            ':fname'   => $fname,
            ':lname'   => $lname,
            ':section' => $section
        ]);

        // 6. If successful, send them back to the add student page with a success message
        header("Location: ../pages/add_student.php?success=1");
        exit();

    } catch(PDOException $e) {
        // 7. If there is an error (like a duplicate Student Number)
        // Redirect back with an error code
        header("Location: ../pages/add_student.php?error=" . urlencode($e->getMessage()));
        exit();
    }

} else {
    // If someone tries to access this file directly without clicking 'Save', send them home
    header("Location: ../index.php");
    exit();
}