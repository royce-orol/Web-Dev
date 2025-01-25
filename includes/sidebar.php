<style>
    .dashboard-sidebar ul {
        list-style-type: none; /* Remove bullet points */
        padding: 0;
        margin: 0;
    }

    .dashboard-sidebar ul li {
        margin: 10px 0; /* Add spacing between list items */
    }

    .dashboard-sidebar ul li a {
        display: block; /* Make the links behave like blocks for easier hover effects */
        text-decoration: none;
        color: black; /* Default text color */
        font-size: 16px; /* Increase font size */
        padding: 10px 15px; /* Add padding for box effect */
        border-radius: 5px; /* Slightly rounded corners */
        transition: all 0.3s ease; /* Smooth transition for hover effects */
    }

    .dashboard-sidebar ul li a:hover {
        background-color: wheat; /* Change background color on hover */
        color: black; /* Keep text readable on hover */
        font-size: 18px; /* Slightly increase font size on hover */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Add a subtle shadow effect */
    }
</style>

<div class="dashboard-sidebar">
    <h3>Features</h3>
    <ul>
        <?php if ($_SESSION['role'] === 'student'): ?>
            <li><a href="/WebDevAsgn/dashboard.php">Home</a></li>
            <li><a href="/WebDevAsgn/student/update_profile.php">Profile</a></li>
            <li><a href="/WebDevAsgn/student/submit_feedback.php">Submit Feedback</a></li>
            <li><a href="/WebDevAsgn/student/view_supervisors.php">View Supervisors</a></li>
            <li><a href="/WebDevAsgn/student/submit_proposal.php">Submit Proposal</a></li>
            <li><a href="/WebDevAsgn/student/view_projects.php">View Projects</a></li>
            <li><a href="/WebDevAsgn/student/schedule_meeting.php">Schedule Meeting</a></li>
            <li><a href="/WebDevAsgn/student/view_goals.php">View Goals</a></li>
            <li><a href="/WebDevAsgn/student/book_presentation.php">Book Presentation</a></li>
            <li><a href="/WebDevAsgn/student/view_marks.php">View Marks</a></li>
            <li><a href="/WebDevAsgn/email.php">Email Others</a></li>
            <li><a href="/WebDevAsgn/chat.php">Chat</a></li>
        <?php elseif ($_SESSION['role'] === 'moderator'): ?>
            <li><a href="/WebDevAsgn/dashboard.php">Home</a></li>
            <li><a href="/WebDevAsgn/student/update_profile.php">Profile</a></li>
            <li><a href="/WebDevAsgn/moderator/view_feedback.php">View Feedback</a></li>
            <li><a href="/WebDevAsgn/moderator/view_proposals.php">View Proposals</a></li>
            <li><a href="/WebDevAsgn/email.php">Email Others</a></li>
            <li><a href="/WebDevAsgn/chat.php">Chat</a></li>
        <?php elseif ($_SESSION['role'] === 'supervisor'): ?>
            <li><a href="/WebDevAsgn/dashboard.php">Home</a></li>
            <li><a href="/WebDevAsgn/student/update_profile.php">Profile</a></li>
            <li><a href="/WebDevAsgn/student/submit_proposal.php">Submit Proposal</a></li>
            <li><a href="/WebDevAsgn/supervisor/assign_projects.php">Assign Projects</a></li>
            <li><a href="/WebDevAsgn/supervisor/manage_meetings.php">Manage Meetings</a></li>
            <li><a href="/WebDevAsgn/supervisor/create_goals.php">Create Goals</a></li>
            <li><a href="/WebDevAsgn/email.php">Email Others</a></li>
            <li><a href="/WebDevAsgn/chat.php">Chat</a></li>
        <?php elseif ($_SESSION['role'] === 'admin'): ?>
            <li><a href="/WebDevAsgn/dashboard.php">Home</a></li>
            <li><a href="/WebDevAsgn/student/update_profile.php">Profile</a></li>
            <li><a href="/WebDevAsgn/admin/manage_announcements.php">Manage Announcements</a></li>
            <li><a href="/WebDevAsgn/admin/manage_users.php">Users</a></li>
            <li><a href="/WebDevAsgn/admin/view_records.php">View Records</a></li>
            <li><a href="/WebDevAsgn/admin/add_admin.php">Add Admin</a></li>
            <li><a href="/WebDevAsgn/admin/add_moderator.php">Add Moderator</a></li>
            <li><a href="/WebDevAsgn/admin/add_supervisor.php">Add Supervisor</a></li>
            <li><a href="/WebDevAsgn/email.php">Email Others</a></li>
            <li><a href="/WebDevAsgn/chat.php">Chat</a></li>

        <?php endif; ?>
    </ul>
</div>
