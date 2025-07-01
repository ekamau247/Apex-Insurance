<?php
session_start();
require '../INCLUDES/db_connects.php';

if (!isset($_SESSION['user_type']) || strtolower($_SESSION['user_type']) !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Stats
function countTable($table, $con) {
    $res = $con->query("SELECT COUNT(*) AS total FROM $table");
    return ($res) ? $res->fetch_assoc()['total'] : 0;
}

$total_users    = countTable("user", $con);
$total_policies = countTable("policy", $con);
$total_claims   = countTable("claims", $con);
$total_payments = countTable("claim_payment", $con);
$total_reports  = countTable("accident_report", $con);

// New claims (last 24 hours)
$new_claims = 0;
$res = $con->query("SELECT COUNT(*) AS total FROM claims WHERE claim_date >= NOW() - INTERVAL 1 DAY");
if ($res) $new_claims = $res->fetch_assoc()['total'];

// Recent users
$recent_users = $con->query("SELECT first_name, last_name, email, user_type, created_at FROM user ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Apex Assurance</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #f4f6f9; }
        .sidebar {
            width: 220px; background: #007BFF; height: 100vh; color: white; position: fixed;
            top: 0; left: 0; padding: 30px 20px;
        }
        .sidebar h2 { text-align: center; }
        .sidebar a {
            display: block; color: white; text-decoration: none; padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .sidebar a:hover { background: #0056b3; }

        .main { margin-left: 240px; padding: 30px; }

        .dashboard {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px; margin-top: 30px;
        }

        .card {
            background: white; border-left: 5px solid #007BFF;
            padding: 20px; border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .card h2 { margin: 0; font-size: 28px; }
        .card p { color: #555; margin-top: 8px; }

        .card.users    { border-color: #007BFF; }
        .card.policies { border-color: #28A745; }
        .card.claims   { border-color: #FFC107; }
        .card.payments { border-color: #17A2B8; }
        .card.reports  { border-color: #DC3545; }

        table {
            width: 100%; background: white; border-collapse: collapse;
            margin-top: 40px;
        }
        th, td {
            padding: 12px; border: 1px solid #ccc; text-align: left;
        }
        th { background: #007BFF; color: white; }

        .alert {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .btn-export {
            margin-top: 30px;
            padding: 10px 20px;
            background: #28A745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="manage_users.php">👥 Manage Users</a>
    <a href="manage_policies.php">📑 Manage Policies</a>
    <a href="manage_claims.php">📂 Manage Claims</a>
    <a href="view_reports.php">🚧 View Reports</a>
    <a href="view_payments.php">💵 View Payments</a>
    <a href="view_cars.php">💵 View vehicles</a>

    <a href="send_notification.php">Send Notification</a></li>
    <li><a href="export_data.php">Export Data</a></li>
    <a href="../logout.php">🚪 Logout</a>
</div>

<div class="main">
    <h1>Welcome Admin 👋</h1>
    <p>Here is your system summary:</p>

    <?php if ($new_claims > 0): ?>
        <div class="alert">
            🔔 You have <strong><?= $new_claims ?></strong> new claim(s) submitted in the last 24 hours.
            <a href="manage_claims.php" style="color: #007BFF; margin-left: 10px;">Review Now</a>
        </div>
    <?php endif; ?>

    <div class="dashboard">
        <div class="card users">
            <h2><?= $total_users ?></h2><p>Registered Users</p>
        </div>
        <div class="card policies">
            <h2><?= $total_policies ?></h2><p>Total Policies</p>
        </div>
        <div class="card claims">
            <h2><?= $total_claims ?></h2><p>Submitted Claims</p>
        </div>
        <div class="card payments">
            <h2><?= $total_payments ?></h2><p>Claim Payments</p>
        </div>
        <div class="card reports">
            <h2><?= $total_reports ?></h2><p>Accident Reports</p>
        </div>
    </div>

    <form method="post" action="export_dashboard.php">
        <button type="submit" class="btn-export">📥 Export Dashboard Stats (CSV)</button>
    </form>

    <h3>Recent Registered Users</h3>
    <table>
        <thead>
            <tr>
                <th>Name</th><th>Email</th><th>Role</th><th>Registered On</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $recent_users->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['user_type']) ?></td>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
