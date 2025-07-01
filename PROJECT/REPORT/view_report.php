<?php
session_start();
require 'INCLUDES.PHP/db_connects.php'; // Ensure this path is correct and the file exists

// Only allow Admin access
if (!isset($_SESSION['Id']) || $_SESSION['user_type'] !== 'Admin') {
    die("Access denied. Admins only.");
}

// Handle search filter
$search = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : '';

if ($search) {
    $query = "SELECT ar.*, u.First_Name, u.Last_Name 
              FROM accident_report ar
              JOIN user u ON ar.user_id = u.id
              WHERE ar.location LIKE '%$search%' OR ar.date_reported LIKE '%$search%'
              ORDER BY ar.date_reported DESC";
} else {
    $query = "SELECT ar.*, u.First_Name, u.Last_Name 
              FROM accident_report ar
              JOIN user u ON ar.user_id = u.id
              ORDER BY ar.date_reported DESC";
}

$result = $con->query($query);

if (!$result) {
    die("Error executing query: " . $con->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Accident Reports</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        h2 { text-align: center; color: #333; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .search-box, .export-links {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type=text] {
            padding: 8px;
            width: 250px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input[type=submit] {
            padding: 8px 16px;
            background-color: #007bff;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        a.button {
            padding: 6px 12px;
            background: #28a745;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 5px;
        }
        a.button:hover {
            background: #218838;
        }
        .actions a {
            margin-right: 8px;
        }
    </style>
</head>
<body>

<h2>All Accident Reports</h2>

<div class="search-box">
    <form method="GET">
        <input type="text" name="search" placeholder="Search by location or date" value="<?= htmlspecialchars($search) ?>" />
        <input type="submit" value="Search" />
    </form>
</div>

<div class="export-links">
    <a class="button" href="export_excel.php" target="_blank">Export to Excel</a>
    <a class="button" href="export_pdf.php" target="_blank">Export to PDF</a>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Reported By</th>
        <th>Location</th>
        <th>Date Reported</th>
        <th>Description</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['First_Name'] . ' ' . $row['Last_Name']) ?></td>
                <td><?= htmlspecialchars($row['location']) ?></td>
                <td><?= htmlspecialchars($row['date_reported']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td class="actions">
                    <a href="edit_report.php?id=<?= $row['id'] ?>" class="button">Edit</a>
                    <a href="delete_report.php?id=<?= $row['id'] ?>" class="button" style="background:#dc3545;" onclick="return confirm('Are you sure you want to delete this report?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="7" style="text-align:center;">No reports found.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
