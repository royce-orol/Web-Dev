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
    <style>
        /* Navbar styling */
        .navbar {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background-color: wheat;
            color: black; /* Updated text color for readability */
            justify-content: space-between;
            flex-wrap: wrap; /* Allow wrapping on smaller screens */
        }

        /* Profile and welcome message container */
        .profile-welcome {
            display: flex;
            align-items: center;
        }

        /* Profile picture */
        .profile-picture {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .welcome-message p {
            margin: 0;
            font-size: 18px;
            color: black;
        }

        /* Navbar button images */
        .navbar-icons img, .logout-button img {
            width: 20px;
            height: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Magnify animation on hover */
        .navbar-icons a:hover img, .logout-button a:hover img {
            transform: scale(1.2);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
        }

        /* Search input field */
        .search-input {
            display: none;
            margin-left: 10px;
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 20px;
            outline: none;
            transition: border 0.3s ease;
        }
    </style>
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
