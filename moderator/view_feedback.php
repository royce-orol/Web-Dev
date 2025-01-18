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
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Feedback Records</h1>

            <?php if (!empty($feedbacks)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Sender</th>
                            <th>Message</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feedbacks as $feedback): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($feedback['sender_first_name']) . ' ' . htmlspecialchars($feedback['sender_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['message']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No feedback records available.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
