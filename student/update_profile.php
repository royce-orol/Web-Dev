<?php
session_start();

// Include the database connection file
include('../db_connection.php');

// Retrieve user details from session
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$user_id = $_SESSION['user_id']; // Assuming you store user ID in the session

// Fetch user details from the database
$query = "SELECT email, CONCAT(first_name, ' ', last_name) AS username, student_id FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_details = $result->fetch_assoc();

// Update profile when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update session variables
    $_SESSION['first_name'] = $_POST['first_name'];
    $_SESSION['last_name'] = $_POST['last_name'];

    // Get updated first name, last name, and password
    $updated_first_name = $_POST['first_name'];
    $updated_last_name = $_POST['last_name'];
    $new_password = $_POST['password'];

    // SQL query to update the user's profile in the database
    $query = "UPDATE users SET first_name = ?, last_name = ?" . ($new_password ? ", password = ?" : "") . " WHERE id = ?";
    $stmt = $conn->prepare($query);

    if ($new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->bind_param('sssi', $updated_first_name, $updated_last_name, $hashed_password, $user_id);
    } else {
        $stmt->bind_param('ssi', $updated_first_name, $updated_last_name, $user_id);
    }

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        $success_message = "Profile updated successfully.";

        // Re-fetch updated user details
        $query = "SELECT email, CONCAT(first_name, ' ', last_name) , role AS username, student_id, role FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_details = $result->fetch_assoc();
    } else {
        $error_message = "Failed to update the profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/update_profile.css">


    <script>
        function toggleForm() {
            const form = document.getElementById('profile-form');
            form.classList.toggle('show');
        }
    </script>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Profile</h1>

            <!-- Display success or error message -->
            <?php if (!empty($success_message)): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php elseif (!empty($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <!-- Display profile information -->
            <div class="profile-info">
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user_details['email']); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user_details['username']); ?></p>
                <p><strong><?php echo ($user_details['role'] === 'student') ? 'Student ID' : 'Staff ID'; ?>:</strong> 
                <?php echo htmlspecialchars($user_details['student_id']); ?></p>
            </div>

            <button onclick="toggleForm()">Edit Profile</button>

            <!-- Form to update profile -->
            <form id="profile-form" method="POST">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($_SESSION['first_name']); ?>" required>

                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($_SESSION['last_name']); ?>" >

                <label for="password">New Password (leave blank to keep current password):</label>
                <input type="password" id="password" name="password" placeholder="Enter new password">

                <button type="submit">Update</button>
            </form>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>
