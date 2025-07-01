<?php
require_once '../INCLUDES/db_connects.php';
require_once '../lib/tcpdf/tcpdf.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user accident reports
$sql = "SELECT ar.*, p.policy_number, c.plate_number 
        FROM accident_report ar
        JOIN policy p ON ar.policy_id = p.id
        JOIN car c ON ar.car_id = c.id
        WHERE ar.user_id = ?";
$stmt = $con->prepare($sql);

if (!$stmt) {
    die("Database prepare error: " . $con->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Create PDF
$pdf = new TCPDF();
$pdf->SetCreator('Apex Assurance');
$pdf->SetAuthor('Apex Assurance');
$pdf->SetTitle('Accident Reports');
$pdf->AddPage();

// Header
$html = '<h2 style="text-align:center;">Accident Reports</h2>';
$html .= '<p>Generated on: ' . date('Y-m-d H:i') . '</p>';

// Table
$html .= '
<table border="1" cellpadding="4">
    <thead>
        <tr style="background-color:#f2f2f2;">
            <th><b>ID</b></th>
            <th><b>Date</b></th>
            <th><b>Location</b></th>
            <th><b>Policy</b></th>
            <th><b>Car</b></th>
            <th><b>Status</b></th>
        </tr>
    </thead>
    <tbody>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
            <td>' . $row['Id'] . '</td>
            <td>' . $row['accident_date'] . '</td>
            <td>' . htmlspecialchars($row['location']) . '</td>
            <td>' . $row['policy_number'] . '</td>
            <td>' . $row['plate_number'] . '</td>
            <td>' . $row['status'] . '</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="6">No accident reports found.</td></tr>';
}

$html .= '</tbody></table>';

// Output
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('accident_reports.pdf', 'D'); // 'D' = Download
?>
