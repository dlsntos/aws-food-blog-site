<?php
session_start(); // Start the session
$servername = "cloudcomfinaldb.czcwm0o0uhok.ap-southeast-1.rds.amazonaws.com";//
$username = "FINALadmin";
$password = "cloudcomfinalpassword";
$dbname = "cloudcomfinaldb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}