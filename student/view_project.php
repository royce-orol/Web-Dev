<?php
// Include database connection
session_start();
include('../db_connection.php'); // Adjusted for consistent relative path


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get student ID from session
$student_id = $_SESSION['user_id'];

// Query to fetch project details with proposal status
$sql = "SELECT 
            p.proposal_id AS project_id,
            p.title AS project_title,
            p.status AS proposal_status,
            pr.date AS presentation_date,
            u.student_id AS student_id
        FROM 
            proposal p
        LEFT JOIN 
            presentation pr ON p.proposal_id = pr.proposal_id
        LEFT JOIN 
            users u ON p.sender_id = u.id
        WHERE 
            p.sender_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Projects</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>View Projects</h1>
            
            <!-- Display projects in a table -->
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Project ID</th>
                        <th>Project Title</th>
                        <th>Proposal Status</th>
                        <th>Presentation Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['project_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['project_title']); ?></td>
                                <td><?php echo htmlspecialchars($row['proposal_status']); ?></td>
                                <td>
                                    <?php 
                                    echo $row['presentation_date'] 
                                        ? htmlspecialchars($row['presentation_date']) 
                                        : 'Not Scheduled'; 
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No projects found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>