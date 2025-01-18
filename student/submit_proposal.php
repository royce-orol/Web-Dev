<?php
session_start();
include('../db_connection.php');


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $proposal_title = $_POST['proposal_title'];
    $proposal_description = $_POST['proposal_description'];

    // Insert proposal into the database
    $stmt = $conn->prepare("INSERT INTO proposal (sender_id, title, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $_SESSION['user_id'], $proposal_title, $proposal_description);
    $stmt->execute();
    $stmt->close();
}
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

