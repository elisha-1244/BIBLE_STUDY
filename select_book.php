<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "bible_reminder");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user data
$user_id = $_GET['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();
$language = $user['language'];

// Fetch Bible books in the selected language
$books = $conn->query("SELECT * FROM books");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select a Book to Read</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Select a Book to Read</h2>
    <form action="record_reading.php" method="POST">
        <input type="hidden" name="user_id" value="<?= $user_id; ?>">
        <label for="book">Choose a Book:</label>
        <select name="book" id="book">
            <?php while($row = $books->fetch_assoc()) {
                $book_name = $language == 'en' ? $row['name_en'] : $row['name_sw'];
                echo "<option value='" . $book_name . "'>" . $book_name . "</option>";
            } ?>
        </select><br><br>

        <input type="submit" value="Read">
    </form>
</body>
</html>
<?php
$conn->close();
?>
