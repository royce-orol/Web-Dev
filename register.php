<?php
session_start();
include('db_connection.php'); // Adjust the file path

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = ""; // Initialize the error variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $student_id = $_POST['student_id'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if student ID already exists
        $query = "SELECT * FROM users WHERE student_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Student ID is already registered.";
        } else {
            // Check if email already exists
            $query = "SELECT * FROM users WHERE email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "Email is already registered.";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert user into the database
                $insert_query = "INSERT INTO users (first_name, last_name, student_id, email, password, role) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_query);
                $role = 'student'; // Default role
                $stmt->bind_param("ssssss", $first_name, $last_name, $student_id, $email, $hashed_password, $role);

                if ($stmt->execute()) {
                    $_SESSION['user_id'] = $conn->insert_id;
                    $_SESSION['first_name'] = $first_name;
                    $_SESSION['last_name'] = $last_name;
                    $_SESSION['role'] = $role;
                    header("Location: login.php");
                    exit();
                } else {
                    $error = "Registration failed. Error: " . $stmt->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FYP Management System</title>
    <link rel="stylesheet" href="css/register.css">
    <style>
        .notification {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #f44336;
            /* Red background */
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            font-size: 14px;
            text-align: center;
            z-index: 1000;
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }

        .notification.hidden {
            opacity: 0;
            visibility: hidden;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Title -->
        <div class="title">FYP Management System - Register</div>

        <!-- Registration Form -->
        <form action="register.php" method="post">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="student_id">Student ID</label>
            <input type="text" id="student_id" name="student_id" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Register</button>

            <p>Already have an account? <a href="login.php">Log in here</a></p>
        </form>
    </div>

    <!-- Notification -->
    <?php if (!empty($error)): ?>
        <div class="notification" id="notification">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <script>
        // Automatically hide the notification after 2 seconds
        window.onload = function() {
            const notification = document.getElementById('notification');
            if (notification) {
                setTimeout(() => {
                    notification.classList.add('hidden');
                }, 2000);
            }
        };
    </script>
</body>

</html>