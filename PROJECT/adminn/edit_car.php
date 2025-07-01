<?php
require_once '../INCLUDES/db_connects.php';
session_start();

// Admin-only access
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch car details to edit
if (!isset($_GET['id'])) {
    die("Car ID is required.");
}

$car_id = intval($_GET['id']);
$stmt = $con->prepare("SELECT * FROM car WHERE Id = ?");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Car not found.");
}

$car = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $number_plate = $_POST['number_plate'];
    $color = $_POST['color'];
    $engine_number = $_POST['engine_number'];
    $chassis_number = $_POST['chassis_number'];
    $value = $_POST['value'];

    $update = $con->prepare("UPDATE car SET make=?, model=?, year=?, number_plate=?, color=?, engine_number=?, chassis_number=?, value=? WHERE Id=?");
    $update->bind_param("ssissssdi", $make, $model, $year, $number_plate, $color, $engine_number, $chassis_number, $value, $car_id);

    if ($update->execute()) {
        header("Location: view_cars.php?message=Car updated successfully");
        exit;
    } else {
        $error = "Update failed: " . $con->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Car</title>
    <style>
        label { display: block; margin-top: 10px; }
        input[type="text"], input[type="number"], input[type="year"] {
            width: 100%; padding: 8px; margin-top: 5px;
        }
        button {
            margin-top: 20px; padding: 10px 20px;
            background-color: #28a745; color: white; border: none; border-radius: 4px;
        }
    </style>
</head>
<body>
    <h2>Edit Car Details</h2>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Make: <input type="text" name="make" value="<?= htmlspecialchars($car['make']) ?>" required></label>
        <label>Model: <input type="text" name="model" value="<?= htmlspecialchars($car['model']) ?>" required></label>
        <label>Year: <input type="number" name="year" value="<?= htmlspecialchars($car['year']) ?>" required></label>
        <label>Number Plate: <input type="text" name="number_plate" value="<?= htmlspecialchars($car['number_plate']) ?>" required></label>
        <label>Color: <input type="text" name="color" value="<?= htmlspecialchars($car['color']) ?>"></label>
        <label>Engine Number: <input type="text" name="engine_number" value="<?= htmlspecialchars($car['engine_number']) ?>" required></label>
        <label>Chassis Number: <input type="text" name="chassis_number" value="<?= htmlspecialchars($car['chassis_number']) ?>" required></label>
        <label>Value (KES): <input type="number" step="0.01" name="value" value="<?= htmlspecialchars($car['value']) ?>"></label>

        <button type="submit">Update Car</button>
    </form>
</body>
</html>
