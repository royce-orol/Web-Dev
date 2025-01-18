<?php
// Start the session to access session variables
session_start();

// Include the database connection file
include('../db_connection.php');


// Get the logged-in student's ID from the session
$student_id = $_SESSION['user_id'];

// Check if the form has been submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the goal ID from the submitted form
    $goal_id = $_POST['goal_id'];
    // Determine if the "is_completed" checkbox was checked (1 for true, 0 for false)
    $is_completed = isset($_POST['is_completed']) ? 1 : 0;

    // Prepare the SQL query to update the goal's completion status
    $stmt = $conn->prepare("UPDATE goals SET is_completed = ? WHERE goal_id = ? AND student_id = ?");
    // Bind the parameters to the query: is_completed, goal_id, and student_id
    $stmt->bind_param('iii', $is_completed, $goal_id, $student_id);
    // Execute the prepared statement
    $stmt->execute();
}

// Prepare a query to fetch all goals assigned to the logged-in student
$stmt = $conn->prepare("SELECT goal_id, goal_title, goal_description, is_completed FROM goals WHERE student_id = ?");
$stmt->bind_param('i', $student_id); // Bind the student_id to the query
$stmt->execute(); // Execute the query
$result = $stmt->get_result(); // Fetch the results of the query
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
    <!-- Link to the header's CSS file -->
    <link rel="stylesheet" href="../css/header.css">
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
                                    <td><?php echo $row['goal_title']; ?></td>
                                    <!-- Display the goal description -->
                                    <td><?php echo $row['goal_description']; ?></td>
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
                                <td colspan="4">No goals have been assigned.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Include the footer file -->
    <?php include '../footer.php'; ?>
</body>
</html>
