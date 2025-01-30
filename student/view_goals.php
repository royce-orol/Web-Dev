<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../db_connection.php');

// Check if the user is logged in (you might need to adjust this based on your actual login system)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Redirect to login page if not logged in
    exit;
}

$student_id = $_SESSION['user_id']; // Get the logged-in student's ID

// Handle goal completion update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['goal_id']) && isset($_POST['is_completed'])) {
        $goal_id = intval($_POST['goal_id']);
        $is_completed = isset($_POST['is_completed']) ? 1 : 0; // Convert checkbox to 1 or 0

        // Update the goal status in the database
        $update_query = "UPDATE goals SET is_completed = ? WHERE goal_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('ii', $is_completed, $goal_id);
        $update_stmt->execute();
        $update_stmt->close();
    }
}

// Fetch goals for the logged-in student
$query = "SELECT goal_id, goal_title, goal_description, is_completed FROM goals WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Specify character encoding for the document -->
    <meta charset="UTF-8">
    <!-- Ensure proper scaling and responsiveness on different devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Set the title of the webpage -->
    <title>Your Goals</title>
    <!-- Link to the dashboard's CSS file -->
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/view_goals.css">

    
</head>
<body>
    <!-- Include the header file for navigation -->
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <!-- Include the sidebar for navigation links -->
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <!-- Main heading of the page -->
            <h1>Your Goals</h1>

            <div class="table-container">
                <!-- Create a table to display the list of goals -->
                <table>
                    <thead>
                        <tr>
                            <!-- Table column headers -->
                            <th>Goal Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Check if there are any goals in the result -->
                        <?php if ($result->num_rows > 0): ?>
                            <!-- Loop through each goal in the result set -->
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <!-- Display the goal title -->
                                    <td><?php echo htmlspecialchars($row['goal_title']); ?></td>
                                    <!-- Display the goal description -->
                                    <td><?php echo htmlspecialchars($row['goal_description']); ?></td>
                                    <!-- Display the goal status as "Completed" or "Not Completed" -->
                                    <td><?php echo $row['is_completed'] ? 'Completed' : 'Not Completed'; ?></td>
                                    <td>
                                        <!-- Form to update the completion status of the goal -->
                                        <form method="POST">
                                            <!-- Hidden input to store the goal ID -->
                                            <input type="hidden" name="goal_id" value="<?php echo $row['goal_id']; ?>">
                                            <!-- Checkbox to mark the goal as completed -->
                                            <input type="checkbox" name="is_completed" <?php echo $row['is_completed'] ? 'checked' : ''; ?> onchange="this.form.submit();">
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <!-- Message displayed if no goals are assigned -->
                            <tr>
                                <td colspan="4" class="no-data">No goals have been assigned.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Include the footer file -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>