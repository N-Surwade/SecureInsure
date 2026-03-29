<?php
// login.php - Login form with error display
session_start();

$login_error = '';
$login_success = '';
if (isset($_SESSION['login_error'])) {
    $login_error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
if (isset($_SESSION['login_success'])) {
    $login_success = $_SESSION['login_success'];
    unset($_SESSION['login_success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Insurance Policy Management System</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="assets/logo-icon.png" type="image/png">
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo">SecureInsure</div>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="policies.php">Policies</a></li>
                <li><a href="buy_policy.php">Buy Policy</a></li>
                <li><a href="track_status.php">Track Status</a></li>
                <li><a href="claim_form.html">Claim</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.html">Register</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h1>User Login</h1>
        <?php if ($login_error): ?>
            <div class="error-message" style="color: red; margin-bottom: 1rem;"><?php echo htmlspecialchars($login_error); ?></div>
        <?php endif; ?>
        <?php if ($login_success): ?>
            <div class="success-message" style="color: green; background: #d4edda; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <?php echo htmlspecialchars($login_success); ?>
                <script>alert('<?php echo addslashes($login_success); ?>');</script>
            </div>
        <?php endif; ?>
        <form id="login-form" class="flex-form" action="process_login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <p style="text-align:center; margin-top:15px; font-size:15px;">
    Don't have an account?
</p>

<div style="display:flex; justify-content:center; gap:10px; margin-top:8px; flex-wrap:wrap;">

    <a href="register.html"
       style="background: linear-gradient(135deg, #28a745, #218838);
              color:white;
              padding:6px 12px;
              border-radius:5px;
              text-decoration:none;
              font-size:14px;
              font-weight:500;
              letter-spacing:0.3px;
              box-shadow:0 2px 6px rgba(0,0,0,0.15);
              transition:0.2s;"
       onmouseover="this.style.transform='scale(1.04)'; this.style.boxShadow='0 4px 10px rgba(0,0,0,0.25)'"
       onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.15)'">
       Register
    </a>

    <a href="admin_login.html"
       style="background: linear-gradient(135deg, #dc3545, #c82333);
              color:white;
              padding:6px 12px;
              border-radius:5px;
              text-decoration:none;
              font-size:14px;
              font-weight:500;
              letter-spacing:0.3px;
              box-shadow:0 2px 6px rgba(0,0,0,0.15);
              transition:0.2s;"
       onmouseover="this.style.transform='scale(1.04)'; this.style.boxShadow='0 4px 10px rgba(0,0,0,0.25)'"
       onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.15)'">
       Admin Login
    </a>

</div>
    </main>

    <footer>
        <div class="container">
            <p class="footer-bottom">&copy; 2023 SecureInsure. All rights reserved.</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>
