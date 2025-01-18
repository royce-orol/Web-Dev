<?php
session_start();
include '../db_connection.php'; // Database connection

// Check if user is logged in and has the 'student' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

// Get logged-in student's ID
$student_id = $_SESSION['user_id'];

// Fetch the student's assigned supervisor ID
$query = "SELECT assigned_sv FROM proposal WHERE sender_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$stmt->bind_result($supervisor_id);
$stmt->fetch();
$stmt->close(); // Supervisor ID fetched

// Fetch all meetings associated with the student
$query = "SELECT meeting_date, meeting_time, status FROM meetings WHERE student_id = ? 
          ORDER BY meeting_date DESC, meeting_time DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$meetings = $result->fetch_all(MYSQLI_ASSOC); // Store all meetings as an array
$stmt->close();

// Handle meeting request form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $supervisor_id) {
    $meeting_date = $_POST['meeting_date'];
    $meeting_time = $_POST['meeting_time'];

    // Insert new meeting request into the database
    $query = "INSERT INTO meetings (student_id, assigned_sv_id, meeting_date, meeting_time, status) 
              VALUES (?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iiss', $student_id, $supervisor_id, $meeting_date, $meeting_time);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Meeting</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <!-- Header and Sidebar -->
    <?php include '../includes/header.php'; ?>
    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="dashboard-main">
            <h1>Schedule a Meeting</h1>

            <!-- Check if supervisor is assigned -->
            <?php if (isset($supervisor_id)): ?>
                <!-- Meeting Request Form -->
                <form action="schedule_meeting.php" method="POST">
                    <label for="meeting_date">Meeting Date:</label>
                    <input type="date" name="meeting_date" id="meeting_date" required>

                    <label for="meeting_time">Meeting Time:</label>
                    <input type="time" name="meeting_time" id="meeting_time" required>

                    <button type="submit">Request Meeting</button>
                </form>
            <?php else: ?>
                <!-- Message if no supervisor is assigned -->
                <p>No supervisor is assigned to you. Please contact your administrator.</p>
            <?php endif; ?>

            <hr>

            <!-- Meeting Schedule Section -->
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
                        <!-- Message if no meetings are scheduled -->
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
