<?php
session_start(); // Start the session
$servername = "localhost";//
$username = "root";
$password = "";
$dbname = "sampleDB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}