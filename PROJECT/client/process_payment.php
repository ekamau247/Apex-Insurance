<?php
session_start();
require '../INCLUDES/db_connects.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id            = $_SESSION['user_id'];
    $accident_report_id = intval($_POST['accident_report_id']);
    $amount             = floatval($_POST['amount']);
    $payment_method     = trim($_POST['payment_method']);
    $payment_reference  = trim($_POST['payment_reference']);
    $notes              = isset($_POST['notes']) ? trim($_POST['notes']) : null;

    $stmt = $con->prepare("INSERT INTO claim_payment (user_id, accident_report_id, amount, payment_method, payment_reference, payment_date, notes) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
    if (!$stmt) {
        die("Prepare failed: " . $con->error);
    }

    $stmt->bind_param("iidsss", $user_id, $accident_report_id, $amount, $payment_method, $payment_reference, $notes);

    if ($stmt->execute()) {
        echo "✅ Payment successfully recorded. <a href='make_payment.php'>Make Another</a>";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}
