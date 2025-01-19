<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Connect to the database
include('../db_connection.php');

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $proposal_id = $_POST['proposal_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Retrieve the student_id based on the proposal's sender_id
    $sql = "SELECT sender_id FROM proposal WHERE proposal_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $proposal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $proposal = $result->fetch_assoc();

    if ($proposal) {
        $student_id = $proposal['sender_id']; // Assume sender_id matches user_id from users table

        // Insert presentation booking into the database
        $sql = "INSERT INTO presentation (proposal_id, student_id, date, time) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $proposal_id, $student_id, $date, $time);

        if ($stmt->execute()) {
            $success_message = "Presentation booked successfully.";
        } else {
            $error_message = "Error booking the presentation. Please try again.";
        }
    } else {
        $error_message = "Invalid proposal selected.";
    }
}

// Get approved proposal for the logged-in user
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

            <?php if (isset($success_message)): ?>
                <p style="color: green;"><?= htmlspecialchars($success_message); ?></p>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <p style="color: red;"><?= htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <?php if ($proposal): ?>
                <form action="book_presentation.php" method="POST">
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
