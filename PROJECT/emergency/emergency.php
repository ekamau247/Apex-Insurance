<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'emergency_service') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Emergency Service</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>You are logged in as an emergency_service.</p>

      <ul>
        <li><a href="view_emergency_calls.php">View Emergency Calls</a></li>
        <li><a href="update_emergency_status.php">Update Emergency Status</a></li>
        <li><a href="profile.php">Edit Profile</a></li>
    </ul>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>

</html>
