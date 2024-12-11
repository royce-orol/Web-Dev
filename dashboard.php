<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Retrieve user details from session
$name = $_SESSION['first_name'] . " " . $_SESSION['last_name'];
$role = $_SESSION['role']; // student, moderator, admin, supervisor

// Sample announcements data (replace with database retrieval)
$announcements = [
    ["title" => "Welcome to the FYP Management System!", "content" => "Please check your assigned projects and schedules."],
    ["title" => "Submission Deadline", "content" => "Ensure your proposals are submitted by December 20th."],
];

?>
    <link rel="stylesheet" href="css/dashboard.css">


<?php include 'header.php'; ?>
<div class="dashboard-container">
    <div class="dashboard-sidebar">
        <h3>Features</h3>
        <ul>
            <?php if ($role === 'student'): ?>
                <li><a href="view_projects.php">View Projects</a></li>
                <li><a href="submit_proposal.php">Submit Proposal</a></li>
                <li><a href="profile.php">Update Profile</a></li>
            <?php elseif ($role === 'moderator'): ?>
                <li><a href="manage_feedback.php">Manage Feedback</a></li>
                <li><a href="view_proposals.php">View Proposals</a></li>
            <?php elseif ($role === 'supervisor'): ?>
                <li><a href="assign_projects.php">Assign Projects</a></li>
                <li><a href="manage_meetings.php">Manage Meetings</a></li>
            <?php elseif ($role === 'admin'): ?>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="edit_announcements.php">Edit Announcements</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="dashboard-main">
        <h1>Announcements</h1>
        <?php foreach ($announcements as $announcement): ?>
            <div class="announcement">
                <h2><?php echo htmlspecialchars($announcement['title']); ?></h2>
                <p><?php echo htmlspecialchars($announcement['content']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include 'footer.php'; ?>

