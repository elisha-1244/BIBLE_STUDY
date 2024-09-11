<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Welcome to Your Dashboard</h2>
    <ul>
        <li><a href="assign_schedule.php?user_id=<?= $user_id; ?>">Assign Reading Schedule</a></li>
        <li><a href="today_reading.php?user_id=<?= $user_id; ?>">Today's Reading</a></li>
        <li><a href="feedback.php?user_id=<?= $user_id; ?>&type=weekly">Weekly Feedback</a></li>
        <li><a href="feedback.php?user_id=<?= $user_id; ?>&type=monthly">Monthly Feedback</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
