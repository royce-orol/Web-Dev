<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Connect to the database
include('../db_connection.php');

// Get approved proposal for the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT proposal_id, title FROM proposal WHERE status = 'approved' AND sender_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$proposal = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Presentation</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>
        <div class="dashboard-main">
            <h1>Book Presentation</h1>

            <?php if ($proposal): ?>
                <form action="submit_presentation.php" method="POST">
                    <p><strong>Proposal:</strong> <?= htmlspecialchars($proposal['title']); ?></p>
                    <input type="hidden" name="proposal_id" value="<?= htmlspecialchars($proposal['proposal_id']); ?>">

                    <label for="date">Select Date:</label>
                    <input type="date" name="date" required>

                    <label for="time">Select Time:</label>
                    <input type="time" name="time" required>

                    <button type="submit">Book Presentation</button>
                </form>
            <?php else: ?>
                <p>No approved proposal is available for booking.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>
