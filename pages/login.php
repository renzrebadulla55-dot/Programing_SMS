<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');
$hour = date('H');

if ($hour >= 0 && $hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour >= 12 && $hour < 18) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }

        .bg-image {
            position: absolute;
            top: -5%;
            left: -5%;
            width: 110%;
            height: 110%;
            background-image: url('../assets/login_bg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: blur(10px);
            z-index: 1;
        }

        .bg-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.4);
            z-index: 2;
        }

        .login-container {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
        }

        /* --- Welcome Banner --- */
        .welcome-banner {
            position: absolute;
            z-index: 20;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(20px);
            padding: 30px 60px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.5);
            /* Animation */
            animation: fadeOutBanner 0.6s ease 2.5s forwards;
        }

        .welcome-banner h1 {
            margin: 0;
            color: #1D4ED8;
            font-size: 2.2rem;
            font-weight: 700;
        }

        @keyframes fadeOutBanner {
            0% { opacity: 1; transform: scale(1); visibility: visible; }
            99% { opacity: 0; transform: scale(0.95); visibility: visible; }
            100% { opacity: 0; transform: scale(0.95); visibility: hidden; display: none; }
        }

        /* --- Login Card --- */
        .login-card {
            background: #FFFFFF;
            border-radius: 12px;
            padding: 50px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            
            /* Hidden initially, then pops up */
            opacity: 0;
            transform: translateY(30px) scale(0.95);
            animation: popUpCard 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) 2.8s forwards;
        }

        @keyframes popUpCard {
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .logo-circle {
            width: 90px;
            height: 90px;
            background: #FFFFFF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            border: 2px solid #E5E7EB;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .sys-title {
            color: #1F2937;
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0 0 5px 0;
        }

        .sys-sub {
            color: #6B7280;
            font-size: 0.9rem;
            margin: 0 0 35px 0;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #1F2937;
            background-color: #F0F4F8; 
            box-sizing: border-box;
            transition: border-color 0.2s, background-color 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2563EB;
            background-color: #FFFFFF;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: #1D4ED8;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s;
        }

        .btn-login:hover {
            background: #1E3A8A;
        }

        .error-msg {
            background: #FEF2F2;
            color: #EF4444;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border: 1px solid #FCA5A5;
        }
    </style>
</head>
<body>

    <div class="bg-image"></div>
    <div class="bg-overlay"></div>

    <div class="login-container">
        
        <!-- Welcome Banner (Shows first, then fades out) -->
        <div class="welcome-banner">
            <h1>Hi, <?php echo $greeting; ?>!</h1>
        </div>

        <!-- Login Card (Hidden initially, pops up after banner) -->
        <div class="login-card">
            <div class="logo-circle" style="border: none; box-shadow: none; background: transparent;">
                <img src="../assets/sms_logo.png" alt="SMS Logo" style="width: 70px; height: 70px; object-fit: contain;">
            </div>
            
            <h2 class="sys-title">Student Monitoring System</h2>
            <p class="sys-sub">Sign in to access the dashboard</p>

            <?php if(isset($_GET['error'])): ?>
                <div class="error-msg">
                    Invalid Username or Password
                </div>
            <?php endif; ?>

            <form action="../actions/auth_login.php" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Enter username" autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn-login">Sign In</button>
            </form>
        </div>

    </div>

</body>
</html>