<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "sampleDB");
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed."]));
}
$query = "SELECT DATE(timestamp) as date, COUNT(*) as count FROM audit_logs WHERE action = 'Login' GROUP BY DATE(timestamp)";
$result = $conn->query($query);

$dates = [];
$counts = [];

while ($row = $result->fetch_assoc()) {
    $dates[] = $row["date"];
    $counts[] = $row["count"];
}

echo json_encode(["dates" => $dates, "counts" => $counts]);
$conn->close();
