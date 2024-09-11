<?php
// This script should be run daily via a cron job

// Database connection
$conn = new mysqli("localhost", "root", "", "bible_reminder");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Determine today's day
$today = date('l'); // e.g., 'Monday'

// Fetch all users
$users = $conn->query("SELECT * FROM users");

while ($user = $users->fetch_assoc()) {
    $user_id = $user['id'];
    $language = $user['language'];
    $email = $user['email'];

    // Fetch the assigned book for today
    $sql = "SELECT books.* FROM reading_schedule 
            JOIN books ON reading_schedule.book_id = books.id 
            WHERE reading_schedule.user_id = $user_id AND reading_schedule.day_of_week = '$today'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
        $book_name = ($language == 'en') ? $book['name_en'] : $book['name_sw'];
        $subject = "Today's Bible Reading Reminder";
        $message = "Hello " . $user['name'] . ",\n\nYour reading for today (" . $today . ") is: " . $book_name . ".\n\nHappy Reading!";
        $headers = "From: no-reply@yourdomain.com";

        // Send email
        mail($email, $subject, $message, $headers);
    }
}

$conn->close();
?>
