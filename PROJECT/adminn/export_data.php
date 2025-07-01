<?php
require '../INCLUDES/db_connects.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="dashboard_stats.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Metric', 'Total']);

$metrics = [
    'Registered Users' => "SELECT COUNT(*) FROM user",
    'Total Policies'   => "SELECT COUNT(*) FROM policy",
    'Submitted Claims' => "SELECT COUNT(*) FROM claims",
    'Claim Payments'   => "SELECT COUNT(*) FROM claim_payment",
    'Accident Reports' => "SELECT COUNT(*) FROM accident_report"
];

foreach ($metrics as $label => $query) {
    $result = $con->query($query);
    $count = $result ? $result->fetch_row()[0] : 0;
    fputcsv($output, [$label, $count]);
}

fclose($output);
exit;
