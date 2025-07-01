<?php
require '../INCLUDES/db_connects.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'] ?? 0;

    $policy_id = $_POST['policy_id'] ?? null;
    $car_id = $_POST['car_id'] ?? null;

    // Convert accident_date to MySQL datetime format (replace T with space)
    $accident_date_raw = $_POST['accident_date'] ?? '';
    $accident_date = str_replace("T", " ", $accident_date_raw);

    $location = $_POST['location'] ?? '';

    // Cast latitude and longitude to float or null if empty
    $latitude = isset($_POST['latitude']) && $_POST['latitude'] !== '' ? floatval($_POST['latitude']) : null;
    $longitude = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? floatval($_POST['longitude']) : null;

    $description = $_POST['description'] ?? '';
    $other_parties_involved = $_POST['other_parties_involved'] ?? '';
    $police_report_number = $_POST['police_report_number'] ?? '';
    $police_station = $_POST['police_station'] ?? '';
    $witness_details = $_POST['witness_details'] ?? '';

    $stmt = $con->prepare("INSERT INTO accident_report 
        (user_id, policy_id, car_id, accident_date, location, latitude, longitude, description, other_parties_involved, police_report_number, police_station, witness_details) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param(
            "iiissddsssss",
            $user_id,
            $policy_id,
            $car_id,
            $accident_date,
            $location,
            $latitude,
            $longitude,
            $description,
            $other_parties_involved,
            $police_report_number,
            $police_station,
            $witness_details
        );

        if ($stmt->execute()) {
            // Redirect to track_claim.php after successful insert
            header("Location: track_claim.php?success=1");
            exit();
        } else {
            $message = "Error saving accident report: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Error preparing statement: " . $con->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Accident Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f3;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 650px;
            margin: 30px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #bbb;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background: #1976d2;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .error {
            color: red;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Accident Report Form</h2>

    <?php if (!empty($message)): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Policy ID:</label>
        <input type="number" name="policy_id" required>

        <label>Car ID:</label>
        <input type="number" name="car_id" required>

        <label>Accident Date:</label>
        <input type="datetime-local" name="accident_date" required>

        <label>Location:</label>
        <input type="text" name="location" required>

        <label>Latitude:</label>
        <input type="text" name="latitude">

        <label>Longitude:</label>
        <input type="text" name="longitude">

        <label>Description:</label>
        <textarea name="description" required></textarea>

        <label>Other Parties Involved:</label>
        <textarea name="other_parties_involved"></textarea>

        <label>Police Report Number:</label>
        <input type="text" name="police_report_number">

        <label>Police Station:</label>
        <input type="text" name="police_station">

        <label>Witness Details:</label>
        <textarea name="witness_details"></textarea>

        <button type="submit">Submit Report</button>
    </form>
</div>
</body>
</html>
