<?php
include 'db_connect.php';

$type = $_GET['type'];

$stmt = $conn->prepare("SELECT id, policy_name FROM policies WHERE type = ?");
$stmt->bind_param("s", $type);
$stmt->execute();
$result = $stmt->get_result();

echo '<option value="">Select Policy</option>';

while ($row = $result->fetch_assoc()) {
    echo "<option value='{$row['id']}'>{$row['policy_name']}</option>";
}

$stmt->close();
$conn->close();
?>