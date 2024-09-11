<?php
// Database connection (replace with your DB credentials)
$conn = new mysqli('localhost', 'root', '', 'bible_reminder');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all books from the database
$books_query = "SELECT id, name_en, name_sw FROM books";
$books = $conn->query($books_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $day = $_POST['day'];
    $book_id = $_POST['book'];
    $chapter = $_POST['chapter'];
    $verse_start = $_POST['verse_start'];
    $verse_end = $_POST['verse_end'];
    $language = $_POST['language'];
    $user_id = 1;  // Example user ID (you can fetch this dynamically based on logged-in user)

    // Check if the book ID exists in the books table
    $book_query = "SELECT COUNT(*) AS count FROM books WHERE id=?";
    $stmt = $conn->prepare($book_query);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();
    
    if ($book['count'] == 0) {
        die("Error: Book ID does not exist.");
    }

    // Insert the schedule into the database
    $stmt = $conn->prepare("INSERT INTO reading_schedule (user_id, day_of_week, book_id, chapter, verse_start, verse_end, language) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issiiis", $user_id, $day, $book_id, $chapter, $verse_start, $verse_end, $language);

    if ($stmt->execute()) {
        echo "Reading schedule assigned successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Reading Schedule</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Assign Bible Books to Days</h2>
    <form method="POST" action="">
        <label for="day">Select Day:</label>
        <select name="day" id="day" required>
            <option value="">--Select a Day--</option>
            <option value="Monday">Monday</option>
            <option value="Tuesday">Tuesday</option>
            <option value="Wednesday">Wednesday</option>
            <option value="Thursday">Thursday</option>
            <option value="Friday">Friday</option>
            <option value="Saturday">Saturday</option>
            <option value="Sunday">Sunday</option>
        </select><br><br>

        <label for="book">Select Book:</label>
        <select name="book" id="book" required>
            <option value="">--Select a Book--</option>
            <?php
            while($book = $books->fetch_assoc()) {
                // Default to English for the dropdown
                echo "<option value='" . $book['id'] . "'>" . htmlspecialchars($book['name_en']) . "</option>";
            }
            ?>
        </select><br><br>

        <label for="chapter">Chapter:</label>
        <input type="number" name="chapter" id="chapter" required><br><br>

        <label for="verse_start">Verse Start:</label>
        <input type="number" name="verse_start" id="verse_start" required><br><br>

        <label for="verse_end">Verse End:</label>
        <input type="number" name="verse_end" id="verse_end" required><br><br>

        <label for="language">Select Language:</label>
        <select name="language" id="language" required>
            <option value="en">English</option>
            <option value="sw">Swahili</option>
        </select><br><br>

        <input type="submit" value="Save Schedule">
    </form>
</body>
</html>
