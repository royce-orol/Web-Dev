<?php
// Start the session
session_start();

// Include the database connection
include('../db_connection.php');



// Fetch the student ID from the session
$student_id = $_SESSION['user_id'];

// Query to get the proposal details (marks, project title, and student ID)
$query = "SELECT proposal.proposal_id, proposal.title, proposal.marks 
          FROM proposal 
          WHERE proposal.sender_id = ? AND proposal.status = 'approved'";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the data
$proposal = ($result->num_rows > 0) ? $result->fetch_assoc() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Final Year Project Marks</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>View Final Year Project Marks</h1>

            <?php if ($proposal): ?>
                <div class="marks-details">
                    <p><strong>Project Title:</strong> <?php echo htmlspecialchars($proposal['title']); ?></p>
                    <p><strong>Your Final Year Project Marks:</strong> <?php  echo htmlspecialchars($proposal['marks'] ?? 'Not yet assigned'); ?> / 100</p>
                </div>
            <?php else: ?>
                <p>No approved proposal found or marks not assigned yet.</p>
            <?php endif; ?>

        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
