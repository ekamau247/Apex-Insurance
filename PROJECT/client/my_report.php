<?php
session_start();
require '../INCLUDES/db_connects.php';

// Ensure client is logged in
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_type']) !== 'client') {
    header("Location: ../login.php");
    exit;
}

$client_id = $_SESSION['user_id'];
$query = "SELECT * FROM accident_report WHERE user_id = '$client_id' ORDER BY accident_date DESC";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Accident Reports</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; padding: 20px; }
        .report-card {
            background: #fff;
            padding: 20px;
            border-left: 5px solid #28a745;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .media-preview img {
            max-height: 100px;
            margin: 5px;
            border-radius: 5px;
        }
        .media-preview video {
            max-width: 160px;
            margin: 5px;
            border-radius: 5px;
        }
        .media-preview a {
            display: block;
            margin: 5px;
            color: #007bff;
        }
    </style>
</head>
<body>
<div class="container">
    <h3 class="text-center mb-4">My Accident Reports</h3>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="report-card">
                <h5><strong>Report ID:</strong> <?= $row['Id'] ?></h5>
                <p><strong>Date:</strong> <?= $row['accident_date'] ?></p>
                <p><strong>Location:</strong> <?= $row['location'] ?></p>
                <p><strong>Description:</strong> <?= $row['description'] ?></p>
                <p><strong>Police Report:</strong> <?= $row['police_report_number'] ?> @ <?= $row['police_station'] ?></p>
                <p><strong>Other Parties:</strong> <?= $row['other_parties_involved'] ?></p>
                <p><strong>Witness Details:</strong> <?= $row['witness_details'] ?></p>

                <?php
                $report_id = isset($row['id']) ? $row['id'] : 0;
                $media_sql = "SELECT * FROM accident_media WHERE accident_report_id = $report_id";
                $media_q = mysqli_query($con, $media_sql);

                if (!$media_q) {
                    echo "<div class='alert alert-danger'>Media query failed: " . mysqli_error($con) . "</div>";
                }
                ?>

                <?php if ($media_q && mysqli_num_rows($media_q) > 0): ?>
                    <div class="media-preview">
                        <strong>Attached Media:</strong><br>
                        <?php while ($media = mysqli_fetch_assoc($media_q)): 
                            $media_url = "../" . $media['media_url'];
                            $type = $media['media_type'];
                        ?>
                            <?php if ($type === 'Photo'): ?>
                                <img src="<?= $media_url ?>" alt="photo">
                            <?php elseif ($type === 'Video'): ?>
                                <video src="<?= $media_url ?>" controls></video>
                            <?php elseif ($type === 'Document'): ?>
                                <a href="<?= $media_url ?>" target="_blank">📄 <?= basename($media_url) ?></a>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p><em>No media files attached or failed to load media.</em></p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">You have not submitted any accident reports yet.</div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
</body>
</html>
