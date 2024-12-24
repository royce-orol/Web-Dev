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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FYP Management System</title>
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <div class="navbar">
        <div class="welcome-message">
            <p>Welcome, <?php echo htmlspecialchars($name); ?> | Role: <?php echo htmlspecialchars($role); ?></p>
        </div>
        <div class="navbar-icons">
            <a href="dashboard.php">
                <img src="images/home.png" title="Dashboard" alt="Dashboard" />
            </a>
            <a href="search.php" title="Search">
                <img src="images/search.png" title="Search" />
            </a>
            <a href="messages.php" title="Messages">
                <i class="fas fa-envelope"></i>
            </a>
        </div>
        <div class=logout-button>
            <a href="logout.php" onclick="return confirm('Are you sure you want to logout?')">
            <img src="images/logout.png" title="log-out" />
        </a>

        </div>
    </div>
</body>
</html>
