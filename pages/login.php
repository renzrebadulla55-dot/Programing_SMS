<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | StudentMonitoringSystem</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body class="login-body">

    <div class="login-card">
        <div class="login-header">
            <span style="font-size: 3rem;"></span>
            <h2>Login</h2>
            <p class="login-header">Please enter your credentials</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div class="error-box">
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

</body>
</html>