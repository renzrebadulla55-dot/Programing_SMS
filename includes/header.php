<!DOCTYPE html>
<html>
<head>
    <title>Student Monitoring</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/add_student.css">
    <link rel="stylesheet" href="../assets/css/view_attendance.css">
    <style>
        .logout-link {
            background-color: #ef4444; 
            color: white !important; 
            padding: 5px 15px; 
            border-radius: 5px;
        }
        .logout-link:hover {
            background-color: #dc2626;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div style="font-size: 1.2rem; font-weight: bold; color: var(--primary);">🛡️ Student Monitoring System</div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="add_student.php">Enroll</a>
            <a href="mark_attendance.php">Attendance</a>
            <a href="view_attendance.php">History</a>
            <a href="../actions/logout.php" class="logout-link">Logout</a>
        </div>
    </div>