<?php
session_start();
require '../INCLUDES/db_connects.php';

if (!isset($_SESSION['user_type']) || strtolower($_SESSION['user_type']) !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("User ID not provided.");
}

$user_id = intval($_GET['id']);

// Get user details
$stmt = $con->prepare("SELECT first_name, last_name, email, phone_number, user_type FROM user WHERE Id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name  = trim($_POST['first_name']);
    $last_name   = trim($_POST['last_name']);
    $phone       = trim($_POST['phone_number']);
    $user_type   = trim($_POST['user_type']);

    $stmt = $con->prepare("UPDATE user SET first_name = ?, last_name = ?, phone_number = ?, user_type = ? WHERE Id = ?");
    $stmt->bind_param("ssssi", $first_name, $last_name, $phone, $user_type, $user_id);

    if ($stmt->execute()) {
        header("Location: manage_users.php?updated=1");
        exit;
    } else {
        $error = "Failed to update user.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <style>
        body { font-family: Arial; padding: 30px; background: #f2f2f2; }
        .form-container {
            background: #fff; padding: 20px; max-width: 500px;
            margin: auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input, select {
            width: 100%; padding: 10px; margin: 10px 0;
        }
        button {
            padding: 10px 20px; background: #007BFF; color: white; border: none;
            border-radius: 6px; cursor: pointer;
        }
        a { display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit User</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <label>First Name</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>

        <label>Last Name</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>

        <label>Phone Number</label>
        <input type="text" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>" required>

        <label>User Type</label>
        <select name="user_type" required>
            <?php
            $roles = ['Admin', 'Adjuster', 'Client', 'Policyholder', 'RepairCenter', 'EmergencyService'];
            foreach ($roles as $role) {
                $selected = strtolower($role) === strtolower($user['user_type']) ? 'selected' : '';
                echo "<option value='$role' $selected>$role</option>";
            }
            ?>
        </select>

        <button type="submit">Update User</button>
        <a href="manager_users.php">← Back to Users</a>
    </form>
</div>

</body>
</html>
