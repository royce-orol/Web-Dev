<?php
session_start();
include '../db_connection.php'; // Database connection

// Check if user is logged in and has the 'student' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get logged-in student's ID
$student_id = $_SESSION['user_id'];

// Initialize success and error messages
$success_message = '';
$error_message = '';

// Fetch the student's assigned supervisor ID and supervisor's email
$supervisor_id = null;
$supervisor_email = null;
$query = "SELECT u.email, p.assigned_sv AS supervisor_id
          FROM proposal p
          JOIN users u ON p.assigned_sv = u.id
          WHERE p.sender_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $stmt->bind_result($supervisor_email, $supervisor_id_from_db);
    if ($stmt->fetch()) {
        $supervisor_id = $supervisor_id_from_db; // Assign fetched supervisor_id
    }
    $stmt->close();
}

// Fetch all meetings associated with the student
$meetings = [];
$query = "SELECT meeting_id, meeting_date, meeting_time, status FROM meetings WHERE student_id = ?
          ORDER BY meeting_date DESC, meeting_time DESC";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $meetings = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Handle meeting request form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $supervisor_id) {
    $meeting_date = $_POST['meeting_date'];
    $meeting_time = $_POST['meeting_time'];

    // Combine date and time for comparison
    $meeting_datetime_str = $meeting_date . ' ' . $meeting_time;
    $meeting_timestamp = strtotime($meeting_datetime_str);
    $current_timestamp = time();

    if ($meeting_timestamp <= $current_timestamp) {
        // --- ERROR CONDITION: Meeting time is in the past ---
        $error_message = "You cannot schedule a meeting in the past. Please select a future date and time.";
    } else {
        // --- VALID MEETING TIME: Proceed to insert into database ---
        $query = "INSERT INTO meetings (student_id, assigned_sv_id, meeting_date, meeting_time, status)
                  VALUES (?, ?, ?, ?, 'pending')";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('iiss', $student_id, $supervisor_id, $meeting_date, $meeting_time);
            if ($stmt->execute()) {
                // --- SUCCESS: Meeting request inserted ---
                $success_message = "Meeting request submitted successfully.";
            } else {
                // --- ERROR: Database insertion failed ---
                $error_message = "Failed to submit meeting request. Please try again.";
            }
            $stmt->close();
        } else {
            // --- ERROR: Database prepare statement failed ---
            $error_message = "Database error. Please try again later.";
        }
    }
}

// Retrieve message from URL if set after form submission
$success_message = isset($_GET['message']) ? $_GET['message'] : $success_message;
$error_message = isset($_GET['error']) ? $_GET['error'] : $error_message;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Schedule</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        form {
            margin-bottom: 20px;
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        form input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        form button:hover {
            background-color: #0056b3;
        }
        .feedback-table {
            width: 100%;
            margin: 0 auto;
            max-width: 1000px;
            overflow-x: auto;
        }
        .success-message {
            color: green;
            margin-bottom: 10px;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Schedule a Meeting</h1>

            <!-- --- ERROR MESSAGE DISPLAY --- -->
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <!-- --- SUCCESS MESSAGE DISPLAY --- -->
            <?php if (!empty($success_message)): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>

            <!-- Check if supervisor is assigned -->
            <?php if ($supervisor_email): ?>
                <p>Your assigned supervisor: <strong><?= htmlspecialchars($supervisor_email); ?></strong></p>
                <!-- Meeting Request Form -->
                <form action="schedule_meeting.php" method="POST">
                    <label for="meeting_date">Meeting Date:</label>
                    <input type="date" name="meeting_date" id="meeting_date" required>

                    <label for="meeting_time">Meeting Time:</label>
                    <input type="time" name="meeting_time" id="meeting_time" required>

                    <button type="submit">Request Meeting</button>
                </form>
            <?php else: ?>
                <p>No supervisor is assigned to you. Please contact your administrator.</p>
            <?php endif; ?>

            <hr>

            <h2>Meeting Schedule</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($meetings)): ?>
                        <!-- Display each meeting using iteration -->
                        <?php foreach ($meetings as $meeting): ?>
                            <tr>
                                <td><?= htmlspecialchars($meeting['meeting_date']); ?></td>
                                <td><?= htmlspecialchars($meeting['meeting_time']); ?></td>
                                <td><?= htmlspecialchars(ucfirst($meeting['status'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No meeting requests yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>
