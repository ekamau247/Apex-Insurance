<?php
$username = "root";
$servername = "localhost";
$password = "";
$databasename = "apex_db";

$con = new mysqli($servername, $username, $password, $databasename);

// Check connection and stop execution if it fails
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// No closing PHP tag at the end
