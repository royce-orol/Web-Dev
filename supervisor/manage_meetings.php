<?php
session_start();
include '../db_connection.php';

// Check if the user is logged in and is a supervisor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supervisor') {
    header("Location: ../login.php");
    exit;
}

// Get the supervisor's ID
$supervisor_id = $_SESSION['user_id'];

// Fetch all meeting requests for the logged-in supervisor
$query = "SELECT m.meeting_id, m.student_id, m.meeting_date, m.meeting_time, m.status, u.first_name AS student_name 
          FROM meetings m
          JOIN users u ON m.student_id = u.id
          WHERE m.assigned_sv_id = ? 
          ORDER BY m.meeting_date DESC, m.meeting_time DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();
$meetings = [];
while ($row = $result->fetch_assoc()) {
    $meetings[] = $row;
}
$stmt->close();

// Handle the update status for meetings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['meeting_id']) && isset($_POST['status'])) {
    $meeting_id = $_POST['meeting_id'];
    $status = $_POST['status'];

    // Update the status of the meeting
    $query = "UPDATE meetings SET status = ? WHERE meeting_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $status, $meeting_id);
    $stmt->execute();
    $stmt->close();
}

// Handle form submission for adding new meetings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_meeting_date']) && isset($_POST['new_meeting_time']) && isset($_POST['student_id'])) {
    $new_meeting_date = $_POST['new_meeting_date'];
    $new_meeting_time = $_POST['new_meeting_time'];
    $student_id = $_POST['student_id'];

    // Insert the new meeting into the database
    $query = "INSERT INTO meetings (student_id, assigned_sv_id, meeting_date, meeting_time, status) 
              VALUES (?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iiss', $student_id, $supervisor_id, $new_meeting_date, $new_meeting_time);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Meetings</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Manage Meeting Requests</h1>

            <h2>Meeting Requests</h2>
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($meetings)): ?>
                        <?php foreach ($meetings as $meeting): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($meeting['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($meeting['meeting_date']); ?></td>
                                <td><?php echo htmlspecialchars($meeting['meeting_time']); ?></td>
                                <td>
                                    <!-- Status will display capitalized first letter of the status -->
                                    <?php echo htmlspecialchars(ucfirst($meeting['status'])); ?>
                                </td>
                                <td>
                                    <!-- Actions only show if the meeting status is "pending" -->
                                    <?php if ($meeting['status'] === 'pending'): ?>
                                        <form action="manage_meetings.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="meeting_id" value="<?php echo $meeting['meeting_id']; ?>">
                                            <button type="submit" name="status" value="accepted" class="btn">Accept</button>
                                        </form>
                                        <form action="manage_meetings.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="meeting_id" value="<?php echo $meeting['meeting_id']; ?>">
                                            <button type="submit" name="status" value="rejected" class="btn">Reject</button>
                                        </form>
                                    <?php else: ?>
                                        <!-- Show status if it's not "pending" -->
                                        <span class="status"><?php echo ucfirst($meeting['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No meeting requests yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <hr>

            <h2>Add New Meeting</h2>
            <form action="manage_meetings.php" method="POST">
                <div class="form-group">
                    <label for="student_id">Student:</label>
                    <select name="student_id" id="student_id" >
                        <?php
                        // Fetch all students assigned to this supervisor from the proposal table
                        $query = "SELECT u.id, u.first_name, u.last_name 
                                  FROM users u 
                                  JOIN proposal p ON p.sender_id = u.id 
                                  WHERE p.assigned_sv = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('i', $supervisor_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</option>";
                        }
                        $stmt->close();
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="new_meeting_date">Meeting Date:</label>
                    <input type="date" name="new_meeting_date" id="new_meeting_date" required>
                </div>

                <div class="form-group">
                    <label for="new_meeting_time">Meeting Time:</label>
                    <input type="time" name="new_meeting_time" id="new_meeting_time" required>
                </div>

                <button type="submit" name="submit_meeting" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>
