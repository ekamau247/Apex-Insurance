<!DOCTYPE html>
<html>
<head><title>Reset Password</title></head>
<body>
<h2>Reset Your Password</h2>
<form method="POST">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>New Password:</label><br>
    <input type="password" name="new_password" required><br><br>

    <input type="submit" name="reset" value="Reset Password">
</form>

<?php
if (isset($_POST['reset'])) {
    $con = new mysqli("localhost", "root", "", "Apex");
    if ($con->connect_error) die("Connection failed: " . $con->connect_error);

    $email = $con->real_escape_string($_POST['email']);
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $sql = "SELECT * FROM user WHERE email = '$email'";
    $result = $con->query($sql);

    if ($result && $result->num_rows === 1) {
        $con->query("UPDATE user SET password = '$new_password' WHERE email = '$email'");
        echo "✅ Password reset successful! Redirecting to login page in 3 seconds...";
        header("refresh:3;url=login.php");
        exit();
    } else {
        echo "❌ No user found with that email.";
    }

    $con->close();
}
?>
</body>
</html>
