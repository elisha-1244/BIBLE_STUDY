<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "bible_reminder");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get parameters
$user_id = $_GET['user_id']; // Validate and sanitize
$book_id = $_GET['book_id']; // Validate and sanitize

// Fetch user's language preference
$user = $conn->query("SELECT language FROM users WHERE id=$user_id")->fetch_assoc();
$language = $user['language'];

// Fetch book name
$book = $conn->query("SELECT * FROM books WHERE id=$book_id")->fetch_assoc();
$book_name = ($language == 'en') ? $book['name_en'] : $book['name_sw'];

// Option 1: Fetch from local database
// Fetch all chapters and verses for the book
/*
$sql = "SELECT chapter, verse, text_" . $language . " AS text FROM bible_text WHERE book_id=$book_id ORDER BY chapter, verse";
$result = $conn->query($sql);
*/

// Option 2: Fetch from external API
// For demonstration, we'll fetch chapter 1
$chapter = isset($_GET['chapter']) ? intval($_GET['chapter']) : 1;

// Function to fetch Bible text via API
function fetch_bible_text($book, $chapter, $language) {
    // Adjust the API URL based on the language
    if ($language == 'en') {
        $url = "https://bible-api.com/" . urlencode("$book $chapter");
    } else {
        // Assuming Swahili API endpoint (replace with actual if available)
        // If no Swahili API is available, display a message or implement translation
        return "Swahili Bible text not available.";
    }

    $response = file_get_contents($url);
    if ($response === FALSE) {
        return "Error fetching Bible text.";
    }

    $data = json_decode($response, true);
    return nl2br(htmlspecialchars($data['text']));
}

$bible_text = fetch_bible_text($book_name, $chapter, $language);

// Optional: Determine total chapters (you might need to fetch this from a reference table or API)
$total_chapters = 50; // Example for Psalms; adjust accordingly

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Read <?= htmlspecialchars($book_name); ?> - Chapter <?= $chapter; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2><?= htmlspecialchars($book_name); ?> - Chapter <?= $chapter; ?></h2>
    <div class="bible-text">
        <?= $bible_text; ?>
    </div>
    
    <div class="navigation">
        <?php if ($chapter > 1): ?>
            <a href="read_book.php?user_id=<?= $user_id; ?>&book_id=<?= $book_id; ?>&chapter=<?= $chapter - 1; ?>">Previous Chapter</a>
        <?php endif; ?>
        
        <?php if ($chapter < $total_chapters): ?>
            <a href="read_book.php?user_id=<?= $user_id; ?>&book_id=<?= $book_id; ?>&chapter=<?= $chapter + 1; ?>">Next Chapter</a>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
