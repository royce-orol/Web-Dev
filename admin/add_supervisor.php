<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
include('../db_connection.php'); 

// Check if user is logged in and has the correct role (admin)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form inputs
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $student_id = htmlspecialchars(trim($_POST['student_id']));
    $password = htmlspecialchars(trim($_POST['password']));
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Set the role as "admin"
    $role = 'supervisor';
    
    // Validate inputs
    if (empty($first_name) || empty($last_name) || empty($email) || empty($student_id) || empty($password)) {
        $error_message = "All fields are required.";
    } else {
        // Prepare SQL statement to insert data into the users table
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, student_id, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("ssssss", $first_name, $last_name, $email, $student_id, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $success_message = "Admin added successfully!";
            } else {
                $error_message = "Error adding admin: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $error_message = "Error preparing the statement: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Add Supervisor</h1>

            <!-- Display success or error message -->
            <?php if (!empty($success_message)): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php elseif (!empty($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <!-- Form to add an admin -->
            <form method="POST">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>
                
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="student_id">Staff ID:</label>
                <input type="text" id="student_id" name="student_id" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit">Add Supervisor</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>