<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<<<<<<< HEAD
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
=======
    <title>Student Monitoring</title>
    <link rel="stylesheet" href="../assets/css/global.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/mark_attendance.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/add_student.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/view_attendance.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        </div>
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">SMS</span>
            <span class="sidebar-brand-sub">Student Monitor</span>
        </div>
    </div>

    <!-- Nav Label -->
    <div class="sidebar-label">Main Menu</div>

    <!-- Nav Links -->
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="sidebar-link <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
            <span class="sidebar-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
            </span>
            <span class="sidebar-text">Dashboard</span>
        </a>

        <a href="add_student.php" class="sidebar-link <?php echo $current_page === 'add_student.php' ? 'active' : ''; ?>">
            <span class="sidebar-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
            </span>
            <span class="sidebar-text">Enroll Student</span>
        </a>

        <a href="mark_attendance.php" class="sidebar-link <?php echo $current_page === 'mark_attendance.php' ? 'active' : ''; ?>">
            <span class="sidebar-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/></svg>
            </span>
            <span class="sidebar-text">Mark Attendance</span>
        </a>

        <a href="view_attendance.php" class="sidebar-link <?php echo $current_page === 'view_attendance.php' ? 'active' : ''; ?>">
            <span class="sidebar-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg>
            </span>
            <span class="sidebar-text">Attendance History</span>
        </a>

    </nav>

    <!-- Spacer -->
    <div class="sidebar-spacer"></div>

    <!-- Logout -->
    <div class="sidebar-bottom">
        <a href="../actions/logout.php" class="sidebar-logout">
            <span class="sidebar-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
            </span>
            <span class="sidebar-text">Logout</span>
        </a>
    </div>

</aside>

<!-- Main Wrapper -->
<div class="main-wrapper">
>>>>>>> 915aea506c89d11fadd177121a29ad1f57cdbecc
