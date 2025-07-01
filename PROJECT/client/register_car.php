<?php
session_start();
require '../INCLUDES/db_connects.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Client') {
    header("Location: ../login.php");
    exit;
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $year = $_POST['year'];
    $make = trim($_POST['make']);
    $model = trim($_POST['model']);
    $number_plate = strtoupper(trim($_POST['number_plate']));
    $color = trim($_POST['color']);
    $engine_number = trim($_POST['engine_number']);
    $chassis_number = trim($_POST['chassis_number']);
    $value = $_POST['value'];

    // Check if car with this number plate already exists for this user
    $check = $con->prepare("SELECT id FROM car WHERE user_id = ? AND number_plate = ?");
    $check->bind_param("is", $user_id, $number_plate);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "⚠️ You have already registered a car with this number plate.";
    } else {
        $stmt = $con->prepare("INSERT INTO car (user_id, year, make, model, number_plate, color, engine_number, chassis_number, value) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssssd", $user_id, $year, $make, $model, $number_plate, $color, $engine_number, $chassis_number, $value);

        if ($stmt->execute()) {
            $success = "✅ Car registered successfully!";
        } else {
            $error = "❌ Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Car</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            padding: 20px;
        }
        form {
            background: white;
            padding: 25px;
            border-radius: 10px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        label {
            font-weight: bold;
        }
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .submit-btn { background-color: #2ecc71; color: white; }
        .reset-btn { background-color: #e74c3c; color: white; }
        .back-btn  { background-color: #3498db; color: white; text-decoration: none; display: inline-block; padding: 10px 20px; border-radius: 6px; }
        .success { color: green; margin-bottom: 15px; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>

    <h2 style="text-align: center;">Register Your Car</h2>

    <form method="post">
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>

        <label>Year:</label>
        <input type="number" name="year" required min="1990" max="2099">

        <label>Make:</label>
        <input type="text" name="make" required>

        <label>Model:</label>
        <input type="text" name="model" required>

        <label>Number Plate:</label>
        <input type="text" name="number_plate" required>

        <label>Color:</label>
        <input type="text" name="color">

        <label>Engine Number:</label>
        <input type="text" name="engine_number" required>

        <label>Chassis Number:</label>
        <input type="text" name="chassis_number" required>

        <label>Estimated Value (Ksh):</label>
        <input type="number" step="0.01" name="value">

        <div class="btn-group">
            <button type="submit" class="submit-btn">Submit</button>
            <button type="reset" class="reset-btn">Reset</button>
            <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
        </div>
    </form>

</body>
</html>
