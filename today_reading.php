<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'bible_reminder');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get today's day of the week
$today = date('l'); // Example: 'Monday'

// Fetch today's reading schedule
$user_id = 1;  // Example user ID
$sql = "SELECT b.name_en, b.name_sw, rs.chapter, rs.verse_start, rs.verse_end, rs.language
        FROM reading_schedule rs
        JOIN books b ON rs.book_id = b.id
        WHERE rs.user_id = ? AND rs.day_of_week = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $reading = $result->fetch_assoc();
    $book_name = ($reading['language'] == 'en') ? $reading['name_en'] : $reading['name_sw'];
    echo "<h2>Today's Reading</h2>";
    echo "<p><strong>Book:</strong> " . htmlspecialchars($book_name) . "</p>";
    echo "<p><strong>Chapter:</strong> " . htmlspecialchars($reading['chapter']) . "</p>";
    echo "<p><strong>Verses:</strong> " . htmlspecialchars($reading['verse_start']) . " - " . htmlspecialchars($reading['verse_end']) . "</p>";

    // Fetch and display the content of the reading
    function fetch_verses($book_name, $chapter, $verse_start, $verse_end) {
        // Replace with your actual API endpoint
        $api_url = "https://api.example.com/verses?book={$book_name}&chapter={$chapter}&start={$verse_start}&end={$verse_end}";
        
        $response = file_get_contents($api_url);
        return json_decode($response, true);
    }

    $verses = fetch_verses($book_name, $reading['chapter'], $reading['verse_start'], $reading['verse_end']);

    if ($verses) {
        echo "<h2>Reading Content</h2>";
        foreach ($verses as $verse) {
            echo "<p>" . htmlspecialchars($verse['text']) . "</p>";
        }
    } else {
        echo "<p>Unable to fetch reading content.</p>";
    }

} else {
    echo "<p>No reading assigned for today.</p>";
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Today's Reading</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Today's Reading: <?= htmlspecialchars($book_name); ?></h2>
    <?php if ($result->num_rows > 0): ?>
        <a href="read_book.php?user_id=<?= $user_id; ?>&book_id=<?= $book['id']; ?>">Read Now</a>
    <?php else: ?>
        <p>Please assign a book to read today.</p>
    <?php endif; ?>
</body>
</html>

<?php
$conn->close();
?>
