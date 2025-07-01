<?php
session_start();
require '../INCLUDES/db_connects.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch client name
$query = $con->prepare("SELECT first_name, last_name FROM user WHERE Id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo "No user data found.";
    exit;
}

$user = $result->fetch_assoc();
$first_name = $user['first_name'] ?? 'Client';
$last_name = $user['last_name'] ?? '';

// Fetch report counts
$total = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM accident_report WHERE user_id = $user_id"))[0];
$under_review = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM accident_report WHERE user_id = $user_id AND status = 'UnderReview'"))[0];
$approved = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM accident_report WHERE user_id = $user_id AND status = 'Approved'"))[0];
$rejected = mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM accident_report WHERE user_id = $user_id AND status = 'Rejected'"))[0];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Client Dashboard</title>
    <style>
        body {
            font-family: Arial;
            background: #f1f1f1;
            margin: 0;
            padding: 0;
        }
        .header {
            background: #007BFF;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .dashboard {
            padding: 30px;
        }
        .summary-container, .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .summary-card, .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 200px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            transition: 0.3s;
        }
        .summary-card h4 {
            margin: 10px 0;
        }
        .summary-card:hover, .card:hover {
            transform: scale(1.05);
        }
        .summary-total { background: #17a2b8; color: white; }
        .summary-approved { background: #28a745; color: white; }
        .summary-review { background: #ffc107; color: white; }
        .summary-rejected { background: #dc3545; color: white; }
        .card a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }
        .export-buttons {
            margin-top: 20px;
            text-align: center;
        }
        .export-buttons a {
            margin: 5px;
            display: inline-block;
            padding: 10px 15px;
            background: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
        .export-buttons a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
        <?= htmlspecialchars($first_name . ' ' . $last_name) ?>
    </div>
    <div class="dashboard">
        <h3>Your Dashboard</h3>

        <!-- Summary Cards -->
        <div class="summary-container">
            <div class="summary-card summary-total">
                <h4>Total Reports</h4>
                <h2><?= $total ?></h2>
            </div>
            <div class="summary-card summary-review">
                <h4>Under Review</h4>
                <h2><?= $under_review ?></h2>
            </div>
            <div class="summary-card summary-approved">
                <h4>Approved</h4>
                <h2><?= $approved ?></h2>
            </div>
            <div class="summary-card summary-rejected">
                <h4>Rejected</h4>
                <h2><?= $rejected ?></h2>
            </div>
        </div>

        <!-- Dashboard Menu Cards -->
        <div class="card-container" style="margin-top: 30px;">

            <div class="card">
                <a href="register_car.php">📄 </a>Register Vehicle
            </div>
            <div class="card">
                <a href="view_cars.php">📄 </a>View Vehicle
            </div>
            
            
            <div class="card">
                <a href="view_policies.php">📄 View Policies</a>
            </div>
            <div class="card">
                <a href="submit_claim.php">📝 Submit Claim</a>
            </div>
            <div class="card">
                <a href="view_claim.php">📋 My Claims</a>
            </div>
            <div class="card">
                <a href="track_claim.php">🔍 Track Claim</a>
            </div>
            <div class="card">
                <a href="make_payment.php">💳 Make Payment</a>
            </div>
            <div class="card">
                <a href="submit_report.php">🛑 Submit Report</a>
            </div>
            <div class="card">
                <a href="my_report.php">📁 My Reports</a>
            </div>
            <div class="card">
                <a href="edit_profile.php">⚙️ Edit Profile</a>
            </div>
            <div class="card">
                <a href="../logout.php">🚪 Logout</a>
            </div>
        </div>

        <!-- Export Buttons -->
        <div class="export-buttons">
            <a href="export_pdf.php">📄 Export PDF</a>
            <a href="print_report.php">🖨️ Export to PDF (Print)</a>
             <a href="notification.php">🔔 Notifications</a>
</div>

            <div class="card">
</div>

        </div>
    </div>
</body>
</html>
