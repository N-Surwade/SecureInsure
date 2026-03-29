@@<?php
// Process claim submission
// This script handles claim form submissions with file upload

include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_POST['user_id']);
    $policy_id = intval($_POST['policy_id']);
    $claim_reason = trim($_POST['claim_reason']);

    // Validate input
    if (empty($user_id) || empty($policy_id) || empty($claim_reason)) {
        echo "All fields are required.";
        exit();
    }

    // Check if user and policy exist
    $stmt = $conn->prepare("SELECT u.id as user_id, p.id as policy_id FROM users u, policies p WHERE u.id = ? AND p.id = ?");
    $stmt->bind_param("ii", $user_id, $policy_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "Invalid user or policy ID.";
        exit();
    }

    $stmt->close();

    // Handle file upload
    $document_path = '';
    if (isset($_FILES['claim_document']) && $_FILES['claim_document']['error'] == 0) {
        $allowed_types = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        $file_name = $_FILES['claim_document']['name'];
        $file_tmp = $_FILES['claim_document']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_types)) {
            $new_file_name = uniqid() . '.' . $file_ext;
            $upload_path = 'uploads/' . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $document_path = $upload_path;
            } else {
                echo "File upload failed.";
                exit();
            }
        } else {
            echo "Invalid file type. Allowed types: PDF, JPG, PNG, DOC, DOCX.";
            exit();
        }
    }

    // Insert claim
    $stmt = $conn->prepare("INSERT INTO claims (user_id, policy_id, claim_reason, document_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $user_id, $policy_id, $claim_reason, $document_path);

if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Claim submitted successfully!',
            'claim_id' => $conn->insert_id
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Claim submission failed. Please try again.'
        ]);
    }

    $stmt->close();
}

$conn->close();
?>

