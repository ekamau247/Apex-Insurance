<?php
session_start();
require '../INCLUDES/db_connects.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "No policy selected.";
    exit;
}

$policy_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Fetch policy details
$sql = "SELECT * FROM policy WHERE id = ? AND user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $policy_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Policy not found or access denied.";
    exit;
}

$policy = $result->fetch_assoc();

// Fetch renewal documents
$renewal_sql = "SELECT * FROM policy_renewals WHERE policy_id = ? AND user_id = ? ORDER BY uploaded_at DESC";
$renewal_stmt = $con->prepare($renewal_sql);
$renewal_stmt->bind_param("ii", $policy_id, $user_id);
$renewal_stmt->execute();
$renewal_result = $renewal_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Policy Details</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f1f1f1; }
        .container { background: #fff; padding: 20px; border-radius: 8px; }
        h2 { color: #007BFF; }
        p { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        th { background-color: #007BFF; color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Policy Details</h2>
        <p><strong>Policy Number:</strong> <?= htmlspecialchars($policy['policy_number']) ?></p>
        <p><strong>Type:</strong> <?= htmlspecialchars($policy['policy_type']) ?></p>
        <p><strong>Start Date:</strong> <?= htmlspecialchars($policy['start_date']) ?></p>
        <p><strong>End Date:</strong> <?= htmlspecialchars($policy['end_date']) ?></p>
        <p><strong>Premium:</strong> KES <?= number_format($policy['premium_amount'], 2) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($policy['status']) ?></p>

        <hr>

        <?php if (isset($_GET['upload']) && $_GET['upload'] === 'success'): ?>
            <p style="color: green;">Renewal document uploaded successfully!</p>
        <?php endif; ?>

        <h3>Upload Renewal Document</h3>
        <form action="upload_renewal.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="policy_id" value="<?= htmlspecialchars($policy['id']) ?>">
    <input type="file" name="renewal_doc" required>
    <button type="submit">Upload</button>
</form>


        <h3>Uploaded Renewal Documents</h3>
        <?php if ($renewal_result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>File Name</th>
                    <th>Uploaded At</th>
                    <th>Action</th>
                </tr>
                <?php while ($renewal = $renewal_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($renewal['file_path']) ?></td>
                        <td><?= htmlspecialchars($renewal['uploaded_at']) ?></td>
                        <td><a href="../uploads/upload_renewal.php<?= urlencode($renewal['file_path']) ?>" target="_blank">View</a></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No renewal documents uploaded yet.</p>
        <?php endif; ?>

        <p><a href="view_policies.php">← Back to My Policies</a></p>
    </div>
</body>
</html>
