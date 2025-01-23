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
    <style>
        .feedback-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .feedback-container h1 {
            color: #4b3d2a;
            margin-bottom: 20px;
            text-align: center;
        }

        .feedback-container label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #4b3d2a;
        }

        .feedback-container textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #e0c097;
            border-radius: 5px;
            background-color: #fff7e8;
            color: #4b3d2a;
            resize: vertical;
        }

        .feedback-container textarea:focus {
            border-color: #8b5e3c;
            outline: none;
            box-shadow: 0 0 5px rgba(139, 94, 60, 0.5);
        }

        .feedback-container button {
            background-color: #e0c097;
            color: #4b3d2a;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
            display: block;
            margin: 20px auto 0;
        }

        .feedback-container button:hover {
            background-color: #d2a87c;
        }

        .feedback-container .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .feedback-container .success {
            background-color: #e8f5e9;
            color: #4caf50;
            border: 1px solid #4caf50;
        }

        .feedback-container .error {
            background-color: #fdecea;
            color: #f44336;
            border: 1px solid #f44336;
        }
    </style>
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
