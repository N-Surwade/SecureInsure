<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // ✅ Validate input
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Email and password are required.";
        header("Location: login.php");
        exit();
    }

    // ✅ Fetch user
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // ✅ Verify password
        if (password_verify($password, $user['password'])) {

            // ✅ STORE SESSION (IMPORTANT)
            $_SESSION['user_id'] = intval($user['id']);
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $email;

            // Optional success message
            $_SESSION['login_success'] = "Welcome, " . $user['name'] . "!";

            // ✅ REDIRECT TO DASHBOARD (IMPORTANT CHANGE)
            header("Location: dashboard.php");
            exit();

        } else {
            $_SESSION['login_error'] = "Invalid password.";
            header("Location: login.php");
            exit();
        }

    } else {
        $_SESSION['login_error'] = "User not found.";
        header("Location: login.php");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>