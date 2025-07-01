<?php
session_start();
require '../INCLUDES/db_connects.php';

$client_id = $_SESSION['user_id'];
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accident_date = mysqli_real_escape_string($con, $_POST['accident_date']);
    $location = mysqli_real_escape_string($con, $_POST['location']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $police_report_number = mysqli_real_escape_string($con, $_POST['police_report_number']);
    $police_station = mysqli_real_escape_string($con, $_POST['police_station']);
    $other_parties = mysqli_real_escape_string($con, $_POST['other_parties'] ?? '');
    $witness_details = mysqli_real_escape_string($con, $_POST['witness_details'] ?? '');

    $check = mysqli_query($con, "SELECT id FROM accident_report 
        WHERE accident_date = '$accident_date' AND location = '$location' AND user_id = '$client_id'");
    
    if (mysqli_num_rows($check) > 0) {
        $error = "Duplicate accident report exists.";
    } else {
        $insert = "INSERT INTO accident_report 
            (user_id, accident_date, location, description, police_report_number, police_station, other_parties_involved, witness_details) 
            VALUES ('$client_id', '$accident_date', '$location', '$description', '$police_report_number', '$police_station', '$other_parties', '$witness_details')";

        if (mysqli_query($con, $insert)) {
            $accident_id = mysqli_insert_id($con);

            if (!empty($_FILES['attachments']['name'][0])) {
                $uploadDir = '../upload/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                foreach ($_FILES['attachments']['name'] as $key => $fileName) {
                    $tmpName = $_FILES['attachments']['tmp_name'][$key];
                    $fileSize = $_FILES['attachments']['size'][$key];
                    $desc = mysqli_real_escape_string($con, $_POST['descriptions'][$key]);

                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'mp4', 'mov', 'avi'];

                    if (!in_array($ext, $allowed)) continue;
                    if ($fileSize > 5 * 1024 * 1024) continue;

                    $media_type = 'Document';
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) $media_type = 'Photo';
                    elseif (in_array($ext, ['mp4', 'mov', 'avi'])) $media_type = 'Video';

                    $newName = uniqid() . '_' . basename($fileName);
                    $targetPath = $uploadDir . $newName;

                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $media_url = 'upload/' . $newName;
                        mysqli_query($con, "INSERT INTO accident_media 
                            (accident_report_id, media_type, media_url, description)
                            VALUES ('$accident_id', '$media_type', '$media_url', '$desc')");
                    }
                }
            }

            $success = "Report and files submitted successfully!";
        } else {
            $error = "Error submitting: " . mysqli_error($con);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Accident Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container { max-width: 900px; margin-top: 30px; background: #fff; padding: 30px; border-radius: 15px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .preview-img { height: 100px; margin-right: 10px; }
        .remove-btn { cursor: pointer; color: red; font-weight: bold; }
        .file-block { border: 1px solid #ddd; padding: 10px; border-radius: 8px; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h3 class="text-center mb-4">Submit Accident Report</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Date of Accident</label>
            <input type="datetime-local" name="accident_date" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Accident Location</label>
            <input type="text" name="location" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label>Police Report Number</label>
            <input type="text" name="police_report_number" class="form-control">
        </div>
        <div class="form-group">
            <label>Police Station</label>
            <input type="text" name="police_station" class="form-control">
        </div>
        <div class="form-group">
            <label>Other Parties Involved</label>
            <textarea name="other_parties" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label>Witness Details</label>
            <textarea name="witness_details" class="form-control"></textarea>
        </div>

        <label><strong>Attachments</strong> (Photos, Videos, Documents)</label>
        <div id="fileUploads">
            <div class="file-block">
                <input type="file" name="attachments[]" class="form-control-file file-input" required>
                <input type="text" name="descriptions[]" class="form-control mt-2" placeholder="Description (optional)">
                <div class="preview mt-2"></div>
                <span class="remove-btn" onclick="removeFile(this)">Remove</span>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-secondary mb-3" onclick="addMore()">+ Add More</button>

        <button type="submit" class="btn btn-primary btn-block">Submit Report</button>
        <a href="dashboard.php" class="btn btn-secondary btn-block">Back to Dashboard</a>
    </form>
</div>

<script>
    function addMore() {
        const block = document.createElement('div');
        block.className = 'file-block';
        block.innerHTML = `
            <input type="file" name="attachments[]" class="form-control-file file-input" required>
            <input type="text" name="descriptions[]" class="form-control mt-2" placeholder="Description (optional)">
            <div class="preview mt-2"></div>
            <span class="remove-btn" onclick="removeFile(this)">Remove</span>
        `;
        document.getElementById('fileUploads').appendChild(block);
    }

    function removeFile(btn) {
        btn.parentElement.remove();
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('file-input')) {
            const file = e.target.files[0];
            const previewDiv = e.target.parentElement.querySelector('.preview');
            previewDiv.innerHTML = '';

            if (!file) return;

            const ext = file.name.split('.').pop().toLowerCase();
            if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                const img = document.createElement('img');
                img.className = 'preview-img';
                img.src = URL.createObjectURL(file);
                previewDiv.appendChild(img);
            } else if (['pdf', 'doc', 'docx'].includes(ext)) {
                const doc = document.createElement('span');
                doc.innerHTML = `<strong>Document:</strong> ${file.name}`;
                previewDiv.appendChild(doc);
            } else if (['mp4', 'mov', 'avi'].includes(ext)) {
                const vid = document.createElement('video');
                vid.src = URL.createObjectURL(file);
                vid.controls = true;
                vid.width = 150;
                previewDiv.appendChild(vid);
            }
        }
    });
</script>
</body>
</html>
