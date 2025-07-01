<?php
session_start();
require '../INCLUDES/db_connects.php';

// Session & Access Control
/*
if (!isset($_SESSION['Id']) || $_SESSION['user_type'] !== 'Adjuster') {
    header("Location: ../login.php");
    exit;
}
*/
$adjusterId = $_SESSION['user_id'];

// Safer getCount function with error reporting
function getCount($con, $query, $param = null) {
    $stmt = $con->prepare($query);
    if (!$stmt) {
        die("Query Error: " . $con->error . "<br>Query: $query");
    }
    if ($param !== null) {
        $stmt->bind_param("i", $param);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        die("Fetch Error: " . $stmt->error);
    }
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

// Fetch Dashboard Statistics
$totalClaims     = getCount($con, "SELECT COUNT(*) AS total FROM claims WHERE adjuster_id = ?", $adjusterId);
$pendingClaims   = getCount($con, "SELECT COUNT(*) AS total FROM claims WHERE adjuster_id = ? AND status = 'Pending'", $adjusterId);
$approvedClaims  = getCount($con, "SELECT COUNT(*) AS total FROM claims WHERE adjuster_id = ? AND status = 'Approved'", $adjusterId);
$rejectedClaims  = getCount($con, "SELECT COUNT(*) AS total FROM claims WHERE adjuster_id = ? AND status = 'Rejected'", $adjusterId);
$assignedReports = getCount($con, "SELECT COUNT(*) AS total FROM accident_report WHERE adjuster_id = ?", $adjusterId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Adjuster Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 1100px;
      margin: auto;
      padding: 40px 20px;
    }
    h1 {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
    }
    nav {
      text-align: right;
      margin-bottom: 20px;
    }
    nav a {
      color: #007bff;
      text-decoration: none;
      margin-left: 20px;
      font-weight: bold;
    }
    nav a:hover {
      text-decoration: underline;
    }
    .cards {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: space-between;
    }
    .card {
      flex: 1;
      min-width: 220px;
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      text-align: center;
    }
    .card h2 {
      font-size: 36px;
      margin-bottom: 10px;
      color: #007bff;
    }
    .card p {
      font-size: 16px;
      color: #555;
    }
    .footer {
      text-align: center;
      margin-top: 40px;
      color: #888;
    }
  </style>
</head>
<body>
  <div class="container">
    <nav>
      <span>Welcome, Adjuster</span>
      <a href="../logout.php">Logout</a>
    </nav>
    
    <h1>Adjuster Dashboard</h1>
    <div class="cards">
      <div class="card">
        <h2><?= $totalClaims ?></h2>
        <p>Total Assigned Claims</p>
      </div>
      <div class="card">
        <h2><?= $pendingClaims ?></h2>
        <p>Pending Claims</p>
      </div>
      <div class="card">
        <h2><?= $approvedClaims ?></h2>
        <p>Approved Claims</p>
      </div>
      <div class="card">
        <h2><?= $rejectedClaims ?></h2>
        <p>Rejected Claims</p>
      </div>
      <div class="card">
        <h2><?= $assignedReports ?></h2>
        <p>Assigned Reports</p>
      </div>
    </div>
    <div class="footer">
      &copy; <?= date('Y') ?> Apex Assurance - Adjuster Panel
    </div>
  </div>
</body>
</html>
