<?php
// admin_settings.php
session_start();
require 'INCLUDES.PHP/db_connects.php';
require 'setting_function.php';

// Ensure only admin can access
if (!isset($_SESSION['Id']) || $_SESSION['user_type'] !== 'Admin') {
    die("Access denied.");
}

$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    set_setting($con, 'company_name', $_POST['company_name']);
    set_setting($con, 'email_notifications', $_POST['email_notifications']);
    set_setting($con, 'default_claim_days', $_POST['default_claim_days']);
    set_setting($con, 'maintenance_mode', $_POST['maintenance_mode']);
    $message = "\u2705 Settings updated successfully.";
}

// Load current settings
$company_name = get_setting($con, 'company_name');
$email_notifications = get_setting($con, 'email_notifications');
$default_claim_days = get_setting($con, 'default_claim_days');
$maintenance_mode = get_setting($con, 'maintenance_mode');
?>

<!DOCTYPE html>
<html>
<head>
    <title>System Settings</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 30px; }
        form { max-width: 500px; background: white; padding: 20px; border-radius: 8px; margin: auto; }
        label { display: block; margin-top: 15px; }
        input[type="text"], select {
            width: 100%; padding: 10px; margin-top: 5px;
        }
        input[type="submit"] {
            background: #007bff; color: white; padding: 10px 20px; border: none; margin-top: 20px;
            cursor: pointer; border-radius: 5px;
        }
        .message { margin-top: 10px; color: green; }
    </style>
</head>
<body>

<h2 style="text-align: center;">System Settings</h2>

<form method="POST">
    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <label for="company_name">Company Name:</label>
    <input type="text" name="company_name" id="company_name" value="<?= htmlspecialchars($company_name) ?>" required>

    <label for="email_notifications">Email Notifications:</label>
    <select name="email_notifications" id="email_notifications">
        <option value="on" <?= $email_notifications == 'on' ? 'selected' : '' ?>>On</option>
        <option value="off" <?= $email_notifications == 'off' ? 'selected' : '' ?>>Off</option>
    </select>

    <label for="default_claim_days">Default Claim Approval Days:</label>
    <input type="text" name="default_claim_days" id="default_claim_days" value="<?= htmlspecialchars($default_claim_days) ?>" required>

    <label for="maintenance_mode">Maintenance Mode:</label>
    <select name="maintenance_mode" id="maintenance_mode">
        <option value="off" <?= $maintenance_mode == 'off' ? 'selected' : '' ?>>Off</option>
        <option value="on" <?= $maintenance_mode == 'on' ? 'selected' : '' ?>>On</option>
    </select>

    <input type="submit" value="Save Settings">
</form>

</body>
</html>