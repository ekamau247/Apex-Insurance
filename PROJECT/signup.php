<?php
require_once 'INCLUDES/db_connects.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $First_Name = trim($_POST['First_Name']);
    $second_name = trim($_POST['Second_Name']);
    $last_name = trim($_POST['Last_Name']);
    $Phone_number = trim($_POST['Phone_Number']);
    $Email = trim($_POST['email']);
    $Password = password_hash($_POST['Password'], PASSWORD_DEFAULT);
    $Gender = $_POST['Gender'];
    $National_Id = trim($_POST['National_Id']);
    $User_type = $_POST['User_type'];

    if ($con) {
        try {
            $stmt = $con->prepare("INSERT INTO user 
                (First_Name, second_name, Last_Name, Phone_number, email, password, Gender, National_id, user_type)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("sssssssss", $First_Name, $second_name, $last_name, $Phone_number, $Email, $Password, $Gender, $National_Id, $User_type);

            if ($stmt->execute()) {
                $message = "<p style='color:green;'>✅ Registration successful!</p>";
            } else {
                $message = "<p style='color:red;'>❌ Failed to register user.</p>";
            }

            $stmt->close();
        } catch (Exception $e) {
            $message = "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
        }
    } else {
        $message = "<p style='color:red;'>❌ Database connection error.</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Signup</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      padding: 20px;
    }

    .form-container {
      background: white;
      padding: 30px;
      margin: auto;
      width: 400px;
      box-shadow: 0 0 10px #ccc;
      border-radius: 10px;
    }

    h2 {
      text-align: center;
      color: darkblue;
    }

    label {
      display: block;
      margin: 10px 0 5px;
    }

    input, select {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    button {
      width: 100%;
      padding: 10px;
      background-color: darkblue;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
    }

    .message {
      text-align: center;
      margin-bottom: 15px;
    }

    .link {
      text-align: center;
      margin-top: 10px;
    }

    .link a {
      color: darkblue;
    }
  </style>
</head>
<body>

<div class="form-container">
  <h2>Sign Up</h2>

  <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>

  <form method="POST" action="">
    <label>First Name</label>
    <input type="text" name="First_Name" required>

    <label>Second Name</label>
    <input type="text" name="Second_Name" required>

    <label>Last Name</label>
    <input type="text" name="Last_Name" required>

    <label>Phone Number</label>
    <input type="text" name="Phone_Number" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="Password" required>

    <label>Gender</label>
    <select name="Gender" required>
        <option value="">--Select--</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Others">Others</option>
    </select>

    <label>National ID</label>
    <input type="text" name="National_Id" required>

    <label>User Type</label>
    <select name="User_type" required>
        <option value="">--Select--</option>
        <option value="Client">Client</option>
        <option value="Adjuster">Adjuster</option>
        <option value="Policyholder">Policy Holder</option>
        <option value="RepairCenter">Repair Center</option>
        <option value="EmergencyService">Emergency Service</option>
          <option value="adminn">ADMIN</option>

    </select>

    <button type="submit">Register</button>
  </form>

  <div class="link">
    Already have an account? <a href="login.php">Login</a>
  </div>
</div>

</body>
</html>
