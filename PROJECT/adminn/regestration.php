<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
</head>
<body>
    <h2>Register</h2>
    <form method="POST" action="">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        
        <input type="submit" name="register" value="Register">
    </form>

    <?php
    if (isset($_POST['register'])) {
        // Database connection
        $con = new mysqli("localhost", "root", "", "Apex");

        if ($con->connect_error) {
            die("Connection failed: " . $con->connect_error);
        }

        // Get form values
        $username = $con->real_escape_string($_POST['username']);
        $email = $con->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        

        // Insert into database
        $sql = "INSERT INTO user (username, email, password) VALUES ('$username', '$email', '$password' )";

       if ($con->query($sql) === TRUE) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $con->error;
        }

        $con->close();
    }
    ?>
</body>
</html>
