<?php
session_start();

// Debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include correct database connection file (using proper directory structure)
require 'INCLUDES.php/db_connects.php'; // Changed to more standard path

// Check connection
if (!isset($con) || !$con) {
    die("Database connection failed");
}

$client_id = $_SESSION['user_id'];

// Fetch policies for the logged-in user
$sql = "SELECT policy_number, policy_type, start_date, end_date, premium_amount, status FROM policy WHERE user_id = ?";
$stmt = $con->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $con->error);
}

$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();

$policies = [];
while ($row = $result->fetch_assoc()) {
    $policies[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Client Dashboard - Policy Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f0f2f5;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        h2 {
            color: #333;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Your Policy Details</h2>

    <?php if (empty($policies)): ?>
        <p>You do not have any policy records yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Policy Number</th>
                    <th>Policy Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Premium Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($policies as $policy): ?>
                    <tr>
                        <td><?= htmlspecialchars($policy['policy_number']) ?></td>
                        <td><?= htmlspecialchars($policy['policy_type']) ?></td>
                        <td><?= htmlspecialchars($policy['start_date']) ?></td>
                        <td><?= htmlspecialchars($policy['end_date']) ?></td>
                        <td><?= htmlspecialchars($policy['premium_amount']) ?></td>
                        <td><?= htmlspecialchars($policy['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>