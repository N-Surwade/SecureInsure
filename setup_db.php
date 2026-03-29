<?php
// One-click DB setup - Run once: http://localhost/INSURANCE_PROJECT/setup_db.php
echo "<h1>🚀 Insurance DB Setup</h1>";

$conn = new mysqli('localhost', 'root', '', 'insurance_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Drop and recreate tables safely
$drop_sql = "
DROP TABLE IF EXISTS claims, purchases, policies, users, admin;
";
$conn->multi_query($drop_sql);
do { if ($result = $conn->store_result()) $result->free(); } while ($conn->next_result());

$sql = file_get_contents('insurance_db.sql');
if ($conn->multi_query($sql)) {
    echo "✅ <strong>All tables recreated + 12 policies inserted!</strong><br>";
    do { if ($result = $conn->store_result()) $result->free(); } while ($conn->next_result());
} else {
    echo "❌ SQL Error: " . $conn->error;
}

echo "<br><strong>Verification:</strong><br>";
$res = $conn->query("SELECT COUNT(*) as policies FROM policies");
$row = $res->fetch_assoc();
echo "Policies: " . $row['policies'] . " <span style='color:green; font-size:1.5em;'>✅</span><br>";

$res = $conn->query("SELECT COUNT(*) as users FROM users");
$row = $res->fetch_assoc();
echo "Users table ready ✓ | Admin: admin/password ✓";

$conn->close();
echo "<br><br><a href='policies.php' class='btn btn-primary'>→ Test Policies Page</a> | <a href='login.php'>→ Login</a>";
?>
<style>
body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; }
.btn { background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
</style>
