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

// Debug: Check if the query is returning any results
if ($students->num_rows === 0) {
    $no_students = true; // Flag to show the modal pop-up
} else {
    $no_students = false;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['student_id']) && !empty($_POST['goal_title']) && !empty($_POST['goal_description'])) {
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
    } else {
        $error_message = "Please fill all the required fields.";
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
    <link rel="stylesheet" href="../css/create_goals.css">
    
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
                <select name="student_id" id="student_id" required onchange="enableFormFields()">
                    <option value="">-- Select Student --</option>
                    <?php
                    // Loop through the fetched students and display them in the dropdown
                    while ($student = $students->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($student['id']); ?>">
                            <?php echo htmlspecialchars($student['first_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <p id="studentMessage" class="small-message"></p>

                <label for="goal_title">Goal Title:</label>
                <input type="text" name="goal_title" id="goal_title" required disabled>

                <label for="goal_description">Goal Description:</label>
                <textarea name="goal_description" id="goal_description" required disabled></textarea>

                <button type="submit" class="action-button" disabled id="submitButton">Create Goal</button>

                <!-- Question icon for no students assigned -->
                <?php if ($no_students): ?>
                    <span class="question-icon" onclick="showModal()">?</span>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Modal for no students assigned -->
    <div class="modal" id="noStudentsModal">
        <span class="close" onclick="closeModal()">×</span>
        <p>No students have been assigned to you. Please ensure that students are assigned in the system.</p>
    </div>

    <script>
        // Enable form fields when a student is selected
        function enableFormFields() {
            var studentIdSelect = document.getElementById('student_id');
            var goalTitleInput = document.getElementById('goal_title');
            var goalDescriptionTextarea = document.getElementById('goal_description');
            var submitButton = document.getElementById('submitButton');

            if (studentIdSelect.value !== "") {
                goalTitleInput.disabled = false;
                goalDescriptionTextarea.disabled = false;
                submitButton.disabled = false;
            } else {
                goalTitleInput.disabled = true;
                goalDescriptionTextarea.disabled = true;
                submitButton.disabled = true;
            }
        }

        // Show modal
        function showModal() {
            document.getElementById('noStudentsModal').style.display = 'block';
        }

        // Close modal
        function closeModal() {
            document.getElementById('noStudentsModal').style.display = 'none';
        }
    </script>
</body>
</html>