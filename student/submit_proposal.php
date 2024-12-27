<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Retrieve user details
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];

// Initialize success and error messages
$success_message = "";
$error_message = "";

include('../db_connection.php'); // Adjusted for consistent relative path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the submitted proposal data
    $proposal_title = trim($_POST['proposal_title']);
    $proposal_description = trim($_POST['proposal_description']);

    // Save proposal to the database
    if ($proposal_title && $proposal_description) {
        // Database logic for inserting the proposal
        $stmt = $conn->prepare("INSERT INTO proposal (sender_id, title, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $_SESSION['user_id'], $proposal_title, $proposal_description);

        if ($stmt->execute()) {
            $success_message = "Your proposal has been submitted successfully!";
        } else {
            $error_message = "Error submitting your proposal. Please try again.";
        }

        $stmt->close();
    } else {
        $error_message = "Please fill in all fields.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Proposal</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Submit Proposal</h1>

            <!-- Display success or error message -->
            <?php if (!empty($success_message)): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <!-- Proposal form -->
            <form method="POST">
                <label for="proposal_title">Proposal Title:</label>
                <input type="text" id="proposal_title" name="proposal_title" required>

                <label for="proposal_description">Proposal Description:</label>
                <textarea id="proposal_description" name="proposal_description" required></textarea>

                <button type="submit">Submit Proposal</button>
            </form>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>