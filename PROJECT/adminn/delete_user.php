<?php
session_start();
require '../INCLUDES/db_connects.php';

if (!isset($_SESSION['user_type']) || strtolower($_SESSION['user_type']) !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("User ID not provided.");
}

$user_id = intval($_GET['id']);

// Optional: protect against deleting yourself
if ($_SESSION['user_id'] == $user_id) {
    die("You cannot delete your own account.");
}

$stmt = $con->prepare("DELETE FROM user WHERE Id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    header("Location: manage_users.php?deleted=1");
    exit;
} else {
    echo "Failed to delete user.";
}
?>
