<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'RepairCenter') {
    header("Location: loginn.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Repair Center Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>You are logged in as a Repair Center.</p>

    <ul>
        <li><a href="view_assigned_repairs.php">View Assigned Repairs</a></li>
        <li><a href="update_repair_status.php">Update Repair Status</a></li>
        <li><a href="profile.php">Edit Profile</a></li>
    </ul>

    <p><a href="logout.php">Logout</a></p>
</body>
</html>
