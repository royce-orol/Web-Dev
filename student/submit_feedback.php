<?php
session_start();

// Connect to database 
include('../db_connection.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = $_SESSION['user_id'];
    $message = trim($_POST['message']);

    if (!empty($message)) {
        // Insert feedback into the database
        $stmt = $conn->prepare("INSERT INTO feedback (sender_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $sender_id, $message);
        if ($stmt->execute()) {
            $success_message = "Thank you for your feedback!";
        } else {
            $error_message = "An error occurred while submitting your feedback. Please try again.";
        }
        $stmt->close();
    } else {
        $error_message = "Feedback message cannot be empty.";
    }
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
    <link rel="stylesheet" href="../css/submit_feedback.css">
    
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>
        <div class="dashboard-main">
            <div class="feedback-container">
                <h1>Submit Feedback</h1>

                <?php if (!empty($success_message)): ?>
                    <div class="message success"> <?php echo htmlspecialchars($success_message); ?> </div>
                <?php elseif (!empty($error_message)): ?>
                    <div class="message error"> <?php echo htmlspecialchars($error_message); ?> </div>
                <?php endif; ?>

                <form method="POST">
                    <label for="message">Your Feedback:</label>
                    <textarea id="message" name="message" rows="5" placeholder="Write your feedback here..." required></textarea>
                    <button type="submit">Submit Feedback</button>
                </form>
            </div>
        </div>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>
