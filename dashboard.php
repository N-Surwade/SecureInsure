<?php
// dashboard.php - User dashboard to view profile and purchased policies
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connect.php';

$error = '';
$user = null;
$purchases = [];

if (isset($_SESSION['user_id'])) {
    // Get user details
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT name, email, phone FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        $error = "User not found. Please login again.";
    } else {
        // Get user's purchases
        $stmt->close();
        $stmt = $conn->prepare("SELECT p.id, p.purchase_date, p.status, p.payment_method, pol.policy_name, pol.type, pol.premium
                                FROM purchases p
                                JOIN policies pol ON p.policy_id = pol.id
                                WHERE p.user_id = ?
                                ORDER BY p.purchase_date DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $purchases[] = $row;
        }
        $stmt->close();
    }
} else {
    $error = "Please login to access the dashboard.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Insurance Policy Management System</title>
<link rel="icon" href="assets/logo-icon.png" type="image/png">
<link rel="stylesheet" href="styles.css">
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
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h1>User Dashboard</h1>
        <?php if ($error): ?>
            <div class="error-message" style="color: red; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></div>
            <a href="login.html" class="btn">Login</a>
            <a href="register.html" class="btn btn-primary">Register</a>
        <?php else: ?>
            <section class="profile-section">
                <h2>Your Profile</h2>
                <div class="profile-info">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                </div>
            </section>

            <section class="purchases-section">
                <h2>Your Purchased Policies</h2>
                <?php if (empty($purchases)): ?>
                    <p>You have not purchased any policies yet.</p>
                    <a href="buy_policy.php" class="btn btn-primary">Buy a Policy</a>
                <?php else: ?>
                    <div class="policies-list">
                        <?php foreach ($purchases as $purchase): ?>
                            <div class="policy-card">
                                <h3><?php echo htmlspecialchars($purchase['policy_name']); ?> (<?php echo htmlspecialchars($purchase['type']); ?>)</h3>
                                <p><strong>Purchase ID:</strong> <?php echo htmlspecialchars($purchase['id']); ?></p>
                                <p><strong>Date:</strong> <?php echo htmlspecialchars($purchase['purchase_date']); ?></p>
                                <p><strong>Status:</strong> <span class="status-<?php echo strtolower($purchase['status']); ?>"><?php echo htmlspecialchars($purchase['status']); ?></span></p>
                                <p><strong>Payment:</strong> <?php echo htmlspecialchars($purchase['payment_method']); ?></p>
                                <p><strong>Premium:</strong> $<?php echo number_format($purchase['premium'], 2); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>About SecureInsure</h3>
                    <p>Your trusted partner for comprehensive insurance solutions.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <p><a href="policies.php">Policies</a></p>
                    <p><a href="buy_policy.php">Buy Policy</a></p>
                    <p><a href="dashboard.php">Dashboard</a></p>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <p>info@secureinsure.com</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 SecureInsure. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>

<?php
$conn->close();
?>
