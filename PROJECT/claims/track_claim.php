<?php
session_start();
require '../INCLUDES/db_connects.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';

// Base SQL with claim_code
$sql = "SELECT claims.claim_code, claim_date, amount_claimed, amount_approved, claims.status, remarks
        FROM claims
        JOIN accident_report a ON claims.accident_id = a.id
        WHERE a.user_id = ? AND (claims.claim_code LIKE ? OR claims.status LIKE ? OR remarks LIKE ?)";

// Parameters
$params = [$user_id, $search, $search, $search];
$types = "isss";

// Optional date range
if (!empty($from_date) && !empty($to_date)) {
    $sql .= " AND claim_date BETWEEN ? AND ?";
    $params[] = $from_date;
    $params[] = $to_date;
    $types .= "ss";
}

$sql .= " ORDER BY claim_date DESC";

$stmt = $con->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $con->error);
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="claims_report.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Claim Code', 'Claim Date', 'Amount Claimed', 'Amount Approved', 'Status', 'Remarks']);
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Track My Claims</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #007BFF; color: white; }
        h2 { color: #333; }
        form input, form button {
            margin-right: 10px;
            padding: 8px;
        }
        form {
            background: #fff;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
        }
        .export-btn {
            background: green;
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h2>Track My Claims</h2>
    <form method="GET">
        <input type="text" name="search" placeholder="Search by claim code, status or remarks" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <input type="date" name="from_date" value="<?= htmlspecialchars($from_date) ?>">
        <input type="date" name="to_date" value="<?= htmlspecialchars($to_date) ?>">
        <button type="submit">Filter</button>
        <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'csv'])) ?>" class="export-btn">Download CSV</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>Claim Code</th>
                <th>Claim Date</th>
                <th>Amount Claimed</th>
                <th>Amount Approved</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['claim_code']) ?></td>
                    <td><?= htmlspecialchars($row['claim_date']) ?></td>
                    <td>Ksh <?= number_format($row['amount_claimed'], 2) ?></td>
                    <td>Ksh <?= number_format($row['amount_approved'], 2) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['remarks']) ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No claims found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="view_claim.php" class="button">View Claim</a>
</body>
</html>
