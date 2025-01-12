<?php
session_start();
include '../db_connection.php';

// Check if the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

// Get the logged-in student's ID
$student_id = $_SESSION['user_id'];

// Fetch the assigned supervisor from the proposals table
$query = "SELECT assigned_sv FROM proposal WHERE sender_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$stmt->bind_result($supervisor_id);
$stmt->fetch();
$stmt->close();

// Fetch all meetings for the logged-in student
$query = "SELECT meeting_date, meeting_time, status FROM meetings WHERE student_id = ? ORDER BY meeting_date DESC, meeting_time DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$meetings = [];
while ($row = $result->fetch_assoc()) {
    $meetings[] = $row;
}
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $supervisor_id) {
    $meeting_date = $_POST['meeting_date'];
    $meeting_time = $_POST['meeting_time'];

    // Insert the meeting request into the database
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
    <?php include '../includes/header.php'; ?>
    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Schedule a Meeting</h1>

            <?php if (isset($supervisor_id)): ?>
                <form action="schedule_meeting.php" method="POST">
                    <div class="form-group">
                        <label for="meeting_date">Meeting Date:</label>
                        <input type="date" name="meeting_date" id="meeting_date" required>
                    </div>

                    <div class="form-group">
                        <label for="meeting_time">Meeting Time:</label>
                        <input type="time" name="meeting_time" id="meeting_time" required>
                    </div>

                    <button type="submit" class="btn">Request Meeting</button>
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
                        <?php foreach ($meetings as $meeting): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($meeting['meeting_date']); ?></td>
                                <td><?php echo htmlspecialchars($meeting['meeting_time']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($meeting['status'])); ?></td>
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

    <?php include '../includes/footer.php'; ?>
</body>
</html>
