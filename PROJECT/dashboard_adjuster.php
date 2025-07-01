<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Adjuster') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Adjuster Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>You are logged in as an Adjuster.</p>

    <ul>
        <li><a href="view_claims_to_adjust.php">View Claims to Adjust</a></li>
        <li><a href="update_claim_status.php">Update Claim Status</a></li>
        <li><a href="profile.php">Edit Profile</a></li>
    </ul>

    <p><a href="logout.php">Logout</a></p>
</body>
</html>
