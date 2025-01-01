<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../db_connection.php'); // Adjusted for consistent relative path

// Check if the logged-in user is a supervisor
if ($_SESSION['role'] !== 'supervisor') {
    header("Location: ../login.php");
    exit;
}

// Get supervisor's ID from session
$supervisor_id = $_SESSION['user_id'];

// Handle the assignment of the proposal
if (isset($_POST['assign_proposal_id'])) {
    $proposal_id = intval($_POST['assign_proposal_id']);

    // Begin the transaction
    $conn->begin_transaction();

    try {
        // Update the proposal to assign it to the supervisor
        $query = "UPDATE proposal SET assigned_sv = ? WHERE proposal_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $supervisor_id, $proposal_id);

        if ($stmt->execute()) {
            // After assignment, update the status to 'approved'
            $status_query = "UPDATE proposal SET status = 'approved' WHERE proposal_id = ?";
            $stmt_status = $conn->prepare($status_query);
            $stmt_status->bind_param('i', $proposal_id);

            if ($stmt_status->execute()) {
                // Commit the transaction if both updates were successful
                $conn->commit();
                $success_message = "Proposal successfully assigned and status updated to approved.";
            } else {
                throw new Exception("Failed to update the status to approved.");
            }
        } else {
            throw new Exception("Failed to assign the proposal.");
        }
    } catch (Exception $e) {
        // If any error occurs, rollback the transaction
        $conn->rollback();
        $error_message = $e->getMessage();
    }
}

// Fetch all pending proposals
$query = "SELECT proposal_id, title, description, status FROM proposal WHERE status = 'pending'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Project</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Assign Project</h1>

            <!-- Display success or error message -->
            <?php if (!empty($success_message)): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php elseif (!empty($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <!-- Display pending proposals in a styled table -->
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
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="assign_proposal_id" value="<?php echo htmlspecialchars($row['proposal_id']); ?>">
                                            <button type="submit" class="action-button">Assign</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No pending proposals found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>