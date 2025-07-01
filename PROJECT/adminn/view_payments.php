<?php
session_start();
require '../INCLUDES/db_connects.php';

if (!isset($_SESSION['user_type']) || !in_array(strtolower($_SESSION['user_type']), ['admin', 'adjuster'])) {
    header("Location: ../login.php");
    exit;
}

$search = isset($_GET['search']) ? '%' . trim($_GET['search']) . '%' : '%';
$from = $_GET['from_date'] ?? '';
$to = $_GET['to_date'] ?? '';

// Build SQL dynamically
$sql = "SELECT * FROM claim_payment WHERE payment_reference LIKE ?";
$params = ["s" => $search];
$types = "s";

if ($from && $to) {
    $sql .= " AND DATE(payment_date) BETWEEN ? AND ?";
    $params["from"] = $from;
    $params["to"] = $to;
    $types .= "ss";
}

$sql .= " ORDER BY payment_date DESC";

$stmt = $con->prepare($sql);
if ($from && $to) {
    $stmt->bind_param($types, $params["s"], $params["from"], $params["to"]);
} else {
    $stmt->bind_param($types, $params["s"]);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Filtered Payments - Apex Assurance</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f6f8;
            padding: 20px;
        }
        h2 {
            color: #333;
        }
        form {
            margin-bottom: 20px;
            background: #fff;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        input[type="text"], input[type="date"] {
            padding: 8px;
            margin-right: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            padding: 8px 14px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
        }
        th {
            background: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
    </style>
</head>
<body>

<h2>Search Claim Payments</h2>

<form method="GET">
    <input type="text" name="search" placeholder="Payment Reference" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    <input type="date" name="from_date" value="<?= htmlspecialchars($from) ?>">
    <input type="date" name="to_date" value="<?= htmlspecialchars($to) ?>">
    <button type="submit">Filter</button>
</form>

<table>
    <tr>
        <th>Payment ID</th>
        <th>Accident Report ID</th>
        <th>Amount</th>
        <th>Method</th>
        <th>Reference</th>
        <th>Date Paid</th>
        <th>Approved By</th>
        <th>Notes</th>
    </tr>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['Id'] ?></td>
            <td><?= $row['accident_report_id'] ?></td>
            <td>KES <?= number_format($row['amount'], 2) ?></td>
            <td><?= htmlspecialchars($row['payment_method']) ?></td>
            <td><?= htmlspecialchars($row['payment_reference']) ?></td>
            <td><?= $row['payment_date'] ?></td>
            <td><?= $row['approved_by'] ?></td>
            <td><?= $row['notes'] ?? '—' ?></td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="8">No results found for the given filters.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
