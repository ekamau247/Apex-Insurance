<?php
session_start();
require_once 'INCLUDES.PHP/db_connects.php';

// Ensure only logged-in Policy Holders can access
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Policyholder') {
    header("Location:claim.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST['accident_date'];
    $desc = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    if (!empty($date) && !empty($desc)) {
        $stmt = $con->prepare("INSERT INTO claims (policyholder_id, accident_date, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $date, $desc);
        
        if ($stmt->execute()) {
            $message = "✅ Claim submitted successfully!";
        } else {
            $message = "❌ Error submitting claim: " . $con->error;
        }
    } else {
        $message = "❌ All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>File New Claim</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f4f4f4; }
        .form-container { background: #fff; padding: 20px; max-width: 500px; margin: auto; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        label { display: block; margin-top: 10px; }
        input[type="date"], textarea, input[type="submit"] {
            width: 100%; padding: 10px; margin-top: 5px; border-radius: 4px; border: 1px solid #ccc;
        }
        input[type="submit"] {
            background-color: #007bff; color: white; cursor: pointer; margin-top: 15px;
        }
        input[type="submit"]:hover { background-color: #0056b3; }
        .message { text-align: center; margin-top: 15px; color: green; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>File New Claim</h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="accident_date">Date of Accident:</label>
        <input type="date" name="accident_date" required>

        <label for="description">Description of Incident:</label>
        <textarea name="description" rows="5" required></textarea>

        <input type="submit" value="Submit Claim">
    </form>
</div>

</body>
</html>
