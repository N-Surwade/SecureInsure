<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ✅ Check login
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        $_SESSION['purchase_error'] = "⚠ Please login first.";
        header("Location: login.php");
        exit();
    }

    $user_id = intval($_SESSION['user_id']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $policy_type = $_POST['policy_type'];
    $payment_method = $_POST['payment_method'];

    // ✅ Validate inputs
    if (empty($full_name) || empty($email) || empty($phone) || empty($policy_type) || empty($payment_method)) {
        $_SESSION['purchase_error'] = "All fields are required.";
        header("Location: " . ($_POST['return_url'] ?? 'buy_policy.php'));
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['purchase_error'] = "Invalid email format.";
        header("Location: " . ($_POST['return_url'] ?? 'buy_policy.php'));
        exit();
    }

    // ✅ Check if user exists (IMPORTANT FIX)
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $_SESSION['purchase_error'] = "Invalid user. Please login again.";
        header("Location: login.php");
        exit();
    }
    $stmt->close();

    // ✅ Get policy_id
    $policy_id = isset($_POST['policy_id']) ? intval($_POST['policy_id']) : 0;

    if ($policy_id <= 0) {
        $_SESSION['purchase_error'] = "Invalid policy selected.";
        header("Location: " . ($_POST['return_url'] ?? 'buy_policy.php'));
        exit();
    }

    // ✅ Verify policy exists
    $stmt = $conn->prepare("SELECT id FROM policies WHERE id = ?");
    $stmt->bind_param("i", $policy_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $_SESSION['purchase_error'] = "Policy not found.";
        header("Location: " . ($_POST['return_url'] ?? 'buy_policy.php'));
        exit();
    }
    $stmt->close();

    // ✅ Insert purchase (FINAL STEP)
    $stmt = $conn->prepare("INSERT INTO purchases (user_id, policy_id, payment_method) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $policy_id, $payment_method);

    if ($stmt->execute()) {
        $purchase_id = $conn->insert_id;

        $_SESSION['purchase_success'] = "✅ Policy purchased successfully! ID: {$purchase_id}";
        header("Location: " . ($_POST['return_url'] ?? 'buy_policy.php'));
        exit();
    } else {
        $_SESSION['purchase_error'] = "❌ Purchase failed. Try again.";
        header("Location: " . ($_POST['return_url'] ?? 'buy_policy.php'));
        exit();
    }

    $stmt->close();
}

$conn->close();
?>