<?php
session_start();
include '../db_connection.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in and has supervisor role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    // Redirect to login or appropriate page if not a logged-in supervisor
    header("Location: ../login.php");
    exit;
}

// Get the logged-in supervisor's user ID from the session
$supervisor_user_id = $_SESSION['user_id'];

// Fetch projects data for the logged-in supervisor from proposal and presentation tables
$query = "SELECT
            u.student_id AS student_id,
            p.proposal_id AS project_id,
            p.title AS project_title,
            p.status AS proposal_status,
            pr.date AS presentation_date,
            p.sender_id AS proposal_sender_id,
            p.assigned_sv AS proposal_assigned_sv,
            u2.student_id AS supervisor_staff_id  -- Fetch supervisor's staff_id
          FROM proposal p
          INNER JOIN users u ON p.sender_id = u.id
          LEFT JOIN presentation pr ON p.proposal_id = pr.proposal_id
          LEFT JOIN users u2 ON p.assigned_sv = u2.id  -- Join again to get supervisor info
          WHERE p.sender_id = ? OR p.assigned_sv = ?  -- Filter by sender_id or assigned_sv
          ORDER BY p.proposal_id DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $supervisor_user_id, $supervisor_user_id); // Bind the supervisor's user ID to the query
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
    <link rel="stylesheet" href="../css/view_projects_sp.css">

    
</head>
<body>
    <?php include '../includes/header.php'; ?> <!-- Include header -->

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?> <!-- Include sidebar -->

        <div class="dashboard-main">
            <h1>Projects Overview</h1> <!-- Heading updated to reflect supervisor's view -->

            <!-- Display projects in a table -->
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Supervisor ID</th>
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
                                <!-- Display Student ID -->
                                <td><?php echo htmlspecialchars($project['student_id']); ?></td>

                                <!-- Display Supervisor ID -->
                                <td><?php echo htmlspecialchars($project['supervisor_staff_id']); ?></td>

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
                            <td colspan="6" class="no-data">No projects assigned to you.</td> <!-- Updated no data message -->
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../footer.php'; ?> <!-- Include footer -->
</body>
</html>
