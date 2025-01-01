<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../db_connection.php');

// Check if the logged-in user is a supervisor
if ($_SESSION['role'] !== 'supervisor') {
    header("Location: ../login.php");
    exit;
}

// Get the supervisor's ID
$supervisor_id = $_SESSION['user_id'];

// Fetch students assigned to the supervisor
$query = "SELECT DISTINCT u.id, u.first_name
          FROM users u
          JOIN proposal p ON u.id = p.sender_id
          WHERE p.assigned_sv = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $supervisor_id);
$stmt->execute();
$students = $stmt->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = intval($_POST['student_id']);
    $goal_title = $_POST['goal_title'];
    $goal_description = $_POST['goal_description'];

    // Insert the goal into the database
    $query = "INSERT INTO goals (student_id, supervisor_id, goal_title, goal_description) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iiss', $student_id, $supervisor_id, $goal_title, $goal_description);

    if ($stmt->execute()) {
        $success_message = "Goal successfully created.";
    } else {
        $error_message = "Failed to create the goal.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Goals</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Create Goals for Students</h1>

            <!-- Display success or error message -->
            <?php if (!empty($success_message)): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php elseif (!empty($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <!-- Form to create a new goal -->
            <form method="POST" class="form-container">
                <label for="student_id">Select Student:</label>
                <select name="student_id" id="student_id" required>
                    <option value="">-- Select Student --</option>
                    <?php while ($student = $students->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($student['id']); ?>">
                            <?php echo htmlspecialchars($student['first_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="goal_title">Goal Title:</label>
                <input type="text" name="goal_title" id="goal_title" required>

                <label for="goal_description">Goal Description:</label>
                <textarea name="goal_description" id="goal_description" required></textarea>

                <button type="submit" class="action-button">Create Goal</button>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>