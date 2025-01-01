<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../db_connection.php');

// Check if the logged-in user is a student
if ($_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

// Get the student's ID
$student_id = $_SESSION['user_id'];

// Handle the form submission to update goal status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['goal_id'])) {
    $goal_id = intval($_POST['goal_id']);
    $is_completed = isset($_POST['is_completed']) ? 1 : 0;

    // Update the goal's completion status
    $query = "UPDATE goals SET is_completed = ? WHERE goal_id = ? AND student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iii', $is_completed, $goal_id, $student_id);

    if (!$stmt->execute()) {
        $error_message = "Failed to update goal status.";
    } else {
        $success_message = "Goal status updated successfully.";
    }
}

// Fetch the goals assigned to the student
$query = "SELECT goal_id, goal_title, goal_description, is_completed FROM goals WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Goals</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Your Goals</h1>

            <!-- Display success or error message -->
            <?php if (!empty($success_message)): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php elseif (!empty($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Goal Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Mark as Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['goal_title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['goal_description']); ?></td>
                                    <td><?php echo $row['is_completed'] ? 'Completed' : 'Not Completed'; ?></td>
                                    <td>
                                        <form method="POST" action="view_goals.php">
                                            <input type="hidden" name="goal_id" value="<?php echo htmlspecialchars($row['goal_id']); ?>">
                                            <input type="checkbox" name="is_completed" <?php echo $row['is_completed'] ? 'checked' : ''; ?> onchange="this.form.submit();">
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No goals have been assigned to you yet.</td>
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