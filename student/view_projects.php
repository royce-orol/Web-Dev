<?php
session_start();
include '../db_connection.php';

// Check if user is logged in and has student role (optional, but good practice)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    // Redirect to login or appropriate page if not a logged-in student
    header("Location: ../login.php");
    exit;
}

// Get the logged-in student's user ID from the session
$student_user_id = $_SESSION['user_id'];

// Fetch projects data for the logged-in student from proposal and presentation tables
$query = "SELECT
            u.student_id AS student_id,
            p.proposal_id AS project_id,
            p.title AS project_title,
            p.status AS proposal_status,
            pr.date AS presentation_date,
            u2.first_name AS supervisor_first_name,  -- Supervisor's first name
            u2.last_name AS supervisor_last_name  -- Supervisor's last name
          FROM proposal p
          INNER JOIN users u ON p.sender_id = u.id
          LEFT JOIN presentation pr ON p.proposal_id = pr.proposal_id
          LEFT JOIN users u2 ON p.assigned_sv = u2.id  -- Join again to get supervisor info
          WHERE p.sender_id = ?  -- Filter by sender_id (student user ID)
          ORDER BY p.proposal_id DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_user_id); // Bind the student's user ID to the query
$stmt->execute();
$result = $stmt->get_result();

// Check for errors in query execution
if (!$result) {
    die("Error retrieving projects: " . $conn->error);
}

$projects = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}
$stmt->close(); // Close the prepared statement
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Projects</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/view_projects.css">

</head>
<body>
    <?php include '../includes/header.php'; ?> <!-- Include header -->

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?> <!-- Include sidebar -->

        <div class="dashboard-main">
            <h1>Your Projects</h1> <!-- Changed heading to reflect it's user's projects -->

            <!-- Display projects in a table -->
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Supervisor Name</th> <!-- Added Supervisor Name column -->
                        <th>Project ID</th>
                        <th>Project Title</th>
                        <th>Proposal Status</th>
                        <th>Presentation Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($projects)): ?>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($project['student_id']); ?></td>
                                <!-- Display Supervisor's Name -->
                                <td><?php echo htmlspecialchars($project['supervisor_first_name']) . ' ' . htmlspecialchars($project['supervisor_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($project['project_id']); ?></td>
                                <td><?php echo htmlspecialchars($project['project_title']); ?></td>
                                <td><?php echo htmlspecialchars($project['proposal_status']); ?></td>
                                <td>
                                    <?php
                                    echo $project['presentation_date']
                                        ? htmlspecialchars($project['presentation_date'])
                                        : 'Not Scheduled';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-data">No projects found for your account.</td> <!-- Updated no data message -->
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../footer.php'; ?> <!-- Include footer -->
</body>
</html>