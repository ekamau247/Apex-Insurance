<?php
session_start();
require_once 'INCLUDES.PHP/db_connects.php';
// Only allow access if admin
if (!isset($_SESSION['Id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: loginn.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $userIdToDelete = intval($_GET['delete']);
    $stmt = $con->prepare("DELETE FROM user WHERE Id = ?");
    $stmt->bind_param("i", $userIdToDelete);
    $stmt->execute();
    header("Location: manager_users.php"); // Refresh
    exit;
}

// Fetch all users
$result = $con->query("SELECT * FROM user ORDER BY Id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; padding: 20px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .delete-btn {
            color: white; background: red; padding: 5px 10px; text-decoration: none; border-radius: 4px;
        }
    </style>
</head>
<body>

<h2>Manage Users</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Type</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['Id'] ?></td>
            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone_number']) ?></td>
            <td><?= htmlspecialchars($row['user_type']) ?></td>
            <td><a href="?delete=<?= $row['Id'] ?>" onclick="return confirm('Are you sure?');" class="delete-btn">Delete</a></td>
        </tr>
    <?php endwhile;
