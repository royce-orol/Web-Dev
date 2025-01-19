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
    <link rel="stylesheet" href="/WebDevAsgn/css/header.css">
    <style>
        /* Navbar styling */
        .navbar {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background-color: wheat;
            color: white;
            justify-content: space-between; /* Spread out content to left and right */
        }

        /* Container for profile picture and welcome message */
        .profile-welcome {
            display: flex;
            align-items: center;
        }

        /* Profile picture */
        .profile-picture {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px; /* Space between the profile picture and welcome message */
        }

        .welcome-message p {
            margin: 0;
            font-size: 18px;
            color: black; /* Set the welcome message text color to black */
        }

        /* Navbar button images */
        .navbar-icons img, .logout-button img {
            width: 20px; /* Smaller size */
            height: 20px; /* Smaller size */
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Add transition for smooth animation */
        }

        /* Magnify animation on hover */
        .navbar-icons a:hover img, .logout-button a:hover img {
            transform: scale(1.2); /* Magnify */
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3); /* Add shadow */
        }

        /* Search input field */
        .search-input {
            display: none;
            margin-left: 10px;
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 20px; /* Rounded edges */
            outline: none; /* Remove default outline */
            transition: border 0.3s ease; /* Optional: Smooth transition for border */
        }
    </style>
</head>
<body>
    <div class="navbar">
        <!-- Profile and welcome message container -->
        <div class="profile-welcome">
            <img src="/WebDevAsgn/images/defaultprofile.png" alt="Profile Picture" class="profile-picture">
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
