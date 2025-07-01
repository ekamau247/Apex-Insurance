<?php
session_start();
require 'db_connects.php';


if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome Admin, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>You have full access to the system.</p>

    <ul>
        <li><a href="manager_users.php">Manage Users</a></li>
        <li><a href="view_reports.php">View Reports</a></li>
        <li><a href="settings.php">System Settings</a></li>
         <li><a href="manage_claims.php">Manage Calims</a></li>
        <li><a href="manage_policies.php">Manage Policies</a></li>
        <li><a href="view_reports.php">View Reports</a></li>
        <li><a href="assign_adjuster.php">Assign Adjuster</a></li>
        <li><a href="send_notification.php">Send Notification</a></li>

        <li><a href="export_data.php">Export Data</a></li>
        <li><a href="profile.php">Edit Profile</a></li>
         </ul>


    <p><a href="logout.php">Logout</a></p>
</body>
</html>
