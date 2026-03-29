<?php
// track_status.php - Track policy or claim status
session_start();

// Include database connection
include 'db_connect.php';

$status_result = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tracking_id'])) {
    $tracking_id = intval($_POST['tracking_id']);

    if (empty($tracking_id)) {
        $status_result = "<div class='error-message' style='color: red; margin-top: 1rem;'>Please provide a valid ID.</div>";
    } else {
        // Check if it's a policy purchase
        $stmt = $conn->prepare("SELECT p.purchase_date, p.status, pol.policy_name, pol.type, u.name
                                FROM purchases p
                                JOIN policies pol ON p.policy_id = pol.id
                                JOIN users u ON p.user_id = u.id
                                WHERE p.id = ?");
        $stmt->bind_param("i", $tracking_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $purchase = $result->fetch_assoc();
            $status_result = "<div class='status-result' style='margin-top: 1rem; padding: 1rem; border: 1px solid #ccc; border-radius: 5px;'>";
            $status_result .= "<h3>Policy Purchase Status</h3>";
            $status_result .= "<p><strong>Policy:</strong> " . htmlspecialchars($purchase['policy_name']) . " (" . htmlspecialchars($purchase['type']) . ")</p>";
            $status_result .= "<p><strong>Status:</strong> " . htmlspecialchars($purchase['status']) . "</p>";
            $status_result .= "<p><strong>Purchase Date:</strong> " . htmlspecialchars($purchase['purchase_date']) . "</p>";
            $status_result .= "<p><strong>User:</strong> " . htmlspecialchars($purchase['name']) . "</p>";
            $status_result .= "</div>";
        } else {
            // Check if it's a claim
            $stmt->close();
            $stmt = $conn->prepare("SELECT c.claim_date, c.claim_status as status, c.claim_reason as description, pol.policy_name, pol.type, u.name
                                    FROM claims c
                                    JOIN policies pol ON c.policy_id = pol.id
                                    JOIN users u ON c.user_id = u.id
                                    WHERE c.id = ?");
            $stmt->bind_param("i", $tracking_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $claim = $result->fetch_assoc();
                $status_result = "<div class='status-result' style='margin-top: 1rem; padding: 1rem; border: 1px solid #ccc; border-radius: 5px;'>";
                $status_result .= "<h3>Claim Status</h3>";
                $status_result .= "<p><strong>Policy:</strong> " . htmlspecialchars($claim['policy_name']) . " (" . htmlspecialchars($claim['type']) . ")</p>";
                $status_result .= "<p><strong>Status:</strong> " . htmlspecialchars($claim['status']) . "</p>";
                $status_result .= "<p><strong>Claim Date:</strong> " . htmlspecialchars($claim['claim_date']) . "</p>";
                $status_result .= "<p><strong>Reason:</strong> " . htmlspecialchars(substr($claim['description'], 0, 100)) . "...</p>";
                $status_result .= "<p><strong>User:</strong> " . htmlspecialchars($claim['name']) . "</p>";
                $status_result .= "</div>";
            } else {
                $status_result = "<div class='error-message' style='color: red; margin-top: 1rem;'>No record found for the provided ID.</div>";
            }
        }

        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Status - Insurance Policy Management System</title>
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
        <h1>Track Your Policy or Claim Status</h1>
        <form id="track-status-form" class="flex-form" action="track_status.php" method="POST">
            <div class="form-group">
                <label for="tracking_id">Policy ID or Claim ID</label>
                <input type="text" id="tracking_id" name="tracking_id" required placeholder="Enter Policy ID or Claim ID">
            </div>
            <button type="submit" class="btn btn-primary">Check Status</button>
        </form>

        <?php echo $status_result; ?>
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

<?php
$conn->close();
?>
