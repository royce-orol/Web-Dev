<?php
session_start();
include('../db_connection.php'); // Adjusted for consistent relative path

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve sender's ID from the session
    $sender_id = $_SESSION['user_id'];
    // Retrieve and sanitize the feedback message
    $message = htmlspecialchars(trim($_POST['message']));

    // Validate input
    if (!empty($message)) {
        // Use prepared statements to avoid SQL injection
        $stmt = $conn->prepare("INSERT INTO feedback (sender_id, message) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("is", $sender_id, $message);

            if ($stmt->execute()) {
                $success_message = "Feedback submitted successfully!";
            } else {
                $error_message = "Error submitting feedback: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $error_message = "Error preparing the statement: " . $conn->error;
        }
    } else {
        $error_message = "Feedback message cannot be empty!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback</title>
    <link rel="stylesheet" href="../css/feedback.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/sidebar.php'; ?>

    <div class="feedback-container">
        <h1>Submit Feedback</h1>
        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
        <?php elseif (!empty($error_message)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="message">Your Feedback:</label>
            <textarea id="message" name="message" rows="5" required></textarea>
            <button type="submit">Submit</button>
        </form>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>