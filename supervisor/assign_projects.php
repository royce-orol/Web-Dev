<?php
session_start();
include('../db_connection.php'); // Include database connection

// Check if the logged-in user is a supervisor
if ($_SESSION['role'] !== 'supervisor') {
    header("Location: ../login.php"); // Redirect if not a supervisor
    exit;
}

// Get supervisor's ID from session
$supervisor_id = $_SESSION['user_id'];

// Handle the proposal assignment
if (isset($_POST['assign_proposal_id'])) {
    $proposal_id = intval($_POST['assign_proposal_id']); // Get proposal ID

    // Begin transaction
    $conn->begin_transaction();

    // Try to assign proposal and update status
    try {
        // Assign the supervisor to the proposal
        $stmt = $conn->prepare("UPDATE proposal SET assigned_sv = ? WHERE proposal_id = ?");
        $stmt->bind_param('ii', $supervisor_id, $proposal_id);
        $stmt->execute();

        // Update proposal status to 'approved'
        $stmt_status = $conn->prepare("UPDATE proposal SET status = 'approved' WHERE proposal_id = ?");
        $stmt_status->bind_param('i', $proposal_id);
        $stmt_status->execute();

        // Commit the transaction if everything went well
        $conn->commit();
        $success_message = "Proposal successfully assigned and status updated to approved.";
    } catch (Exception $e) {
        // Rollback if an error occurs
        $conn->rollback();
        $error_message = "Error: " . $e->getMessage();
    }
}

// Fetch all pending proposals
$result = $conn->query("SELECT proposal_id, title, description, status FROM proposal WHERE status = 'pending'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Project</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: black;
        }

        .dashboard-container {
            display: flex;
            height: 100vh;
            background-color: #fff;
            padding-left: 0;
        }

        .dashboard-main {
            flex-grow: 1;
            padding: 20px;
            margin-left: 0;
        }

        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
            padding-left: 20px;  /* Align the header text to the left */
        }

        .success-message {
            color: green;
            background-color: #d4edda;
            padding: 10px;
            border-radius: 5px;
            margin-left: 20px;
            margin-bottom: 10px; /* Add margin bottom for spacing */
            display: block; /* Ensure it takes full width */
        }

        .error-message {
            color: red;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin-left: 20px;
            margin-bottom: 10px; /* Add margin bottom for spacing */
            display: block; /* Ensure it takes full width */
        }

        .table-container {
            width: 100%;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-left: 20px;  /* Align the table container to the left */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
        }

        th, td {
            padding: 12px 15px; /* Increased padding for better spacing */
            text-align: left;
            border-bottom: 1px solid #ddd; /* Lighter border */
        }

        th {
            background-color: #f4f4f4; /* Light grey header */
            color: #333; /* Darker header text */
            font-weight: bold;
            text-transform: uppercase; /* Optional: Uppercase headers */
            letter-spacing: 0.6px; /* Optional: Letter spacing */
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9; /* Very light grey for even rows */
        }

        tbody tr:hover {
            background-color: #e6e6e6; /* Light grey on hover */
        }

        td:last-child {
            text-align: center; /* Center action buttons in the last column */
        }

        .action-button {
            background-color: #007bff; /* Blue button color */
            color: white;
            padding: 10px 15px; /* Slightly larger buttons */
            border: none;
            cursor: pointer;
            border-radius: 5px; /* Rounded corners */
            font-size: 0.9em;
            font-weight: bold; /* Bold button text */
            letter-spacing: 0.5px; /* Letter spacing for buttons */
            text-transform: uppercase; /* Uppercase button text */
        }

        .action-button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .action-button:focus {
            outline: none; /* Remove default focus outline */
            box-shadow: 0 0 0 2px rgba(0,123,255,.5); /* Add focus indication */
        }

        td button {
            padding: 8px 16px;
            font-size: 14px; /* Keep button font size consistent */
        }

        /* Responsive design adjustments for smaller screens */
        @media (max-width: 768px) {
            .dashboard-main {
                margin-left: 0;
                padding: 10px;
            }

            h1 {
                font-size: 22px; /* Slightly smaller header on smaller screens */
                padding-left: 10px; /* Adjust header padding */
            }

            .success-message, .error-message, .table-container {
                margin-left: 10px; /* Adjust message and table margins */
                margin-right: 10px;
            }

            table {
                font-size: 14px; /* Smaller font size for table text */
            }

            th, td {
                padding: 10px; /* Adjust cell padding */
            }

            .action-button {
                padding: 7px 12px; /* Slightly smaller buttons on smaller screens */
                font-size: 0.8em;
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?> <!-- Include header -->

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?> <!-- Include sidebar -->

        <div class="dashboard-main">
            <h1>Assign Project</h1>

            <!-- Display success or error message -->
            <?php if (!empty($success_message)) echo "<p class='success-message'>$success_message</p>"; ?>
            <?php if (!empty($error_message)) echo "<p class='error-message'>$error_message</p>"; ?>

            <!-- Display table of pending proposals -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Proposal ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['proposal_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td class="action-cell"> <!-- Added class for action cell if needed -->
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="assign_proposal_id" value="<?php echo htmlspecialchars($row['proposal_id']); ?>">
                                            <button type="submit" class="action-button">Assign</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5">No pending proposals found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?> <!-- Include footer -->
</body>
</html>