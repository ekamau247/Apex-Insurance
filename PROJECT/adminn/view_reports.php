<?php
session_start();
require '../INCLUDES/db_connects.php';

// Admin only
if (!isset($_SESSION['user_type']) || strtolower($_SESSION['user_type']) !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Search handling
$search = $_GET['search'] ?? '';
$search_sql = "";
if (!empty($search)) {
    $search = mysqli_real_escape_string($con, $search);
    $search_sql = " AND (
        u.first_name LIKE '%$search%' OR 
        u.last_name LIKE '%$search%' OR 
        ar.location LIKE '%$search%' OR 
        ar.police_report_number LIKE '%$search%'
    )";
}

$query = "
    SELECT ar.id, ar.user_id, ar.accident_date, ar.location, ar.description, ar.police_report_number, 
           ar.police_station, ar.other_parties_involved, ar.witness_details, 
           u.first_name, u.last_name 
    FROM accident_report ar 
    JOIN user u ON ar.user_id = u.id 
    WHERE 1 $search_sql
    ORDER BY ar.accident_date DESC";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Accident Reports</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f5f7fa; padding: 30px; }
        .report-card {
            background: #fff;
            border-left: 6px solid #007bff;
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.05);
        }
        .media-preview img, .media-preview video {
            max-height: 120px;
            margin: 5px;
            border-radius: 6px;
        }
        .media-preview a {
            display: inline-block;
            margin: 6px;
            color: #007bff;
        }
        .report-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #343a40;
        }
        .section-title {
            font-weight: 500;
            margin-top: 15px;
            color: #495057;
        }
        .search-box {
            margin-bottom: 25px;
        }
        .btn-danger {
            float: right;
        }
    </style>
    <script>
        function confirmDelete(reportId) {
            if (confirm("Are you sure you want to delete this report and all its media?")) {
                window.location.href = "delete_report.php?id=" + reportId;
            }
        }
    </script>
</head>
<body>
<div class="container">
    <h3 class="text-center mb-4">📋 Accident Reports</h3>

    <form method="GET" class="form-inline justify-content-center search-box">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search by name, report #, location" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary">Search</button>
        <?php if (!empty($search)): ?>
            <a href="view_reports.php" class="btn btn-outline-secondary ml-2">Clear</a>
        <?php endif; ?>
    </form>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <?php $report_id = $row['id']; ?>
            <div class="report-card">
                <div class="report-title">
                    Report #<?= $report_id ?> | <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?>
                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $report_id ?>)">Delete</button>
                </div>
                <p><strong>Date:</strong> <?= $row['accident_date'] ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($row['description'])) ?></p>
                <p><strong>Police Report:</strong> <?= htmlspecialchars($row['police_report_number']) ?> @ <?= htmlspecialchars($row['police_station']) ?></p>
                <p><strong>Other Parties:</strong> <?= nl2br(htmlspecialchars($row['other_parties_involved'])) ?></p>
                <p><strong>Witness Details:</strong> <?= nl2br(htmlspecialchars($row['witness_details'])) ?></p>

                <div class="section-title">📎 Attached Media:</div>
                <?php
                $media_q = mysqli_query($con, "SELECT * FROM accident_media WHERE accident_report_id = '$report_id'");
                ?>

                <?php if (mysqli_num_rows($media_q) > 0): ?>
                    <div class="media-preview">
                        <?php while ($media = mysqli_fetch_assoc($media_q)):
                            $media_url = "../" . $media['media_url'];
                            $desc = htmlspecialchars($media['description']);
                            $type = $media['media_type'];
                        ?>
                            <?php if ($type === 'Photo'): ?>
                                <div>
                                    <img src="<?= $media_url ?>" alt="<?= $desc ?>" title="<?= $desc ?>">
                                    <a href="<?= $media_url ?>" download class="btn btn-sm btn-outline-primary">Download</a>
                                </div>
                            <?php elseif ($type === 'Video'): ?>
                                <div>
                                    <video src="<?= $media_url ?>" controls title="<?= $desc ?>"></video>
                                    <a href="<?= $media_url ?>" download class="btn btn-sm btn-outline-primary">Download</a>
                                </div>
                            <?php elseif ($type === 'Document'): ?>
                                <div>
                                    <a href="<?= $media_url ?>" target="_blank">📄 <?= basename($media_url) ?> (<?= $desc ?>)</a>
                                    <a href="<?= $media_url ?>" download class="btn btn-sm btn-outline-primary">Download</a>
                                </div>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p><em>No media files uploaded.</em></p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info">No reports found.</div>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary mt-4">⬅️ Back to Dashboard</a>
</div>
</body>
</html>
