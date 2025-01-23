<?php
session_start();
include('../db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch list of students
$students_query = "SELECT id, CONCAT(first_name, ' ', last_name) AS full_name FROM users WHERE role = 'student'";
$students_result = $conn->query($students_query);

// Fetch list of supervisors
$supervisors_query = "SELECT id, email FROM users WHERE role = 'supervisor'";
$supervisors_result = $conn->query($supervisors_query);

// Handle form submission
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = intval($_POST['student_id']);
    $supervisor_id = intval($_POST['supervisor_id']);

    if ($student_id && $supervisor_id) {
        // Check if the assignment already exists in the studsuper table
        $check_query = "SELECT * FROM studsuper WHERE student_id = ? AND supervisor_id = ?";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param('ii', $student_id, $supervisor_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "This student is already assigned to the selected supervisor.";
        } else {
            // Insert the assignment into the studsuper table
            $insert_query = "INSERT INTO studsuper (student_id, supervisor_id) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param('ii', $student_id, $supervisor_id);

            if ($stmt->execute()) {
                // After assignment, store the supervisor email along with student ID
                $message = "Student successfully assigned to the supervisor.";
            } else {
                $message = "Failed to assign student to the supervisor.";
            }
        }
    } else {
        $message = "Please select both a student and a supervisor.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Student to Supervisor</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
    <style>
        .form-container {
            margin: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            max-width: 400px;
        }

        .form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-container select,
        .form-container button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container button {
            background-color: wheat;
            color: white;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: blue;
        }

        .message {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Assign Student to Supervisor</h1>

            <!-- Display success or error message -->
            <?php if (!empty($message)): ?>
                <p class="message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <!-- Form to assign students to supervisors -->
            <form method="POST" class="form-container">
                <label for="student_id">Select Student:</label>
                <select name="student_id" id="student_id" required>
                    <option value="">-- Select Student --</option>
                    <?php while ($student = $students_result->fetch_assoc()): ?>
                        <option value="<?php echo $student['id']; ?>">
                            <?php echo htmlspecialchars($student['full_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="supervisor_id">Select Supervisor:</label>
                <select name="supervisor_id" id="supervisor_id" required>
                    <option value="">-- Select Supervisor --</option>
                    <?php while ($supervisor = $supervisors_result->fetch_assoc()): ?>
                        <option value="<?php echo $supervisor['id']; ?>">
                            <?php echo htmlspecialchars($supervisor['email']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit">Assign</button>
            </form>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
