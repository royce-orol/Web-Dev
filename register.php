<?php
include('db_connection.php'); // Adjust the file path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $student_id = $_POST['student_id'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'student'; // Default role for new users

    // Check if the email or student_id already exists
    $query = "SELECT * FROM users WHERE email = ? OR student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $query = "INSERT INTO users (email, first_name, last_name, student_id, password, role, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssss", $email, $first_name, $last_name, $student_id, $password, $role);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Error registering user.";
        }
    } else {
        $error = "Email or Student ID already exists.";
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
</head>
<body>
    <div class="container">
        <!-- Title -->
        <div class="title">FYP Management System</div>
        
        <!-- Registration Form -->
        <form action="register_process.php" method="post">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required>
            
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required>
            
            <label for="student_id">Student ID</label>
            <input type="text" id="student_id" name="student_id" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Register</button>
            
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>
</body>
</html>