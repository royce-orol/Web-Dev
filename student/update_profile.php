<?php
session_start();

// Include the database connection file
include('../db_connection.php');

// Retrieve user details from session
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$user_id = $_SESSION['user_id']; // Assuming you store user ID in the session

// Update profile when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update session variables
    $_SESSION['first_name'] = $_POST['first_name'];
    $_SESSION['last_name'] = $_POST['last_name'];

    // Get updated first name and last name
    $updated_first_name = $_POST['first_name'];
    $updated_last_name = $_POST['last_name'];

    // SQL query to update the user's profile in the database
    $query = "UPDATE users SET first_name = ?, last_name = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssi', $updated_first_name, $updated_last_name, $user_id);

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        $success_message = "Profile updated successfully.";
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
    <title>Update Profile</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Update Profile</h1>

            <!-- Display success or error message -->
            <?php if (!empty($success_message)): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php elseif (!empty($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <!-- Form to update profile -->
            <form method="POST">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($_SESSION['first_name']); ?>" required>

                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($_SESSION['last_name']); ?>" required>

                <button type="submit">Update</button>
            </form>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>
