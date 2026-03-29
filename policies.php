<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Policies - Insurance Policy Management System</title>
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
                <li><a href="track_status.html">Track Status</a></li>
                <li><a href="claim_form.html">Claim</a></li>
                <li><a href="login.html">Login</a></li>
                <li><a href="register.html">Register</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h1>Our Insurance Policies</h1>
        <div class="grid" id="policies-container">
            <?php
            // Include database connection
            include 'db_connect.php';

            // Fetch policies from database
            $sql = "SELECT * FROM policies ORDER BY type, policy_name";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="card">';
                    echo '<h3>' . htmlspecialchars($row['policy_name']) . '</h3>';
                    echo '<p><strong>Type:</strong> ' . htmlspecialchars($row['type']) . '</p>';
                    echo '<p><strong>Premium:</strong> $' . number_format($row['premium'], 2) . '</p>';
                    echo '<p><strong>Duration:</strong> ' . htmlspecialchars($row['duration']) . '</p>';
                    echo '<button class="btn" onclick="openModal(\'modal-' . $row['id'] . '\')">View Details</button>';
                    echo '</div>';

                    // Modal for details
                    echo '<div id="modal-' . $row['id'] . '" class="modal">';
                    echo '<div class="modal-content">';
                    echo '<span class="close">&times;</span>';
                    echo '<h2>' . htmlspecialchars($row['policy_name']) . '</h2>';
                    echo '<p><strong>Type:</strong> ' . htmlspecialchars($row['type']) . '</p>';
                    echo '<p><strong>Premium:</strong> $' . number_format($row['premium'], 2) . '</p>';
                    echo '<p><strong>Duration:</strong> ' . htmlspecialchars($row['duration']) . '</p>';
                    echo '<p><strong>Benefits:</strong> ' . htmlspecialchars($row['benefits']) . '</p>';
                    echo '<p><strong>Description:</strong> ' . htmlspecialchars($row['description']) . '</p>';
                    $type = $row['type'];
                    if ($type == 'Health') {
                        $buy_link = "buy_health.php?policy={$row['id']}";
                    } elseif ($type == 'Life') {
                        $buy_link = "buy_life.php?policy={$row['id']}";
                    } elseif ($type == 'Vehicle') {
                        $buy_link = "buy_vehicle.php?policy={$row['id']}";
                    } elseif ($type == 'Travel') {
                        $buy_link = "buy_travel.php?policy={$row['id']}";
                    } else {
                        $buy_link = "buy_policy.php?policy={$row['id']}";
                    }
                    echo '<a href="' . $buy_link . '" class="btn btn-primary">Buy This Policy</a>';
                    echo '</div>';
                    echo '</div>'; 
                }
            } else {
                echo '<p>No policies available at the moment.</p>';
            }

            if (isset($conn)) {
                $conn->close();
            }
            ?>
        </div>
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
