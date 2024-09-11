<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "bible_reminder");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = 1; // Assuming user ID is 1

// Fetch readings from the past week
$week_start = date('Y-m-d', strtotime('-7 days'));
$readings = $conn->query("SELECT * FROM readings WHERE user_id=$user_id AND date >= '$week_start'");

echo "Weekly Bible Reading Feedback:<br>";
if ($readings->num_rows > 0) {
    while ($row = $readings->fetch_assoc()) {
        echo "Book: " . $row['book'] . ", Chapter: " . $row['chapter'] . " (Date: " . $row['date'] . ")<br>";
    }
} else {
    echo "No readings found for the past week.";
}

$conn->close();
?>
