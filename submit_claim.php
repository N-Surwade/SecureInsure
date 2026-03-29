<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $policy_id = $_POST['policy_id'] ?? '';
    $claim_reason = $_POST['claim_reason'] ?? '';

    // Optional file upload
    if(isset($_FILES['claim_document']) && $_FILES['claim_document']['error'] === 0){
        $uploadDir = 'uploads/';
        if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filePath = $uploadDir . basename($_FILES['claim_document']['name']);
        move_uploaded_file($_FILES['claim_document']['tmp_name'], $filePath);
    }

    echo json_encode(['status' => 'success', 'message' => 'Claim submitted successfully!']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
?>