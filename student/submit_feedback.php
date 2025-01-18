<?php
session_start();

//Connect to database 
include('../db_connection.php');


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = $_SESSION['user_id'];
    $message = $_POST['message'];

    // Insert feedback into the database
    $stmt = $conn->prepare("INSERT INTO feedback (sender_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $sender_id, $message);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>
        <div class="dashboard-main">
            <h1>Submit Feedback</h1>
            <form method="POST">
                <label for="message">Your Feedback:</label>
                <textarea id="message" name="message" rows="5" required></textarea>
                <button type="submit">Submit Feedback</button>
            </form>
        </div>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>
