<?php
session_start();
require_once '../INCLUDES/db_connects.php';

// Admin-only access
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

// Initialize search variables
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
}

// Check if search is empty and run appropriate query
if (!empty($search_query)) {
    $query = "
        SELECT c.*, u.first_name, u.last_name, u.email 
        FROM car c 
        JOIN user u ON c.user_id = u.Id
        WHERE u.first_name LIKE ? OR u.last_name LIKE ? OR c.number_plate LIKE ?
        ORDER BY c.created_at DESC
    ";
    $stmt = $con->prepare($query);
    if (!$stmt) {
        die("SQL error: " . $con->error);
    }
    $search_param = "%$search_query%";
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "
        SELECT c.*, u.first_name, u.last_name, u.email 
        FROM car c 
        JOIN user u ON c.user_id = u.Id
        ORDER BY c.created_at DESC
    ";
    $result = $con->query($query);
}

// Update status if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['status'], $_POST['car_id'])) {
        $updateStmt = $con->prepare("UPDATE car SET status = ? WHERE Id = ?");
        $updateStmt->bind_param("si", $_POST['status'], $_POST['car_id']);
        $updateStmt->execute();
        header("Location: view_client_cars.php");
        exit;
    }
    if (isset($_POST['delete_id'])) {
        $delStmt = $con->prepare("DELETE FROM car WHERE Id = ?");
        $delStmt->bind_param("i", $_POST['delete_id']);
        $delStmt->execute();
        header("Location: view_client_cars.php");
        exit;
    }
}

// Export to CSV if requested
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="client_cars.csv"');

    $output = fopen("php://output", "w");
    fputcsv($output, ['Client', 'Email', 'Make', 'Model', 'Plate', 'Year', 'Color', 'Engine No', 'Chassis No', 'Value', 'Document', 'Registered On', 'Status']);

    if (!empty($search_query)) {
        $stmt->execute();
        $res = $stmt->get_result();
    } else {
        $res = $result;
    }

    while ($row = $res->fetch_assoc()) {
        fputcsv($output, [
            $row['first_name'] . ' ' . $row['last_name'],
            $row['email'], $row['make'], $row['model'],
            $row['number_plate'], $row['year'], $row['color'],
            $row['engine_number'], $row['chassis_number'],
            $row['value'], $row['document_path'], $row['created_at'], $row['status']
        ]);
    }
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View All Registered Cars</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #888;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
        form.search-bar {
            margin-bottom: 20px;
        }
        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            color: white;
            background-color: #007BFF;
            cursor: pointer;
            text-decoration: none;
            margin-right: 5px;
        }
        .btn-danger {
            background-color: #DC3545;
        }
        .btn-approve {
            background-color: #28A745;
        }
        .btn-reject {
            background-color: #FFC107;
        }
    </style>
</head>
<body>

<h2>All Registered Cars by Clients</h2>

<form method="get" class="search-bar">
    <input type="text" name="search" placeholder="Search by name or number plate" value="<?= htmlspecialchars($search_query) ?>">
    <button type="submit">Search</button>
    <a href="view_cars.php">Reset</a>
    <a href="?export=csv" class="btn">Export CSV</a>
</form>

<table>
    <thead>
    <tr>
        <th>Client</th>
        <th>Email</th>
        <th>Make</th>
        <th>Model</th>
        <th>Plate</th>
        <th>Year</th>
        <th>Color</th>
        <th>Engine No</th>
        <th>Chassis No</th>
        <th>Value</th>
        <th>Document</th>
        <th>Registered On</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['make']) ?></td>
                <td><?= htmlspecialchars($row['model']) ?></td>
                <td><?= htmlspecialchars($row['number_plate']) ?></td>
                <td><?= htmlspecialchars($row['year']) ?></td>
                <td><?= htmlspecialchars($row['color']) ?></td>
                <td><?= htmlspecialchars($row['engine_number']) ?></td>
                <td><?= htmlspecialchars($row['chassis_number']) ?></td>
                <td>KES <?= number_format($row['value'], 2) ?></td>
                <td>
                    <?php if (!empty($row['document_path'])): ?>
                        <a href="<?= htmlspecialchars($row['document_path']) ?>" target="_blank">View</a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td>
                    <form method="post" style="margin:0;">
                        <input type="hidden" name="car_id" value="<?= $row['Id'] ?>">
                        <select name="status" onchange="this.form.submit()">
                            <option value="Pending" <?= $row['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Approved" <?= $row['status'] === 'Approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="Rejected" <?= $row['status'] === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </form>
                </td>
                <td>
                    <a class="btn" href="edit_car.php?id=<?= $row['Id'] ?>">Edit</a>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="delete_id" value="<?= $row['Id'] ?>">
                        <button class="btn btn-danger" type="submit" onclick="return confirm('Are you sure you want to delete this car?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="14">No cars found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>
