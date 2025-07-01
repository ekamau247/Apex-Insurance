<?php
// submit_claim.php
session_start();
include('../INCLUDES/db_connects.php');


// Assuming session client_id is set. Fallback to 1 for testing.
$client_id = isset($_SESSION['client_id']) ? $_SESSION['client_id'] : 1;

$message = '';
$claim_code = '';

// Replace with actual client ID from session if login is implemented
$client_id = isset($_SESSION['client_id']) ? $_SESSION['client_id'] : 1;

$message = '';
$claim_code = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accident_id = mysqli_real_escape_string($conn, $_POST['accident_id']);
    $claim_details = mysqli_real_escape_string($conn, $_POST['claim_details']);
    $date = date("Y-m-d");

    // Insert claim first to get auto-incremented ID
    $insert_sql = "INSERT INTO claims (client_id, accident_id, claim_details, claim_date, status) 
                   VALUES ('$client_id', '$accident_id', '$claim_details', '$date', 'Pending')";

    if (mysqli_query($conn, $insert_sql)) {
        $claim_id = mysqli_insert_id($conn);
        $claim_code = "CLM" . date("Ymd") . str_pad($claim_id, 3, '0', STR_PAD_LEFT);

        $update_sql = "UPDATE claims SET claim_code='$claim_code' WHERE id='$claim_id'";
        mysqli_query($conn, $update_sql);

        $message = "✅ Claim submitted successfully. Your Claim Code is: <strong>$claim_code</strong>";
    } else {
        $message = "❌ Error submitting claim: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Claim - Apex Assurance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            max-width: 600px;
            margin-top: 40px;
            padding: 30px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: none;
        }
        .btn-primary {
            border-radius: 30px;
        }
        .message-box {
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <h3 class="mb-4 text-center">Submit a New Claim</h3>

    <?php if ($message): ?>
        <div class="alert alert-info message-box"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" action="submit_claim.php">
        <div class="form-group">
            <label for="accident_id">Accident ID</label>
            <input type="text" class="form-control" id="accident_id" name="accident_id" required>
        </div>
        <div class="form-group">
            <label for="claim_details">Claim Details</label>
            <textarea class="form-control" id="claim_details" name="claim_details" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Submit Claim</button>
    </form>
</div>
</body>
</html>
