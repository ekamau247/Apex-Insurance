<?php
require '../INCLUDES/db_connects.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=users.xls");
echo "ID\tFirst Name\tLast Name\tEmail\tPhone\tUser Type\tRegistered On\n";

$sql = "SELECT Id, first_name, last_name, email, phone_number, user_type, created_at FROM user";
$result = $con->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "{$row['Id']}\t{$row['first_name']}\t{$row['last_name']}\t{$row['email']}\t{$row['phone_number']}\t{$row['user_type']}\t{$row['created_at']}\n";
}
exit;
