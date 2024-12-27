<div class="dashboard-sidebar">
    <h3>Features</h3>
    <ul>
        <?php if ($_SESSION['role'] === 'student'): ?>
            <li><a href="/WebDevAsgn/view_projects.php">View Projects</a></li>
            <li><a href="/WebDevAsgn/student/submit_proposal.php">Submit Proposal</a></li>
            <li><a href="/WebDevAsgn/student/update_profile.php">Update Profile</a></li>
            <li><a href="/WebDevAsgn/student/submit_feedback.php">Submit Feedback</a></li>
            <li><a href="/WebDevAsgn/student/book_presentation.php">Book Presentation</a></li>



            <li><a href="/WebDevAsgn/dashboard.php">Home</a></li>


        <?php elseif ($_SESSION['role'] === 'moderator'): ?>
            <li><a href="manage_feedback.php">Manage Feedback</a></li>
            <li><a href="view_proposals.php">View Proposals</a></li>
        <?php elseif ($_SESSION['role'] === 'supervisor'): ?>
            <li><a href="assign_projects.php">Assign Projects</a></li>
            <li><a href="manage_meetings.php">Manage Meetings</a></li>
        <?php elseif ($_SESSION['role'] === 'admin'): ?>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="edit_announcements.php">Edit Announcements</a></li>
        <?php endif; ?>
    </ul>
</div>