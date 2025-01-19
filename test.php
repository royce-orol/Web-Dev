<?php
session_start();

// Include necessary files for database interaction
include("classes/learn.php");
include("classes/logins.php");
include("classes/user.php");
/*
    if(!isset($_SESSION['username']))
    {
        header("Location: login.php");
    }
    elseif ($_SESSION['usertype'] =='admin')
    {
        // code...
        header("Location: login.php");
    }
*/
$host ="localhost";
$user ="root";
$password ="";
$db ="ebwdata";

$data=mysqli_connect($host,$user,$password,$db);

$name = $_SESSION['username'];

$sql="SELECT * FROM user WHERE username='$name' ";

$result=mysqli_query($data,$sql);

$info=mysqli_fetch_assoc($result);


// Function to update data in database
function updateData($table, $data, $conditions = '') {
    global $conn; // Assuming $conn is your database connection

    // Build SQL UPDATE statement
    $sql = "UPDATE $table SET ";
    $updates = [];

    foreach ($data as $key => $value) {
        $updates[] = "$key = '$value'";
    }

    $sql .= implode(", ", $updates);

    if (!empty($conditions)) {
        $sql .= " WHERE $conditions";
    }

    // Execute query
    if (mysqli_query($conn, $sql)) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - eB Asset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
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
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: calc(100% - 20px);
            padding: 8px;
            margin-bottom: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .btn {
            display: block;
            width: 100%;
            max-width: 200px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .btn.logout {
            background-color: #dc3545;
        }
        .btn.logout:hover {
            background-color: #c82333;
        }
    </style>
    <script>
        function saveProfile(event) {
            event.preventDefault();
            const userId = document.getElementById('userId').value;
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // Replace the alert with code to send the data to the server
            alert(`Profile Updated:\nUsername: ${username}\nEmail: ${email}\nPassword: ${password}\nUser ID: ${userId}`);

           
            window.location.href = "userp.php";
        }

        function logout() {
            // Redirect to the login page
            window.location.href = "logout.php";
        }
    </script>
</head>
<body>
    <header class="header">
        <h1>eB Asset</h1>
        <a href="dashboard.php" class="profile-icon">
            <img src="image/User.jpg" alt="Profile">
        </a>
    </header>
    <div class="container">
        <h2>Edit Profile</h2>
        <form method="post">
            <!-- Hidden input field for user ID -->
            <input type="hidden"  name="userId" value="<?php echo "{$info['username']}" ?>">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter username">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password">
            </div>
            <div class="btn-container">
                <button type="submit" class="btn">Save Profile</button>
                <a href="logout.php" class="btn logout">Logout</a>
            </div>
        </form>
    </div>
</body>
</html>
