<?php
session_start();
require '../INCLUDES/db_connects.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$client_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';

$sql = "SELECT 
            c.claim_code,
            c.claim_date,
            c.amount_claimed,
            c.amount_approved,
            c.status,
            c.remarks,
            c.claim_details,
            a.accident_date,
            a.location
        FROM claims c
        JOIN accident_report a ON c.accident_id = a.id
        WHERE c.client_id = ?
        AND (c.claim_code LIKE ? OR c.status LIKE ?)
        ORDER BY c.claim_date DESC";

$stmt = $con->prepare($sql);
$stmt->bind_param("iss", $client_id, $search, $search);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Claims - Apex Assurance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; }
        .container { margin-top: 40px; }
        table th { background-color: #007bff; color: white; }
        .search-bar { margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h3 class="mb-4">My Insurance Claims</h3>

    <form class="form-inline search-bar" method="GET">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search by Claim Code or Status" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="dashboard.php" class="btn btn-secondary ml-auto">Back to Dashboard</a>
    </form>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Claim Code</th>
                <th>Accident Date</th>
                <th>Location</th>
                <th>Claim Date</th>
                <th>Claimed</th>
                <th>Approved</th>
                <th>Status</th>
                <th>Remarks</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['claim_code']) ?></td>
                    <td><?= htmlspecialchars($row['accident_date']) ?></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td><?= htmlspecialchars($row['claim_date']) ?></td>
                    <td>Ksh <?= number_format($row['amount_claimed'], 2) ?></td>
                    <td>Ksh <?= number_format($row['amount_approved'], 2) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['remarks']) ?></td>
                    <td><?= htmlspecialchars($row['claim_details']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="9" class="text-center">No claims found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
