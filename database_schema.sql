-- Create Database
CREATE DATABASE IF NOT EXISTS student_monitoring_db;
USE student_monitoring_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students Table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_no VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    section VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Attendance Table
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    status ENUM('Present', 'Absent') NOT NULL,
    attendance_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- This UNIQUE constraint is required for ON DUPLICATE KEY UPDATE to work in save_attendance.php
    UNIQUE KEY unique_attendance (student_id, attendance_date),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Insert a default admin account (Password is 'admin123' using BCRYPT hash)
INSERT INTO users (username, password) 
VALUES ('admin', '$2y$10$e.w2O.HwL2B4u0y00aImeeeV/pAXYhFfA8Yj61wZ5O1QO0rX3A1v6') 
ON DUPLICATE KEY UPDATE username=username;
