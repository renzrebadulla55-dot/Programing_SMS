<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Student Monitoring</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/add_student.css">
    <link rel="stylesheet" href="../assets/css/view_attendance.css">
</head>
<body>
    <div class="app-layout">
        
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div style="background: var(--accent); padding: 5px; border-radius: 8px;">🛡️</div>
                <span>SMS</span>
                <span style="font-size: 0.7rem; font-weight: 400; opacity: 0.7; margin-top: 5px;">Student Monitoring</span>
            </div>
            
            <div class="sidebar-menu">
                <div class="sidebar-menu-title">Main Menu</div>
                <?php $currentPage = basename($_SERVER['SCRIPT_NAME']); ?>
                
                <a href="dashboard.php" class="sidebar-link <?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
                    📊 Dashboard
                </a>
                <a href="add_student.php" class="sidebar-link <?php echo ($currentPage == 'add_student.php') ? 'active' : ''; ?>">
                    👤 Enroll Student
                </a>
                <a href="mark_attendance.php" class="sidebar-link <?php echo ($currentPage == 'mark_attendance.php') ? 'active' : ''; ?>">
                    ✅ Mark Attendance
                </a>
                <a href="view_attendance.php" class="sidebar-link <?php echo ($currentPage == 'view_attendance.php') ? 'active' : ''; ?>">
                    📋 Attendance History
                </a>
            </div>
            
            <div class="sidebar-bottom">
                <a href="../actions/logout.php" class="sidebar-link" style="color: #ef4444;">
                    🚪 Logout
                </a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">