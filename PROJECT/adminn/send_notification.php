<?php
session_start();
require '../INCLUDES/db_connects.php';

// Check Admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

$success = $error = "";

// Fetch users for dropdown
$users = $con->query("SELECT Id, first_name, last_name FROM user ORDER BY first_name");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $title = $_POST['title'];
    $message = $_POST['message'];

    // Related fields (optional)
    $related_id = 0; // Or null
    $related_type = 'AccidentReport'; // Default type

    $stmt = $con->prepare("INSERT INTO notification (user_id, related_id, related_type, title, message, is_read, status) 
                           VALUES (?, ?, ?, ?, ?, 0, 'unread')");
    if ($stmt === false) {
        $error = "❌ Failed to prepare query: " . $con->error;
    } else {
        $stmt->bind_param("iisss", $user_id, $related_id, $related_type, $title, $message);
        if ($stmt->execute()) {
            $success = "✅ Notification sent successfully.";
        } else {
            $error = "❌ Failed to execute query: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send Notification</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f4f4f4; }
        form { background: white; padding: 20px; border-radius: 10px; max-width: 500px; margin: auto; }
        label { display: block; margin-top: 10px; }
        input, textarea, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; background: #007BFF; color: white; border: none; padding: 10px 15px; cursor: pointer; }
        .success { color: green; }
        .error { color: red; }
        a.btn { display: inline-block; margin-top: 20px; text-decoration: none; padding: 10px 15px; background: #28a745; color: #fff; border-radius: 5px; }
    </style>
</head>
<body>
    <h2>📢 Send Notification</h2>

    <?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>
    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>

    <form method="POST">
        <label for="user_id">Select User</label>
        <select name="user_id" required>
            <option value="">-- Select User --</option>
            <?php while ($row = $users->fetch_assoc()): ?>
                <option value="<?= $row['Id'] ?>">
                    <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="title">Title</label>
        <input type="text" name="title" required>

        <label for="message">Message</label>
        <textarea name="message" rows="4" required></textarea>

        <button type="submit">Send Notification</button>
    </form>

    <a class="btn" href="index.php">⬅️ Back to Dashboard</a>
</body>
</html>
