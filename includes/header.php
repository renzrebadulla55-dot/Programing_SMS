<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Student Monitoring</title>
    <link rel="stylesheet" href="../assets/css/global.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/dashboard.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="app-layout">
        
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon" style="background: transparent; box-shadow: none;">
                    <img src="../assets/sms_logo.png" alt="SMS Logo" style="width: 40px; height: 40px; object-fit: contain;">
                </div>
                <div style="display: flex; flex-direction: column;">
                    <span class="sidebar-brand-name">SMS</span>
                    <span class="sidebar-brand-sub">Management System</span>
                </div>
            </div>
            
            <div class="sidebar-label">Main Menu</div>
            <nav class="sidebar-nav">
                
                <a href="dashboard.php" class="sidebar-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <span class="sidebar-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    </span>
                    Dashboard
                </a>

                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'professor'): ?>
                <a href="schedule_overview.php" class="sidebar-link <?php echo ($current_page == 'schedule_overview.php') ? 'active' : ''; ?>">
                    <span class="sidebar-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </span>
                    Schedule Overview
                </a>
                <?php endif; ?>

                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="add_student.php" class="sidebar-link <?php echo ($current_page == 'add_student.php' || $current_page == 'edit_student.php') ? 'active' : ''; ?>">
                    <span class="sidebar-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                    </span>
                    Registration
                </a>
                <?php endif; ?>

                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'professor'): ?>
                <a href="mark_attendance.php" class="sidebar-link <?php echo ($current_page == 'mark_attendance.php') ? 'active' : ''; ?>">
                    <span class="sidebar-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/></svg>
                    </span>
                    Mark Attendance
                </a>

                <a href="view_attendance.php" class="sidebar-link <?php echo ($current_page == 'view_attendance.php') ? 'active' : ''; ?>">
                    <span class="sidebar-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg>
                    </span>
                    Attendance Reports
                </a>
                <?php endif; ?>

                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="users.php" class="sidebar-link <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">
                    <span class="sidebar-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </span>
                    Users
                </a>

                <a href="system_logs.php" class="sidebar-link <?php echo ($current_page == 'system_logs.php') ? 'active' : ''; ?>">
                    <span class="sidebar-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </span>
                    System Logs
                </a>
                <?php endif; ?>
            </nav>
            
            <div class="sidebar-bottom">
                <a href="#" onclick="showLogoutModal(event)" class="sidebar-link" style="color: #EF4444; margin-top: auto; border: 1px solid #FCA5A5; background: #FEF2F2; justify-content: center; font-weight: 700;">
                    <span class="sidebar-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                    </span>
                    Secure Logout
                </a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-wrapper">

        <!-- LOGOUT CONFIRMATION MODAL -->
        <div id="logoutModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 10000; justify-content: center; align-items: center;">
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(10px);" onclick="hideLogoutModal()"></div>
            <div style="position: relative; background: white; padding: 40px; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.15); width: 100%; max-width: 400px; text-align: center; border: 1px solid #E2E8F0; z-index: 10001; animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;">
                <div style="width: 70px; height: 70px; background: #FEF2F2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #EF4444;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                </div>
                <h3 style="font-size: 1.5rem; color: var(--text-main); margin-bottom: 10px; font-family: 'Sora', sans-serif;">Confirm Logout</h3>
                <p style="color: var(--text-muted); margin-bottom: 30px; font-size: 0.95rem;">Are you sure you want to securely log out of your account?</p>
                <div style="display: flex; gap: 15px;">
                    <button onclick="hideLogoutModal()" class="btn" style="flex: 1; background: #F3F4F6; color: var(--text-main); font-weight: 600;">Cancel</button>
                    <a href="../actions/logout.php" class="btn" style="flex: 1; background: #EF4444; color: white; font-weight: 600; text-decoration: none;">Yes, Log Out</a>
                </div>
            </div>
        </div>
        <style>
            @keyframes popIn {
                from { opacity: 0; transform: scale(0.9) translateY(20px); }
                to { opacity: 1; transform: scale(1) translateY(0); }
            }
        </style>
        <script>
            function showLogoutModal(e) {
                if(e) e.preventDefault();
                document.getElementById('logoutModal').style.display = 'flex';
            }
            function hideLogoutModal() {
                document.getElementById('logoutModal').style.display = 'none';
            }

            // Prevent Back Button and turn it into Logout Prompt
            history.pushState(null, null, location.href);
            window.addEventListener('popstate', function () {
                history.pushState(null, null, location.href);
                showLogoutModal();
            });

            // Prevent bfcache (Back-Forward Cache) bypass
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    window.location.reload();
                }
            });
        </script>
