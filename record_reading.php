<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "bible_reminder");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $book = $_POST['book'];
    $chapter = 1;  // Start with chapter 1 (you can implement chapter selection later)
    $date = date('Y-m-d');

    // Insert reading record into the database
    $sql = "INSERT INTO readings (user_id, book, chapter, date) VALUES ($user_id, '$book', $chapter, '$date')";
    if ($conn->query($sql) === TRUE) {
        echo "Reading recorded successfully!";
        header("Location: feedback.php?user_id=" . $user_id);
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
