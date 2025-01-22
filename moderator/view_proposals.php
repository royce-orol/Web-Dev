<?php
session_start();
include '../db_connection.php';


// Retrieve proposals where status is not approved
$query = "SELECT p.proposal_id, u1.first_name AS sender_first_name, u1.last_name AS sender_last_name, 
                 p.title, p.description, p.status, u2.first_name AS sv_first_name, u2.last_name AS sv_last_name, p.marks 
          FROM proposal p 
          JOIN users u1 ON p.sender_id = u1.id 
          LEFT JOIN users u2 ON p.assigned_sv = u2.id 
          WHERE p.status != 'approved'
          ORDER BY p.proposal_id DESC";
$result = $conn->query($query);

$proposals = [];
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
    <title>View Proposals</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Proposals</h1>

            <?php if (!empty($proposals)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Proposal ID</th>
                            <th>Sender</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Supervisor</th>
                            <th>Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($proposals as $proposal): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($proposal['proposal_id']); ?></td>
                                <td><?php echo htmlspecialchars($proposal['sender_first_name']) . ' ' . htmlspecialchars($proposal['sender_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($proposal['title']); ?></td>
                                <td><?php echo htmlspecialchars($proposal['description']); ?></td>
                                <td><?php echo htmlspecialchars($proposal['status']); ?></td>
                                <td>
                                    <?php 
                                    echo $proposal['sv_first_name'] && $proposal['sv_last_name'] 
                                        ? htmlspecialchars($proposal['sv_first_name']) . ' ' . htmlspecialchars($proposal['sv_last_name']) 
                                        : 'Not Assigned'; 
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($proposal['marks']) ?? 'N/A'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No proposals available.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html