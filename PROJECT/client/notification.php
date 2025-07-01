<?php
session_start();
require '../INCLUDES/db_connects.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch notifications
$stmt = $con->prepare("SELECT * FROM notification WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Notifications</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; padding: 20px; }
        .notification {
            background: white;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .notification.unread {
            background-color: #e6f7ff;
        }
    </style>
</head>
<body>
    <h2>📬 Your Notifications</h2>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="notification <?= $row['status'] == 'unread' ? 'unread' : '' ?>">
            <strong><?= htmlspecialchars($row['title']) ?></strong><br>
            <?= htmlspecialchars($row['message']) ?><br>
            <small><?= $row['created_at'] ?></small>
        </div>
    <?php endwhile; ?>
</body>
</html>
