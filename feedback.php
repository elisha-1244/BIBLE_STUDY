<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "bible_reminder");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_GET['user_id']; // Validate and sanitize input
$feedback_type = isset($_GET['type']) ? $_GET['type'] : 'weekly'; // 'weekly' or 'monthly'

// Determine date range based on feedback type
if ($feedback_type == 'weekly') {
    $start_date = date('Y-m-d', strtotime('-7 days'));
    $title = "Weekly";
} else {
    $start_date = date('Y-m-d', strtotime('-30 days'));
    $title = "Monthly";
}

// Fetch readings within the date range
$sql = "SELECT book_id, COUNT(*) AS chapters_read FROM readings 
        WHERE user_id = $user_id AND date >= '$start_date' 
        GROUP BY book_id";
$result = $conn->query($sql);

// Prepare data for the chart
$books = [];
$chapters_read = [];
while ($row = $result->fetch_assoc()) {
    // Fetch book name
    $book = $conn->query("SELECT * FROM books WHERE id=" . $row['book_id'])->fetch_assoc();
    $book_name = ($conn->query("SELECT language FROM users WHERE id=$user_id")->fetch_assoc()['language'] == 'en') ? $book['name_en'] : $book['name_sw'];
    
    $books[] = $book_name;
    $chapters_read[] = $row['chapters_read'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title; ?> Feedback</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h2><?= $title; ?> Bible Reading Feedback</h2>
    <canvas id="readingChart" width="400" height="200"></canvas>
    
    <script>
        var ctx = document.getElementById('readingChart').getContext('2d');
        var readingChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($books); ?>,
                datasets: [{
                    label: 'Chapters Read',
                    data: <?= json_encode($chapters_read); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    </script>
    
    <div class="feedback-options">
        <a href="feedback.php?user_id=<?= $user_id; ?>&type=weekly">Weekly Feedback</a> |
        <a href="feedback.php?user_id=<?= $user_id; ?>&type=monthly">Monthly Feedback</a>
    </div>
</body>
</html>
