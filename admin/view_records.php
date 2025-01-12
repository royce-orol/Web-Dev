<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
include '../db_connection.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Retrieve proposals, announcements, user records, feedback, meetings, and presentations
$proposals = [];
$announcements = [];
$users = [];
$feedbacks = [];
$meetings = [];
$presentations = [];

// Query for proposals
$query_proposals = "
    SELECT p.proposal_id, p.title, p.description, p.status, 
           u1.first_name AS sender_first_name, u1.last_name AS sender_last_name, 
           u2.first_name AS supervisor_first_name, u2.last_name AS supervisor_last_name
    FROM proposal p
    LEFT JOIN users u1 ON p.sender_id = u1.id
    LEFT JOIN users u2 ON p.assigned_sv = u2.id
";
$result = $conn->query($query_proposals);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $proposals[] = $row;
    }
}

// Query for announcements
$query_announcements = "SELECT * FROM announcements";
$result = $conn->query($query_announcements);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}

// Query for user records
$query_users = "SELECT * FROM users";
$result = $conn->query($query_users);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Query for feedback
$query_feedbacks = "
    SELECT f.feedback_id, f.sender_id, f.message, f.created_at, 
           u.first_name AS sender_first_name, u.last_name AS sender_last_name
    FROM feedback f
    LEFT JOIN users u ON f.sender_id = u.id
";
$result = $conn->query($query_feedbacks);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}

// Query for meetings
$query_meetings = "
    SELECT m.meeting_id, m.student_id, m.assigned_sv_id, m.meeting_date, m.meeting_time, m.status, 
           u1.first_name AS student_first_name, u1.last_name AS student_last_name, 
           u2.first_name AS supervisor_first_name, u2.last_name AS supervisor_last_name
    FROM meetings m
    LEFT JOIN users u1 ON m.student_id = u1.id
    LEFT JOIN users u2 ON m.assigned_sv_id = u2.id
";
$result = $conn->query($query_meetings);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $meetings[] = $row;
    }
}

// Query for presentations
$query_presentations = "
    SELECT p.id, p.student_id, p.date, p.time, p.created_at, 
           u.first_name AS student_first_name, u.last_name AS student_last_name, 
           pr.title AS proposal_title
    FROM presentation p
    LEFT JOIN users u ON p.student_id = u.id
    LEFT JOIN proposal pr ON p.proposal_id = pr.proposal_id
";
$result = $conn->query($query_presentations);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $presentations[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Records</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>View All Records</h1>

            <!-- Display Proposals Table -->
            <h2>Project Proposals</h2>
            <table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Proposal ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Sender Name</th>
                        <th>Supervisor Name</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($proposals)): ?>
                        <?php foreach ($proposals as $proposal): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($proposal['proposal_id']); ?></td>
                                <td><?php echo htmlspecialchars($proposal['title']); ?></td>
                                <td><?php echo htmlspecialchars($proposal['description']); ?></td>
                                <td><?php echo htmlspecialchars($proposal['sender_first_name'] . ' ' . $proposal['sender_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($proposal['supervisor_first_name'] . ' ' . $proposal['supervisor_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($proposal['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No proposals found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Display Announcements Table -->
            <h2>Announcements</h2>
            <table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Announcement ID</th>
                        <th>Message</th>
                        
                        
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($announcements)): ?>
                        <?php foreach ($announcements as $announcement): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($announcement['id']); ?></td>
                                <td><?php echo htmlspecialchars($announcement['description']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No announcements found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Display Feedback Records Table -->
            <h2>Feedback</h2>
            <table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Feedback ID</th>
                        <th>Sender Name</th>
                        <th>Feedback Content</th>
                        <th>Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($feedbacks)): ?>
                        <?php foreach ($feedbacks as $feedback): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($feedback['feedback_id']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['sender_first_name'] . ' ' . $feedback['sender_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['message']); ?></td>
                                <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No feedback found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Display Meetings Records Table -->
            <h2>Meetings</h2>
            <table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Meeting ID</th>
                        <th>Student Name</th>
                        <th>Supervisor Name</th>
                        <th>Meeting Date</th>
                        <th>Meeting Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($meetings)): ?>
                        <?php foreach ($meetings as $meeting): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($meeting['meeting_id']); ?></td>
                                <td><?php echo htmlspecialchars($meeting['student_first_name'] . ' ' . $meeting['student_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($meeting['supervisor_first_name'] . ' ' . $meeting['supervisor_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($meeting['meeting_date']); ?></td>
                                <td><?php echo htmlspecialchars($meeting['meeting_time']); ?></td>
                                <td><?php echo htmlspecialchars($meeting['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No meetings found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Display Presentations Records Table -->
            <h2>Presentations</h2>
            <table border="1" cellpadding="10">
                <thead>
                    <tr>
                        <th>Presentation ID</th>
                        <th>Student Name</th>
                        <th>Proposal Title</th>
                        <th>Presentation Date</th>
                        <th>Presentation Time</th>
                        <th>Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($presentations)): ?>
                        <?php foreach ($presentations as $presentation): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($presentation['presentation_id']); ?></td>
                                <td><?php echo htmlspecialchars($presentation['student_first_name'] . ' ' . $presentation['student_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($presentation['proposal_title']); ?></td>
                                <td><?php echo htmlspecialchars($presentation['presentation_date']); ?></td>
                                <td><?php echo htmlspecialchars($presentation['presentation_time']); ?></td>
                                <td><?php echo htmlspecialchars($presentation['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No presentations found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
