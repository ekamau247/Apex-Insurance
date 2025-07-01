<?php
session_start();
include('../INCLUDES/db_connects.php');

$client_id = $_SESSION['user_id']; // use the correct session key if different
$success = $error = '';

// Fetch accident reports for this client
$accident_result = mysqli_query($con, "SELECT id, accident_date FROM accident_report WHERE user_id = '$client_id'");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accident_id = mysqli_real_escape_string($con, $_POST['accident_id']);
    $details = mysqli_real_escape_string($con, $_POST['claim_details']);
    $claim_code = 'CLM' . strtoupper(substr(uniqid(), -6));
    $status = 'Pending';
    $date = date('Y-m-d');

    // Optional: Check if a claim already exists for this accident
    $check = mysqli_query($con, "SELECT id FROM claims WHERE accident_id = '$accident_id'");
    if (mysqli_num_rows($check) > 0) {
        $error = "You have already submitted a claim for this accident.";
    } else {
        $sql = "INSERT INTO claims (claim_code, accident_id, claim_details, claim_date, status, client_id)
                VALUES ('$claim_code', '$accident_id', '$details', '$date', '$status', '$client_id')";

        if (mysqli_query($con, $sql)) {
            $success = "Claim submitted successfully with Claim Code: <strong>$claim_code</strong>";
        } else {
            $error = "Error submitting claim: " . mysqli_error($con);
        }
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
            background: #f5f8fb;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            margin-top: 50px;
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        textarea {
            resize: vertical;
        }
    </style>
</head>
<body>
<div class="container">
    <h3 class="text-center mb-4">Submit a New Claim</h3>

    <?php if ($success): ?>
        <div class="alert alert-success text-center"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-group">
            <label for="accident_id">Select Accident Report</label>
            <select class="form-control" id="accident_id" name="accident_id" required>
                <option value="">-- Select Accident --</option>
                <?php while ($row = mysqli_fetch_assoc($accident_result)): ?>
                    <option value="<?= $row['id'] ?>">Accident #<?= $row['id'] ?> - <?= $row['accident_date'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="claim_details">Claim Details</label>
            <textarea class="form-control" id="claim_details" name="claim_details" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Submit Claim</button>
        <a href="dashboard.php" class="btn btn-secondary btn-block">Back to Dashboard</a>
    </form>
</div>
</body>
</html>
