<?php
// Start the session
session_start();

// Include database connection
include('../db_connection.php'); // Adjusted for consistent relative path


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
            p.sender_id = ?"; // Only fetch projects related to the logged-in student

// Prepare and execute the query
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id); // Bind the student ID to the query
$stmt->execute();
$result = $stmt->get_result(); // Fetch the result of the query
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
    <?php include '../includes/header.php'; ?> <!-- Include header -->

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?> <!-- Include sidebar -->

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
                                    // Check if the presentation date exists, if not display 'Not Scheduled'
                                    echo $row['presentation_date'] 
                                        ? htmlspecialchars($row['presentation_date']) 
                                        : 'Not Scheduled'; 
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No projects found.</td> <!-- Message if no projects are found -->
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../footer.php'; ?> <!-- Include footer -->
</body>
</html>
