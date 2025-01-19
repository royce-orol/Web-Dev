<?php
/*
    session_start();


    include("classes/learn.php");
    include("classes/logins.php");
    include("classes/user.php");

    if(isset($_SESSION['userid']) && is_numeric($_SESSION['userid']))
    {

        $id = $_SESSION['userid'];
        $login = new Login();

        $result = $login->check_login($id);

        if($result)
        {

            $user = new User();
            $user_data = $user->get_data($id);

            if(!$user_data)
            {
                header("Location: login.php");
                die;

            }

        }else
        {
            header("Location: login.php");
            die;

        }



    }else
    {
        header("Location: login.php");
        die;
    }
*/
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eB Asset</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensures the body takes up at least the full height of the viewport */
        }
        .header {
            width: 100%;
            background-color: #4CAF50; /* Original color */
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            flex: 1;
        }
        .profile-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
            flex-shrink: 0;
        }
        .profile-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .dashboard-container {
            max-width: 600px;
            width: 100%;
            margin-top: 50px;
            margin: 0 auto; /* Center the container horizontally */
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            flex-grow: 1; /* Allows the dashboard to grow to fill available space */
        }
        .dashboard-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #343a40;
            font-size: 22px;
        }
        .welcome-message {
            text-align: center;
            margin-bottom: 30px;
            color: #555;
        }
        .btn-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }
        .btn {
            padding: 12px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            width: 80%;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #218838;
        }
        /* Footer Styles */
        .footer {
            background-color: #343a40;
            color: white;
            width: 100%;
            padding: 20px 0;
            text-align: center;
            flex-shrink: 0; /* Prevents the footer from shrinking */
            display: flex;
            justify-content: space-between; /* Aligns items to the space between the container */
            align-items: center;
        }
        .footer p {
            margin: 0;
        }
        .footer a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border: none; /* Remove border */
            transition: background-color 0.3s, color 0.3s;
        }
        .footer a:hover {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>eB Asset</h1>
        <a href="userp.php" class="profile-icon">
            <img src="image/User.jpg" alt="Profile"> <!-- Updated profile image source -->
        </a>
    </header>    
    <div class="dashboard-container">
        <h2>Welcome to Your eB Asset Dashboard</h2>
        <div class="welcome-message">
            <p>Hello, <?php echo $user_data['username'] ?>! You are now logged in.</p>
        </div>
        <div class="btn-container">
            <a href="report_faulty.php" class="btn">Report Faulty Asset</a>
        </div>
        <div class="btn-container">
            <a href="request_asset.php" class="btn">Request Loan/Borrow Asset</a>
        </div>
    </div>
    <footer class="footer">
        <div class="footer-content">
            <a href="logout.php">Logout</a> <!-- Link to Logout -->
        </div>
        <div class="footer-content">
            <p>&copy; 2024 eB Asset. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
