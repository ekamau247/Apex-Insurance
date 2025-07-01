<?php
ob_start();
session_start();
require 'INCLUDES/db_connects.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $con->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // ✅ Set session variables
            $_SESSION['user_id']   = $user['Id'];
            $_SESSION['email']     = $user['email'];
            $_SESSION['user_type'] = $user['user_type'];

            // ✅ Redirect based on exact user_type
            switch (strtolower($user['user_type'])) {
                case 'admin':
                    header("Location: adminn/dashboard.php");
                    break;
                case 'adjuster':
                    header("Location: adjuster/dashboard.php");
                    break;
                case 'policyholder':
                    header("Location: policy/dashboard.php");
                    break;
                case 'client':
                    header("Location: client/dashboard.php");
                    break;
                case 'emergencyservice':
                    header("Location: emergency/dashboard.php");
                    break;
                case 'repaircenter':
                    header("Location: repair/dashboard.php");
                    break;
                default:
                    header("Location: index.html");
            }
            exit;
        } else {
            $message = "❌ Invalid password.";
        }
    } else {
        $message = "❌ No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Apex Assurance</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #007bff, #00c6ff);
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            padding: 35px 40px;
            border-radius: 12px;
            box-shadow: 0 15px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        label {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
        }

        input[type=email], input[type=password] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        input[type=submit] {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        input[type=submit]:hover {
            background: #0056b3;
        }

        .message {
            background-color: #ffecec;
            color: #d8000c;
            padding: 10px;
            border: 1px solid #d8000c;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }

        .footer {
            text-align: center;
            font-size: 14px;
            margin-top: 15px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login to Apex Assurance</h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="e.g. yourname@example.com" required />

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required />

        <input type="submit" value="Login" name="login" />
    </form>

    <div class="footer">
        Don't have an account? <a href="signup.php">Register here</a>
    </div>
</div>

</body>
</html>
