<?php
session_start();
require_once '../INCLUDES/db_connects.php';

// Check admin login
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

// Validate car ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid car ID.");
}

$car_id = intval($_GET['id']);

// Update car status to 'Approved'
$query = "UPDATE car SET status = 'Approved' WHERE id = ?";
$stmt = $con->prepare($query);

if (!$stmt) {
    die("SQL error: " . $con->error);
}

$stmt->bind_param("i", $car_id);

if ($stmt->execute()) {
    header("Location: view_cars.php?msg=Car approved successfully");
} else {
    echo "Error approving car: " . $stmt->error;
}

$stmt->close();
$con->close();
?>
