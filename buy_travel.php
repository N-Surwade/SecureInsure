<?php
// buy_travel.php - Travel policy purchase form
session_start();
include 'db_connect.php';

$user = null;
$error = '';
$success = '';
$policy_name = '';
$policy_premium = 0;

if (isset($_GET['policy'])) {
    $policy_id = intval($_GET['policy']);
    $stmt = $conn->prepare("SELECT policy_name, premium FROM policies WHERE id = ? AND type = 'Travel'");
    $stmt->bind_param("i", $policy_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $policy_name = $row['policy_name'];
        $policy_premium = $row['premium'];
    }
    $stmt->close();
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (isset($_SESSION['purchase_success'])) {
        $success = $_SESSION['purchase_success'];
        unset($_SESSION['purchase_success']);
    }
    if (isset($_SESSION['purchase_error'])) {
        $error = $_SESSION['purchase_error'];
        unset($_SESSION['purchase_error']);
    }
} else {
    $error = "Please login to purchase.";
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy <?php echo htmlspecialchars($policy_name); ?> - SecureInsure</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo">SecureInsure</div>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="policies.php">Policies</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main class="container">
        <h1>Travel Insurance - <?php echo htmlspecialchars($policy_name); ?></h1>
        <div class="card">
            <p><strong>Premium:</strong> $<?php echo number_format($policy_premium, 2); ?></p>
        </div>
        <?php if ($error): ?>
            <div style="color: red;"><?php echo htmlspecialchars($error); ?></div>
            <a href="login.php" class="btn">Login</a>
        <?php else: ?>
            <?php if ($success): ?>
                <div class="success-message" style="color: green; background: #d4edda; padding: 1rem; border-radius: 4px;">
                    <?php echo htmlspecialchars($success); ?>
                    <script>alert('<?php echo addslashes($success); ?>');</script>
                </div>
            <?php endif; ?>
            <form class="flex-form" action="process_buy.php" method="POST">
                <input type="hidden" name="policy_id" value="<?php echo isset($_GET['policy']) ? intval($_GET['policy']) : ''; ?>">
                <input type="hidden" name="policy_name" value="<?php echo htmlspecialchars($policy_name); ?>">
                <input type="hidden" name="policy_type" value="Travel">
                <input type="hidden" name="return_url" value="buy_travel.php?policy=<?php echo isset($_GET['policy']) ? intval($_GET['policy']) : ''; ?>">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" value="<?php echo $user['name'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $user['email'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" value="<?php echo $user['phone'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Trip Start Date</label>
                    <input type="date" name="trip_start" required>
                </div>
                <div class="form-group">
                    <label>Trip End Date</label>
                    <input type="date" name="trip_end" required>
                </div>
                <div class="form-group">
                    <label>Destination Country</label>
                    <input type="text" name="destination" required>
                </div>
                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method" required>
                        <option value="">Select</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Debit Card">Debit Card</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Purchase Travel Policy</button>
            </form>
        <?php endif; ?>
    </main>
    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2023 SecureInsure. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="script.js"></script>
</body>
</html>
