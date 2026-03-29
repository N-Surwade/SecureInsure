<?php
// Admin dashboard
// This script handles admin dashboard functionality and CRUD operations

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.html");
    exit();
}

include 'db_connect.php';

// Handle POST requests for CRUD operations
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add_policy':
            $policy_name = trim($_POST['policy_name']);
            $policy_type = $_POST['policy_type'];
            $premium = floatval($_POST['premium']);
            $duration = trim($_POST['duration']);
            $benefits = trim($_POST['benefits']);
            $description = trim($_POST['description']);

            $stmt = $conn->prepare("INSERT INTO policies (policy_name, type, premium, duration, benefits, description) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsss", $policy_name, $policy_type, $premium, $duration, $benefits, $description);
            $stmt->execute();
            $stmt->close();
            break;

        case 'delete_policy':
            $policy_id = intval($_POST['policy_id']);
            $stmt = $conn->prepare("DELETE FROM policies WHERE id = ?");
            $stmt->bind_param("i", $policy_id);
            $stmt->execute();
            $stmt->close();
            break;

        case 'delete_user':
            $user_id = intval($_POST['user_id']);
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
            break;

        case 'update_claim_status':
            $claim_id = intval($_POST['claim_id']);
            $status = $_POST['status'];
            $stmt = $conn->prepare("UPDATE claims SET claim_status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $claim_id);
            $stmt->execute();
            $stmt->close();
            break;
    }

    // Redirect to avoid form resubmission
    header("Location: admin_dashboard.php");
    exit();
}

// Get statistics
$total_policies = $conn->query("SELECT COUNT(*) as count FROM policies")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_claims = $conn->query("SELECT COUNT(*) as count FROM claims")->fetch_assoc()['count'];
$active_purchases = $conn->query("SELECT COUNT(*) as count FROM purchases WHERE status = 'Active'")->fetch_assoc()['count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Insurance Policy Management System</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="assets/logo-icon.png" type="image/png">
</head>
<body>
    <header>
        <nav class="container">
            <div class="logo">SecureInsure Admin</div>
            <ul class="nav-links">
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h1>Admin Dashboard</h1>

        <!-- Dashboard Statistics -->
        <section class="dashboard-grid">
            <div class="stat-card">
                <h3><?php echo $total_policies; ?></h3>
                <p>Total Policies</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $total_users; ?></h3>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $total_claims; ?></h3>
                <p>Total Claims</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $active_purchases; ?></h3>
                <p>Active Purchases</p>
            </div>
        </section>

        <!-- Manage Policies -->
        <section id="manage-policies">
            <h2>Manage Policies</h2>
            <button class="btn btn-primary" onclick="openModal('add-policy-modal')">Add New Policy</button>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Policy Name</th>
                        <th>Type</th>
                        <th>Premium</th>
                        <th>Duration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $policies = $conn->query("SELECT * FROM policies ORDER BY id DESC");
                    while ($policy = $policies->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $policy['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($policy['policy_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($policy['type']) . "</td>";
                        echo "<td>$" . number_format($policy['premium'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($policy['duration']) . "</td>";
                        echo "<td><form method='POST' style='display:inline;'><input type='hidden' name='action' value='delete_policy'><input type='hidden' name='policy_id' value='" . $policy['id'] . "'><button type='submit' class='btn' onclick='return confirm(\"Are you sure?\")'>Delete</button></form></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <!-- Manage Users -->
        <section id="manage-users">
            <h2>Manage Users</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users = $conn->query("SELECT * FROM users ORDER BY id DESC");
                    while ($user = $users->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $user['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($user['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['phone']) . "</td>";
                        echo "<td><form method='POST' style='display:inline;'><input type='hidden' name='action' value='delete_user'><input type='hidden' name='user_id' value='" . $user['id'] . "'><button type='submit' class='btn' onclick='return confirm(\"Are you sure?\")'>Delete</button></form></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <!-- Manage Claims -->
        <section id="manage-claims">
            <h2>Manage Claims</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User ID</th>
                        <th>Policy ID</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $claims = $conn->query("SELECT * FROM claims ORDER BY id DESC");
                    while ($claim = $claims->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $claim['id'] . "</td>";
                        echo "<td>" . $claim['user_id'] . "</td>";
                        echo "<td>" . $claim['policy_id'] . "</td>";
                        echo "<td>" . htmlspecialchars(substr($claim['claim_reason'], 0, 50)) . "...</td>";
                        echo "<td>" . htmlspecialchars($claim['claim_status']) . "</td>";
                        echo "<td>";
                        echo "<form method='POST' style='display:inline; margin-right: 5px;'>";
                        echo "<input type='hidden' name='action' value='update_claim_status'>";
                        echo "<input type='hidden' name='claim_id' value='" . $claim['id'] . "'>";
                        echo "<select name='status'>";
                        echo "<option value='Pending'" . ($claim['claim_status'] == 'Pending' ? ' selected' : '') . ">Pending</option>";
                        echo "<option value='Approved'" . ($claim['claim_status'] == 'Approved' ? ' selected' : '') . ">Approved</option>";
                        echo "<option value='Rejected'" . ($claim['claim_status'] == 'Rejected' ? ' selected' : '') . ">Rejected</option>";
                        echo "</select>";
                        echo "<button type='submit' class='btn'>Update</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>

    <!-- Add Policy Modal -->
    <div id="add-policy-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Policy</h2>
            <form action="admin_dashboard.php" method="POST">
                <input type="hidden" name="action" value="add_policy">
                <div class="form-group">
                    <label for="policy_name">Policy Name</label>
                    <input type="text" id="policy_name" name="policy_name" required>
                </div>
                <div class="form-group">
                    <label for="policy_type">Type</label>
                    <select id="policy_type" name="policy_type" required>
                        <option value="Health">Health</option>
                        <option value="Life">Life</option>
                        <option value="Vehicle">Vehicle</option>
                        <option value="Travel">Travel</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="premium">Premium</label>
                    <input type="number" id="premium" name="premium" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="duration">Duration</label>
                    <input type="text" id="duration" name="duration" required>
                </div>
                <div class="form-group">
                    <label for="benefits">Benefits</label>
                    <textarea id="benefits" name="benefits" required></textarea>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Add Policy</button>
            </form>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Admin Panel</h3>
                    <p>Insurance Management System</p>
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
