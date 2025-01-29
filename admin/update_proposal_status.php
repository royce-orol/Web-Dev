<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('../db_connection.php'); 

// Check if user is logged in and has the correct role (admin)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$success_message = '';
$error_message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proposal_id'], $_POST['status'])) {
    $proposal_id = intval($_POST['proposal_id']);
    $status = $_POST['status'];
    
    if (in_array($status, ['pending', 'approved', 'rejected', 'assigned'])) {
        $stmt = $conn->prepare("UPDATE proposal SET status = ? WHERE proposal_id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $status, $proposal_id);
            if ($stmt->execute()) {
                $success_message = "Proposal status updated successfully!";
            } else {
                $error_message = "Error updating status: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Error preparing the statement: " . $conn->error;
        }
    } else {
        $error_message = "Invalid status selected.";
    }
}

// Retrieve all proposals
$proposals = [];
$query = "
    SELECT p.proposal_id, p.title, p.description, p.status, 
           u1.first_name AS sender_first_name, u1.last_name AS sender_last_name, 
           u2.first_name AS supervisor_first_name, u2.last_name AS supervisor_last_name
    FROM proposal p
    LEFT JOIN users u1 ON p.sender_id = u1.id  
    LEFT JOIN users u2 ON p.assigned_sv = u2.id  
    ORDER BY p.proposal_id DESC";

$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $proposals[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Proposal Status</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>
        <div class="dashboard-main">
            <h1>Update Proposal Status</h1>

            <!-- Display success or error message -->
            <?php if (!empty($success_message)): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php elseif (!empty($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <!-- Proposal List -->
            <table border="1" width="100%">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Sender</th>
                        <th>Supervisor</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proposals as $proposal): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($proposal['title']); ?></td>
                            <td><?php echo htmlspecialchars($proposal['sender_first_name'] . ' ' . $proposal['sender_last_name']); ?></td>
                            <td><?php echo htmlspecialchars($proposal['supervisor_first_name'] . ' ' . $proposal['supervisor_last_name']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($proposal['status'])); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="proposal_id" value="<?php echo $proposal['proposal_id']; ?>">
                                    <select name="status" required>
                                        <option value="approved" <?php echo ($proposal['status'] == 'approved') ? 'selected' : ''; ?>>Approved</option>
                                        <option value="rejected" <?php echo ($proposal['status'] == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>