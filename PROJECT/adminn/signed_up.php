<?php
session_start();
require_once 'INCLUDES.PHP/db_connects.php';

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("❌ CSRF validation failed.");
    }

    // Sanitize inputs
    $First_Name   = trim($_POST['First_Name'] ?? '');
    $Second_Name  = trim($_POST['Second_Name'] ?? '');
    $Last_Name    = trim($_POST['Last_Name'] ?? '');
    $Phone_Number = trim($_POST['Phone_Number'] ?? '');
    $Email        = trim($_POST['email'] ?? '');
    $Password     = $_POST['Password'] ?? '';
    $Gender       = $_POST['Gender'] ?? '';
    $National_Id  = trim($_POST['National_Id'] ?? '');
    $User_type    = $_POST['User_type'] ?? '';

    $errors = [];
    if (empty($First_Name)) $errors[] = "First Name is required.";
    if (empty($Second_Name)) $errors[] = "Second Name is required.";
    if (empty($Last_Name)) $errors[] = "Last Name is required.";
    if (empty($Phone_Number)) $errors[] = "Phone Number is required.";
    if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid Email is required.";
    if (strlen($Password) < 6) $errors[] = "Password must be at least 6 characters.";
    if (empty($Gender)) $errors[] = "Gender is required.";
    if (empty($National_Id)) $errors[] = "National ID is required.";
    if (empty($User_type)) $errors[] = "User Type is required.";

    if (empty($errors)) {
        $PasswordHash = password_hash($Password, PASSWORD_DEFAULT);

        $stmt = $con->prepare("INSERT INTO user 
            (First_Name, second_name, Last_Name, Phone_number, email, password, Gender, National_id, user_type)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt === false) {
            $message = "❌ Failed to prepare statement: " . $con->error;
        } else {
            $stmt->bind_param("sssssssss", $First_Name, $Second_Name, $Last_Name, $Phone_Number, $Email, $PasswordHash, $Gender, $National_Id, $User_type);

            if ($stmt->execute()) {
                header("Location: loginn.php?registered=success");
                exit();
            } else {
                $message = "❌ Registration failed: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $message = "❌ Please fix the following errors:<br>" . implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Apex Assurance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f9fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 350px;
        }
        .register-container h2 {
            text-align: center;
        }
        .register-container img {
            display: block;
            margin: 0 auto 10px;
            width: 80px;
        }
        .register-container input,
        .register-container select,
        .register-container button {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            font-size: 14px;
        }
        .register-container .message {
            color: red;
            margin-bottom: 10px;
        }
        .register-container a {
            text-align: center;
            display: block;
            margin-top: 10px;
            font-size: 13px;
            color: #333;
        }
    </style>
</head>
<body>

<div class="register-container">
    <img src="logo.png" alt="Apex Assurance Logo">
    <h2>Register</h2>

    <?php if (!empty($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <input type="text" name="First_Name" placeholder="First Name" required>
        <input type="text" name="Second_Name" placeholder="Second Name" required>
        <input type="text" name="Last_Name" placeholder="Last Name" required>
        <input type="text" name="Phone_Number" placeholder="Phone Number" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="Password" placeholder="Password (min 6 chars)" required>

        <select name="Gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <input type="text" name="National_Id" placeholder="National ID" required>

        <select name="User_type" required>
            <option value="">Select User Type</option>
            <option value="Client">Client</option>
            <option value="Adjuster">Adjuster</option>
            <option value="Policyholder">Policyholder</option>
            <option value="Repair Center">Repair Center</option>
            <option value="Emergency Service">Emergency Service</option>
        </select>

        <button type="submit">Register</button>
    </form>

    <a href="loginn.php">Already have an account? Login here</a>
</div>

</body>
</html>
