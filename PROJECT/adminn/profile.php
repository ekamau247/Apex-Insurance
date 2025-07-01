<?php
session_start();
require_once 'db_connects.php'; // Ensure this file correctly sets $conn

// Validate session variables
if (!isset($_SESSION['user_type']) || !isset($_SESSION['email'])) {
    die("Access denied. Please log in.");
}

$user_type = $_SESSION['user_type'];
$email = $_SESSION['email'];

// Fetch user from database
$query = "SELECT * FROM user WHERE user_type = ? AND email = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("ss", $user_type, $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

$user_id = $user['Id']; // Needed for updates

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $first_name = htmlspecialchars($_POST['first_name']);
    $second_name = htmlspecialchars($_POST['second_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $email_input = htmlspecialchars($_POST['email']);
    $gender = htmlspecialchars($_POST['gender']);
    $national_id = htmlspecialchars($_POST['national_id']);

    // Validate email
    if (!filter_var($email_input, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Update profile
        $update_query = "UPDATE user SET 
                            first_name = ?, 
                            second_name = ?, 
                            last_name = ?, 
                            phone_number = ?, 
                            email = ?, 
                            gender = ?, 
                            national_id = ?,
                            updated_at = CURRENT_TIMESTAMP
                        WHERE Id = ?";
        $stmt = $con->prepare($update_query);
        $stmt->bind_param("sssssssi", $first_name, $second_name, $last_name, $phone_number, $email_input, $gender, $national_id, $user_id);

        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
            // Refresh user data
            $query = "SELECT * FROM user WHERE Id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
        } else {
            $error = "Error updating profile: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3>Edit Profile</h3></div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                                   value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="second_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="second_name" name="second_name"
                                   value="<?php echo htmlspecialchars($user['second_name']); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                                   value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number"
                                   value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="national_id" class="form-label">National ID</label>
                            <input type="text" class="form-control" id="national_id" name="national_id"
                                   value="<?php echo htmlspecialchars($user['national_id']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" name="gender" required>
                                <option value="Male" <?php if ($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                <option value="Female" <?php if ($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                <option value="Other" <?php if ($user['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Profile</button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
