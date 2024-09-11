<?php
session_start();
// ... existing registration code ...

if ($conn->query($sql) === TRUE) {
    $user_id = $conn->insert_id;  // Get the newly registered user's ID
    $_SESSION['user_id'] = $user_id; // Set session

    // Redirect to book schedule assignment page
    header("Location: assign_schedule.php");
    exit();
} else {
    echo "Error: " . $conn->error;
}

// ... existing code ...
?>
