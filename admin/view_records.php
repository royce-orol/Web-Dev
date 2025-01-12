<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
include '../db_connection.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Retrieve all proposal records along with sender and supervisor details
$proposals = [];
$query = "
    SELECT p.proposal_id, p.title, p.description, p.status, p.marks, 
           u1.first_name AS sender_first_name, u1.last_name AS sender_last_name, 
           u2.first_name AS supervisor_first_name, u2.last_name AS supervisor_last_name
    FROM proposal p
    LEFT JOIN users u1 ON p.sender_id = u1.id
    LEFT JOIN users u2 ON p.assigned_sv = u2.id
";

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
    <title>View Records</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>View Project Records</h1>

            <!-- Display table of all proposals -->
            <table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Proposal ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Sender Name</th>
                        <th>Supervisor Name</th>
                        <th>Status</th>
                        <th>Marks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($proposals)): ?>
                        <?php foreach ($proposals as $proposal): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($proposal['proposal_id']); ?></td>
                                <td><?php echo htmlspecialchars($proposal['title']); ?></td>
                                <td><?php echo htmlspecialchars($proposal['description']); ?></td>
                                <td><?php echo htmlspecialchars($proposal['sender_first_name'] . ' ' . $proposal['sender_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($proposal['supervisor_first_name'] . ' ' . $proposal['supervisor_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($proposal['status']); ?></td>
                                <p><?php echo htmlspecialchars($row['marks'] ?? ''); ?></p>

                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">No records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
