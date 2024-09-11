<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "bible_reminder");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = 1; // Assuming user ID is 1

// Fetch readings from the past month
$month_start = date('Y-m-d', strtotime('-30 days'));
$readings = $conn->query("SELECT * FROM readings WHERE user_id=$user_id AND date >= '$month_start'");

echo "Monthly Bible Reading Feedback:<br>";
if ($readings->num_rows > 0) {
    while ($row = $readings->fetch_assoc()) {
        echo "Book: " . $row['book'] . ", Chapter: " . $row['chapter'] . " (Date: " . $row['date'] . ")<br>";
    }
} else {
    echo "No readings found for the past month.";
}

$conn->close();
?>
