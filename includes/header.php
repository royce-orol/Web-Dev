<?php
// Check if a session is already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Retrieve user details from session
$name = $_SESSION['first_name'] . " " . $_SESSION['last_name'];
$role = $_SESSION['role']; // student, moderator, admin, supervisor
$profile_picture = $_SESSION['profile_picture'] ?? '/WebDevAsgn/images/defaultprofile.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FYP Management System</title>
    <link rel="stylesheet" href="/WebDevAsgn/css/header.css">
    <link rel="stylesheet" href="/WebDevAsgn/css/navbar.css">

   
</head>
<body>
    <div class="navbar">
        <!-- Profile and welcome message container -->
        <div class="profile-welcome">
            <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-picture">
            <div class="welcome-message">
                <p><b><?php echo htmlspecialchars($name); ?> | Role: <?php echo htmlspecialchars($role); ?></b></p>
            </div>
        </div>

        <!-- Logout button -->
        <div class="logout-button">
            <a href="/WebDevAsgn/logout.php" onclick="return confirm('Are you sure you want to logout?')">
                <img src="/WebDevAsgn/images/log_out.png" title="Log Out" alt="Log Out" />
            </a>
        </div>
    </div>
</body>
</html>
