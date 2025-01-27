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
    // Redirect to refresh the page and show updated status
    header("Location: manage_meetings.php");
    exit();
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
    // Redirect to refresh the page and show the new meeting
    header("Location: manage_meetings.php");
    exit();
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
    <style>
        /* Style for the meeting requests table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add a subtle shadow */
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd; /* Lighter bottom border */
        }
        th {
            background-color: #f4f4f4; /* Light grey header background */
            color: #333;
            font-weight: bold;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9; /* Very light grey for even rows */
        }
        tbody tr:hover {
            background-color: #e6e6e6; /* Light grey on hover */
        }
        td:last-child {
            text-align: center; /* Center align action buttons */
        }
        .btn {
            padding: 8px 12px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-accept {
            background-color: #4CAF50; /* Green for accept */
            color: white;
        }
        .btn-reject {
            background-color: #f44336; /* Red for reject */
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .status {
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 5px;
            display: inline-block; /* Make status behave like inline block */
            text-align: center;
            min-width: 70px; /* Ensure status text doesn't wrap too tightly */
        }
        .status-accepted {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #85640a;
            border: 1px solid #ffeeba;
        }


        /* Style for the "Add New Meeting" form - you can adjust as needed */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group select,
        .form-group input[type="date"],
        .form-group input[type="time"] {
            width: calc(100% - 22px); /* Adjust width to account for padding and border */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Make padding and border part of the element's total width */
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        /* General form styling adjustments if needed */
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
    </style>
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
                                    <?php
                                        $statusClass = '';
                                        switch ($meeting['status']) {
                                            case 'accepted':
                                                $statusClass = 'status-accepted';
                                                break;
                                            case 'rejected':
                                                $statusClass = 'status-rejected';
                                                break;
                                            case 'pending':
                                                $statusClass = 'status-pending';
                                                break;
                                            default:
                                                $statusClass = '';
                                        }
                                    ?>
                                    <span class="status <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars(ucfirst($meeting['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Actions only show if the meeting status is "pending" -->
                                    <?php if ($meeting['status'] === 'pending'): ?>
                                        <form action="manage_meetings.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="meeting_id" value="<?php echo $meeting['meeting_id']; ?>">
                                            <button type="submit" name="status" value="accepted" class="btn btn-accept">Accept</button>
                                        </form>
                                        <form action="manage_meetings.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="meeting_id" value="<?php echo $meeting['meeting_id']; ?>">
                                            <button type="submit" name="status" value="rejected" class="btn btn-reject">Reject</button>
                                        </form>
                                    <?php else: ?>
                                        <!-- Show status if it's not "pending" -->
                                        <span class="status <?php echo $statusClass; ?>"><?php echo ucfirst($meeting['status']); ?></span>
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
                    <select name="student_id" id="student_id" class="form-control">
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
                    <input type="date" name="new_meeting_date" id="new_meeting_date" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="new_meeting_time">Meeting Time:</label>
                    <input type="time" name="new_meeting_time" id="new_meeting_time" required class="form-control">
                </div>

                <button type="submit" name="submit_meeting" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>