<?php
session_start();
require '../INCLUDES/db_connects.php';

// Secure access to Admins only
if (!isset($_SESSION['user_type']) || strtolower($_SESSION['user_type']) !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No claim ID provided.");
}

$claim_id = intval($_GET['id']);
$message = "";

// Fetch existing claim data
$sql = "SELECT * FROM claims WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $claim_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Claim not found.");
}

$claim = $result->fetch_assoc();

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $description = trim($_POST['description']);

    // Update claim
    $update_sql = "UPDATE claims SET status = ?, description = ?, updated_at = NOW() WHERE id = ?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->bind_param("ssi", $status, $description, $claim_id);

    if ($update_stmt->execute()) {
        $message = "✅ Claim updated successfully.";
        // Refresh claim info
        $claim['status'] = $status;
        $claim['description'] = $description;
    } else {
        $message = "❌ Failed to update claim.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Claim</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f8;
            padding: 20px;
        }
        .container {
            background: white;
            max-width: 600px;
            margin: auto;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        h2 {
            color: #007BFF;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 15px;
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Claim - #<?= htmlspecialchars($claim['claim_code']) ?></h2>

        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="status">Status</label>
            <select name="status" id="status" required>
                <option value="pending" <?= $claim['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?= $claim['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= $claim['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                <option value="under review" <?= $claim['status'] === 'under review' ? 'selected' : '' ?>>Under Review</option>
            </select>

            <label for="description">Description / Notes</label>
            <textarea name="description" rows="6"><?= htmlspecialchars($claim['description']) ?></textarea>

            <button type="submit">Save Changes</button>
        </form>
        <br>
        <a href="manage_claims.php">← Back to Manage Claims</a>
    </div>
</body>
</html>
