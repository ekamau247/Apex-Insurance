<?php
session_start();
require '../INCLUDES/db_connects.php';

// Ensure admin-only access
if (!isset($_SESSION['user_type']) || strtolower($_SESSION['user_type']) !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle filters
$status_filter = $_GET['status'] ?? '';
$user_filter = $_GET['user'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$where = [];
if (!empty($status_filter)) $where[] = "c.status = '" . $con->real_escape_string($status_filter) . "'";
if (!empty($user_filter)) $where[] = "c.user_id = '" . $con->real_escape_string($user_filter) . "'";
if (!empty($start_date)) $where[] = "DATE(c.submitted_at) >= '" . $con->real_escape_string($start_date) . "'";
if (!empty($end_date)) $where[] = "DATE(c.submitted_at) <= '" . $con->real_escape_string($end_date) . "'";
$where_sql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Fetch claims
$sql = "SELECT c.*, u.first_name, u.last_name FROM claims c JOIN user u ON c.user_id = u.Id $where_sql ORDER BY c.submitted_at DESC";
$result = $con->query($sql);

// Fetch users and status counts for filter and chart
$users = $con->query("SELECT Id, first_name, last_name FROM user");
$status_counts = $con->query("SELECT status, COUNT(*) as total FROM claims GROUP BY status");
$status_data = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
while ($row = $status_counts->fetch_assoc()) {
    $status_data[strtolower($row['status'])] = $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Claims</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; padding: 20px; background: #f4f4f4; }
        h2 { color: #007BFF; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; box-shadow: 0 0 5px #ccc; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        th { background: #007BFF; color: white; }
        tr:nth-child(even) { background: #f2f2f2; }
        .filter-form { margin-bottom: 15px; }
        .filter-form input, .filter-form select { padding: 5px; margin-right: 10px; }
        .action-buttons a { margin-right: 5px; text-decoration: none; color: #007BFF; }
    </style>
</head>
<body>
<h2>Manage Claims</h2>
<a href="dashboard.php">← Back to Dashboard</a>

<form method="GET" class="filter-form">
    <select name="status">
        <option value="">-- Status --</option>
        <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="approved" <?= $status_filter == 'approved' ? 'selected' : '' ?>>Approved</option>
        <option value="rejected" <?= $status_filter == 'rejected' ? 'selected' : '' ?>>Rejected</option>
    </select>
    <select name="user">
        <option value="">-- User --</option>
        <?php while ($user = $users->fetch_assoc()): ?>
            <option value="<?= $user['Id'] ?>" <?= $user_filter == $user['Id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>
    <input type="date" name="start_date" value="<?= $start_date ?>">
    <input type="date" name="end_date" value="<?= $end_date ?>">
    <button type="submit">Filter</button>
</form>

<canvas id="statusChart" width="400" height="150"></canvas>
<script>
const ctx = document.getElementById('statusChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Pending', 'Approved', 'Rejected'],
        datasets: [{
            label: 'Claims by Status',
            data: [<?= $status_data['pending'] ?>, <?= $status_data['approved'] ?>, <?= $status_data['rejected'] ?>],
            backgroundColor: ['orange', 'green', 'red']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});
</script>

<?php if ($result && $result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Claim Code</th>
                <th>Policy Holder</th>
                <th>Status</th>
                <th>Submitted</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['claim_code']) ?></td>
                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['submitted_at']) ?></td>
                <td class="action-buttons">
                    <a href="view_claim.php?id=<?= $row['id'] ?>">View</a>
                    <a href="edit_claim.php?id=<?= $row['id'] ?>">Edit</a>
                    <a href="delete_claim.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this claim?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No claims found.</p>
<?php endif; ?>

</body>
</html>
