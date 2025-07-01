<?php
session_start();
require '../INCLUDES/db_connects.php';

// Check admin access
if (!isset($_SESSION['user_type']) || strtolower($_SESSION['user_type']) !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Handle search query
$search = isset($_GET['search']) ? $con->real_escape_string(trim($_GET['search'])) : '';
if (!empty($search)) {
    $sql = "SELECT Id, first_name, last_name, email, phone_number, user_type, created_at 
            FROM user 
            WHERE first_name LIKE '%$search%' 
               OR last_name LIKE '%$search%' 
               OR email LIKE '%$search%' 
            ORDER BY created_at DESC";
} else {
    $sql = "SELECT Id, first_name, last_name, email, phone_number, user_type, created_at 
            FROM user 
            ORDER BY created_at DESC";
}

$result = $con->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - Admin</title>
    <style>
        
        body { font-family: 'Segoe UI', sans-serif; background: #f9f9f9; margin: 0; padding: 20px; }
        h2 { color: #007BFF; }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        form input[type="text"] {
            padding: 6px;
            width: 200px;
        }

        form button {
            padding: 6px 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background: #007BFF;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        .action-buttons a {
            margin-right: 8px;
            text-decoration: none;
            color: #007BFF;
        }

        .action-buttons a:hover {
            text-decoration: underline;
        }

        .back-link {
            text-decoration: none;
            color: #007BFF;
        }

        .export-btn {
            text-decoration: none;
            padding: 6px 10px;
            background-color: #28a745;
            color: white;
            border-radius: 4px;
        }

        .export-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<h2>Manage Users</h2>

<div class="top-bar">
    <a class="back-link" href="dashboard.php">← Back to Dashboard</a>

    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <a class="export-btn" href="export_users.php" target="_blank">Export to CSV</a>
</div>

<?php if ($result && $result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>User Role</th>
                <th>Registered On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone_number']) ?></td>
                <td><?= htmlspecialchars($row['user_type']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td class="action-buttons">
                    <a href="edit_user.php?id=<?= $row['Id'] ?>">Edit</a>
                    <a href="delete_user.php?id=<?= $row['Id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No users found.</p>
<?php endif; ?>

</body>
</html>
