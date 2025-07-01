<?php
session_start();
require 'db_connects.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];

// Fetch client policies
$stmt = $con->prepare("SELECT policy_number, policy_type, start_date, end_date, premium_amount, status FROM policy WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$policy_result = $stmt->get_result();
$policies = [];
while ($row = $policy_result->fetch_assoc()) {
    $policies[] = $row;
}
$stmt->close();

// Initialize claim stats
$claimStats = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];

// Fetch total claims
$sql = "SELECT COUNT(*) as count FROM claims WHERE client_id = ?";
$stmt = $con->prepare($sql);
if (!$stmt) {
    die("Prepare failed for total claims: " . $con->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$claimStats['total'] = $total;
$stmt->close();

// Fetch claims by status
$statuses = ['pending', 'approved', 'rejected'];
foreach ($statuses as $status) {
    $sql = "SELECT COUNT(*) as count FROM claims WHERE client_id = ? AND status = ?";
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        die("Prepare failed for $status claims: " . $con->error);
    }
    $stmt->bind_param("is", $user_id, $status);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $claimStats[$status] = $count;
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Client Dashboard - Apex Assurance</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
        }
        .sidebar {
            width: 220px;
            background-color: #007BFF;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 30px;
        }
        .sidebar h2 { text-align: center; margin-bottom: 30px; }
        .sidebar a {
            display: block;
            padding: 15px 25px;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }
        .sidebar a:hover { background-color: #0056b3; }
        .main {
            margin-left: 220px;
            padding: 40px;
        }
        .main h2 { color: #333; }
        .summary-cards {
            display: flex;
            gap: 20px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        .card {
            flex: 1 1 200px;
            background: #007BFF;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .card.pending { background: #FFC107; color: #333; }
        .card.approved { background: #28A745; }
        .card.rejected { background: #DC3545; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background: #007BFF;
            color: white;
        }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .no-data {
            font-style: italic;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Apex Assurance</h2>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="accident_report.php">📄 Report Accident</a>
    <a href="view_claim.php">📊 My Claims</a>
    <a href="edit_profile.php">👤 Edit Profile</a>
    <a href="../logout.php">🚪 Logout</a>
</div>

<div class="main">
    <h2>Welcome, <?= htmlspecialchars($email) ?> 👋</h2>
    <p>Here’s a summary of your account activity:</p>

    <div class="summary-cards">
        <div class="card">
            <h3>Total Claims</h3>
            <p><?= $claimStats['total'] ?></p>
        </div>
        <div class="card pending">
            <h3>Pending</h3>
            <p><?= $claimStats['pending'] ?></p>
        </div>
        <div class="card approved">
            <h3>Approved</h3>
            <p><?= $claimStats['approved'] ?></p>
        </div>
        <div class="card rejected">
            <h3>Rejected</h3>
            <p><?= $claimStats['rejected'] ?></p>
        </div>
    </div>

    <h3>Your Policies</h3>
    <?php if (empty($policies)): ?>
        <p class="no-data">You do not have any policies at the moment.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Policy Number</th>
                    <th>Policy Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Premium Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($policies as $policy): ?>
                    <tr>
                        <td><?= htmlspecialchars($policy['policy_number']) ?></td>
                        <td><?= htmlspecialchars($policy['policy_type']) ?></td>
                        <td><?= htmlspecialchars($policy['start_date']) ?></td>
                        <td><?= htmlspecialchars($policy['end_date']) ?></td>
                        <td>KES <?= number_format($policy['premium_amount'], 2) ?></td>
                        <td><?= htmlspecialchars($policy['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
