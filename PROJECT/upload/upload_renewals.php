<?php
session_start();
require '../INCLUDES/db_connects.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $policy_id = intval($_POST['policy_id']);

    if (isset($_FILES['renewal_doc']) && $_FILES['renewal_doc']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['renewal_doc']['tmp_name'];
        $fileName = $_FILES['renewal_doc']['name'];
        $fileSize = $_FILES['renewal_doc']['size'];
        $fileType = $_FILES['renewal_doc']['type'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
        if (!in_array($fileExt, $allowedExtensions)) {
            die("Invalid file type. Only PDF, JPG, and PNG are allowed.");
        }

        $uploadDir = '../uploads/renewals/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newFileName = uniqid('renewal_', true) . '.' . $fileExt;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // Save file path in DB
            $stmt = $con->prepare("INSERT INTO policy_renewals (policy_id, user_id, file_path, uploaded_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iis", $policy_id, $user_id, $newFileName);
            $stmt->execute();

            header("Location: view_policy_details.php?id=" . $policy_id . "&upload=success");
            exit;
        } else {
            echo "Failed to move uploaded file.";
        }
    } else {
        echo "No file uploaded or upload error.";
    }
} else {
    echo "Invalid request.";
}
?>
