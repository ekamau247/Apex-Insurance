<?php
session_start();
require '../INCLUDES/db_connects.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Set headers to force Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=accident_reports.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Corrected table name
$sql = "SELECT Id, accident_date, location, description, status, created_at FROM accident_report WHERE user_id = ?";
$stmt = $con->prepare($sql);

if (!$stmt) {
    echo "Database prepare error: " . $con->error;
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<table border='1'>";
echo "<tr>
        <th>Report ID</th>
        <th>Date</th>
        <th>Location</th>
        <th>Description</th>
        <th>Status</th>
        <th>Submitted</th>
      </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['Id']}</td>
            <td>{$row['accident_date']}</td>
            <td>{$row['location']}</td>
            <td>{$row['description']}</td>
            <td>{$row['status']}</td>
            <td>{$row['created_at']}</td>
          </tr>";
}
echo "</table>";
?>
