<?php
session_start();
include '../db_connection.php'; // Ensure correct path for db_connection.php

// Check if the user is logged in and is a moderator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'moderator') {
    header("Location: login.php");
    exit;
}

// Retrieve feedback from the database
$feedbacks = [];
$query = "SELECT f.feedback_id, f.message, u.first_name AS sender_first_name, u.last_name AS sender_last_name, f.created_at 
          FROM feedback f 
          JOIN users u ON f.sender_id = u.id 
          ORDER BY f.created_at DESC";
$result = $conn->query($query);

// Fetch feedback records if available
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
    <style>
        .feedback-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }

        .feedback-tile {
            background-color: #f9f9f9;
            border-radius: 12px;
            padding: 15px;
            width: 300px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .feedback-tile:hover {
            transform: translateY(-5px);
        }

        .feedback-header {
            font-weight: bold;
            font-size: 16px;
            color: #333;
        }

        .feedback-message {
            margin-top: 10px;
            font-size: 14px;
            color: #555;
        }

        .feedback-date {
            margin-top: 10px;
            font-size: 12px;
            color: #888;
        }

        .feedback-table {
            display: none; /* Hide the original table */
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Feedback Records</h1>

            <?php if (!empty($feedbacks)): ?>
                <div class="feedback-container">
                    <?php foreach ($feedbacks as $feedback): ?>
                        <div class="feedback-tile">
                            <div class="feedback-header">
                                <?php echo htmlspecialchars($feedback['sender_first_name']) . ' ' . htmlspecialchars($feedback['sender_last_name']); ?>
                            </div>
                            <div class="feedback-message">
                                <?php echo htmlspecialchars($feedback['message']); ?>
                            </div>
                            <div class="feedback-date">
                                <?php 
                                // Convert timestamp to human-readable format
                                $formattedDate = date("d F Y", strtotime($feedback['created_at']));
                                echo htmlspecialchars($formattedDate);
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No feedback records available.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
