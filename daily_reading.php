<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "bible_reminder");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data (for demonstration, assuming user ID is 1)
$user_id = 1;
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

// Fetch the user's last reading
$last_reading = $conn->query("SELECT * FROM readings WHERE user_id=$user_id ORDER BY date DESC LIMIT 1")->fetch_assoc();

// Determine the next chapter to read
if ($last_reading) {
    // Move to the next chapter or book
    $book = $last_reading['book'];
    $chapter = $last_reading['chapter'] + 1;
} else {
    // Start from Genesis chapter 1
    $book = 'Genesis';
    $chapter = 1;
}

// Display the next reading
echo "Hello " . $user['name'] . "!<br>";
echo "Your next Bible reading is: " . $book . " Chapter " . $chapter . "<br>";
echo "Language: " . ($user['language'] == 'en' ? 'English' : 'Swahili');

// Insert today's reading into the readings table
$date = date('Y-m-d');
$conn->query("INSERT INTO readings (user_id, book, chapter, date) VALUES ($user_id, '$book', $chapter, '$date')");

$conn->close();
?>
