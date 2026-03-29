<?php
session_start();
include 'db_connect.php';

// Handle session messages for confirmation
if (isset($_SESSION['purchase_success'])) {
    $success = $_SESSION['purchase_success'];
    unset($_SESSION['purchase_success']);
} elseif (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

$user = null;

// Check login
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        $error = "User not found!";
    }
} else {
    $error = "Please login first.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Policy</title>
    <link rel="stylesheet" href="styles.css">

    <style>
        .flex-form {
            max-width: 600px;
            margin: auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input, select {
            width: 100%;
            padding: 10px;
        }
        .btn {
            padding: 10px 15px;
            cursor: pointer;
        }
    </style>
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
    <h1>Buy Insurance Policy</h1>

    <?php if (isset($error) && $error): ?>
        <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
        <a href="login.php" class="btn">Login</a>

    <?php else: ?>

        <p>Welcome, <strong><?php echo htmlspecialchars($user['name']); ?></strong> 👋</p>

        <?php if (isset($success) && $success): ?>
            <div class="success-box"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form class="flex-form" action="process_buy.php" method="POST">

            <!-- USER DATA -->
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name"
                       value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone"
                       value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>

            <!-- POLICY TYPE -->
            <div class="form-group">
                <label>Policy Type</label>
                <select id="policy_type" name="policy_type" required>
                    <option value="">Select Policy Type</option>

                    <?php
                    $types = $conn->query("SELECT DISTINCT type FROM policies");
                    if ($types && $types->num_rows > 0) {
                        while ($row = $types->fetch_assoc()) {
                            echo "<option value='".htmlspecialchars($row['type'])."'>"
                                 .htmlspecialchars($row['type'])." Insurance</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <!-- POLICY NAME -->
            <div class="form-group">
                <label>Policy Name</label>
                <select id="policy_name" name="policy_id" required>
                    <option value="">First select policy type</option>
                </select>
            </div>

            <!-- PAYMENT -->
            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Debit Card">Debit Card</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="Cash">Cash</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Submit Purchase</button>
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

<script>
const typeDropdown = document.getElementById("policy_type");

if (typeDropdown) {
    typeDropdown.addEventListener("change", function () {
        let type = this.value;

        fetch("get_policies.php?type=" + encodeURIComponent(type))
        .then(res => res.text())
        .then(data => {
            document.getElementById("policy_name").innerHTML = data;
        })
        .catch(() => {
            document.getElementById("policy_name").innerHTML =
                "<option>Error loading policies</option>";
        });
    });
}
</script>

</body>
</html>

<?php $conn->close(); ?>