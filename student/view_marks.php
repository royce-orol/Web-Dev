<?php
// Start the session
session_start();

// Include the database connection
include('../db_connection.php');

// Check if user is logged in (and is a student, though not strictly enforced here but good practice)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Redirect to login if not logged in
    exit;
}

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

// Close statement and connection (good practice)
$stmt->close();
//$conn->close(); // Keep connection open for footer/header includes if needed, otherwise close it here.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Final Year Project Marks</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7f9; /* Light grey background */
            color: #333;
        }

        .dashboard-container {
            display: flex;
            flex-direction: row;
            margin: 0;
        }

        .dashboard-main {
            flex-grow: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center; /* Center content horizontally */
        }

        h1 {
            margin-bottom: 30px;
            color: #3498db; /* Blue heading color */
            text-align: center; /* Center heading text */
        }

        .marks-details {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); /* Soft shadow */
            text-align: center; /* Center text within marks details */
            width: 80%; /* Adjust width as needed */
            max-width: 600px; /* Maximum width for larger screens */
        }

        .marks-details p {
            margin-bottom: 15px;
            font-size: 1.1em;
        }

        .marks-details p:last-child {
            margin-bottom: 0; /* No margin for the last paragraph */
        }

        .marks-details strong {
            font-weight: bold;
            color: #2c3e50; /* Darker text for labels */
        }

        .marks-details .marks-value {
            font-size: 1.5em;
            color: #27ae60; /* Green color for marks value */
            font-weight: bold;
        }

        .no-marks-message {
            text-align: center;
            font-style: italic;
            color: #777;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05); /* Lighter shadow for message */
            width: 80%; /* Match width with marks-details */
            max-width: 600px;
            margin-top: 20px; /* Add some margin from the heading */
        }
    </style>
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
                    <p><strong>Your Final Year Project Marks:</strong> <span class="marks-value"><?php  echo htmlspecialchars($proposal['marks'] ?? 'Not yet assigned'); ?> / 100</span></p>
                </div>
            <?php else: ?>
                <p class="no-marks-message">No approved proposal found or marks not assigned yet.</p>
            <?php endif; ?>

        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>