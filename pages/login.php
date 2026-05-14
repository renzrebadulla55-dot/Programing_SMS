<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | StudentMonitoringSystem</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/login.css">
    <style>
        body { margin: 0; overflow: hidden; background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%); }
        .dashboard-blur-background {
            filter: blur(10px);
            opacity: 0.8;
            pointer-events: none;
            user-select: none;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            position: relative;
        }
        .login-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.85); /* Less transparent for readability */
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
        }
    </style>
</head>
<body>

    <!-- Background Dashboard Mockup -->
    <div class="dashboard-blur-background">
        
        <!-- Sidebar Mockup -->
        <aside class="sidebar" style="position: absolute;">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon"></div>
                <div class="sidebar-brand-text">
                    <span class="sidebar-brand-name" style="color: white; font-weight: bold; font-family: 'Sora', sans-serif;">SMS</span>
                    <span class="sidebar-brand-sub" style="color: rgba(255,255,255,0.5); font-size: 10px;">Student Monitor</span>
                </div>
            </div>
            <div class="sidebar-label" style="color: rgba(255,255,255,0.4); padding: 20px 18px 10px; font-size: 10px; text-transform: uppercase;">Main Menu</div>
            <nav class="sidebar-nav" style="padding: 0 10px;">
                <div class="sidebar-link active" style="background: var(--primary); padding: 10px 12px; border-radius: 8px; color: white; margin-bottom: 5px;"><span class="sidebar-text">Dashboard</span></div>
                <div class="sidebar-link" style="padding: 10px 12px; color: rgba(255,255,255,0.6); margin-bottom: 5px;"><span class="sidebar-text">Enroll Student</span></div>
                <div class="sidebar-link" style="padding: 10px 12px; color: rgba(255,255,255,0.6); margin-bottom: 5px;"><span class="sidebar-text">Mark Attendance</span></div>
                <div class="sidebar-link" style="padding: 10px 12px; color: rgba(255,255,255,0.6); margin-bottom: 5px;"><span class="sidebar-text">Attendance History</span></div>
            </nav>
        </aside>

        <!-- Main Wrapper Mockup -->
        <div class="main-wrapper" style="margin-left: 230px; padding: 32px 40px; width: calc(100% - 230px); box-sizing: border-box;">
            
            <div class="dash-header" style="margin-bottom: 22px;">
                <h2 style="font-family: 'Sora', sans-serif; font-size: 20px; font-weight: 700; color: #1e293b; margin: 0;">Dashboard</h2>
                <p style="font-size: 13px; color: #64748b; margin-top: 3px;">Welcome back, Admin!</p>
            </div>

            <div class="stat-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px;">
                <div class="stat-card" style="background: white; border-radius: 12px; padding: 18px; border: 0.5px solid #e2e8f0;">
                    <div style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Total Students</div>
                    <div style="font-size: 28px; font-weight: 700; color: #1e293b; margin: 4px 0;">124</div>
                    <div style="font-size: 11px; color: #64748b;">Enrolled this term</div>
                </div>
                <div class="stat-card" style="background: white; border-radius: 12px; padding: 18px; border: 0.5px solid #e2e8f0;">
                    <div style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Present Today</div>
                    <div style="font-size: 28px; font-weight: 700; color: #1e293b; margin: 4px 0;">98</div>
                    <div style="font-size: 11px; color: #64748b;">79% attendance rate</div>
                </div>
                <div class="stat-card" style="background: white; border-radius: 12px; padding: 18px; border: 0.5px solid #e2e8f0;">
                    <div style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Late Today</div>
                    <div style="font-size: 28px; font-weight: 700; color: #1e293b; margin: 4px 0;">12</div>
                    <div style="font-size: 11px; color: #64748b;">Tardiness records</div>
                </div>
                <div class="stat-card" style="background: white; border-radius: 12px; padding: 18px; border: 0.5px solid #e2e8f0;">
                    <div style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Absent Today</div>
                    <div style="font-size: 28px; font-weight: 700; color: #1e293b; margin: 4px 0;">14</div>
                    <div style="font-size: 11px; color: #64748b;">Missing students</div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1.6fr 1fr;gap:16px;margin-bottom:20px;">
                <div class="chart-card" style="background: white; border-radius: 12px; padding: 22px; border: 0.5px solid #e2e8f0; height: 220px;">
                    <h3 style="margin: 0 0 10px 0; font-size: 14px; font-weight: bold; color: #1e293b;">Weekly Attendance</h3>
                    <div style="height: 180px; background: #f8fafc; border-radius: 8px;"></div>
                </div>
                <div class="chart-card" style="background: white; border-radius: 12px; padding: 22px; border: 0.5px solid #e2e8f0; height: 220px;">
                    <h3 style="margin: 0 0 10px 0; font-size: 14px; font-weight: bold; color: #1e293b;">Recent Records</h3>
                    <div style="height: 180px; background: #f8fafc; border-radius: 8px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div class="login-overlay">
        <div class="login-card">
            <div class="login-header">
                <div style="width: 64px; height: 64px; background: rgba(79, 70, 229, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <p class="login-header" style="margin-top: 5px; font-size: 1.2rem; font-weight: 600; color: var(--text-main);">Welcome Back, Admin!</p>
            </div>

            <?php if(isset($_GET['error'])): ?>
                <div class="error-box" style="background: rgba(255, 0, 0, 0.2); color: #991b1b; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.9rem; text-align: center;">
                    Invalid Username or Password
                </div>
            <?php endif; ?>

            <form action="../actions/auth_login.php" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="admin" autofocus>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn-primary">
                    Sign In to Dashboard
                </button>
            </form>
        </div>
    </div>

</body>
</html>