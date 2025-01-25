<?php
session_start();
include 'db_connection.php'; // Database connection

// Check if user is logged in and has the 'student' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

// Get logged-in student's ID
$student_id = $_SESSION['user_id'];

// Fetch the student's assigned supervisor ID and supervisor's email
$supervisor_id = null;
$supervisor_email = null;
$query = "SELECT u.email FROM studsuper ss 
          JOIN users u ON ss.supervisor_id = u.id
          WHERE ss.student_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $stmt->bind_result($supervisor_email);
    if ($stmt->fetch()) {
        $supervisor_id = $stmt->insert_id; // Get the supervisor's ID if available
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

    // Insert new meeting request into the database with 'pending' status
    $query = "INSERT INTO meetings (student_id, assigned_sv_id, meeting_date, meeting_time, status) 
              VALUES (?, ?, ?, ?, 'pending')";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('iiss', $student_id, $supervisor_id, $meeting_date, $meeting_time);
        $stmt->execute();
        $stmt->close();
        
        // Reload the page to show the updated meeting schedule
        header("Location: schedule_meeting.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting Schedule</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/header.css">
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
    </style>
</head>
<body>
    <!-- Header and Sidebar -->
    <header>
        <nav>
            <!-- Your navigation bar here -->
        </nav>
    </header>

    <div class="dashboard-container">
        <aside>
            <!-- Your sidebar content here -->
        </aside>

        <div class="dashboard-main">
            <h1>Schedule a Meeting</h1>

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
    <footer>
        <!-- Your footer content here -->
    </footer>
</body>
</html>
