<?php
session_start();
require_once '../INCLUDES/db_connects.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Client') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $con->prepare("SELECT * FROM car WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Registered Cars</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h2 {
            color: #333;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }

        table, th, td {
            border: 1px solid #444;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
            padding: 10px;
        }

        td {
            padding: 8px;
        }

        .status-approved {
            color: green;
            font-weight: bold;
        }

        .status-rejected {
            color: red;
            font-weight: bold;
        }

        .status-pending {
            color: orange;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>My Registered Cars</h2>

    <?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Make</th>
            <th>Model</th>
            <th>Number Plate</th>
            <th>Engine No.</th>
            <th>Chassis No.</th>
            <th>Year</th>
            <th>Color</th>
            <th>Value</th>
            <th>Status</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['make']) ?></td>
            <td><?= htmlspecialchars($row['model']) ?></td>
            <td><?= htmlspecialchars($row['number_plate']) ?></td>
            <td><?= htmlspecialchars($row['engine_number']) ?></td>
            <td><?= htmlspecialchars($row['chassis_number']) ?></td>
            <td><?= htmlspecialchars($row['year']) ?></td>
            <td><?= htmlspecialchars($row['color']) ?></td>
            <td><?= htmlspecialchars($row['value']) ?></td>
            <td>
                <?php
                $status = $row['status'] ?? 'Pending';
                if ($status === 'Approved') {
                    echo "<span class='status-approved'>Approved</span>";
                } elseif ($status === 'Rejected') {
                    echo "<span class='status-rejected'>Rejected</span>";
                } else {
                    echo "<span class='status-pending'>Pending</span>";
                }
                ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
        <p>No cars registered yet.</p>
    <?php endif; ?>
</body>
</html>
