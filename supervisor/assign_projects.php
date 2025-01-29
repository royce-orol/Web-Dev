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
        $stmt_status = $conn->prepare("UPDATE proposal SET status = 'assigned' WHERE proposal_id = ?");
        $stmt_status->bind_param('i', $proposal_id);
        $stmt_status->execute();

        // Commit the transaction if everything went well
        $conn->commit();
        $success_message = "Proposal successfully assigned to you";
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
    <link rel="stylesheet" href="../css/assign_projects.css">
    
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
                                            <button type="submit" class="action-button">Assign To Me</button>
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