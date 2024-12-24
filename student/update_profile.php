<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Retrieve user details
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated first and last name from the form
    $new_first_name = trim($_POST['first_name']);
    $new_last_name = trim($_POST['last_name']);

    // Update the session values (replace this with a database update in real implementation)
    $_SESSION['first_name'] = $new_first_name;
    $_SESSION['last_name'] = $new_last_name;

    // Success message
    $success_message = "Profile updated successfully!";
}
?>
<link rel="stylesheet" href="../css/update_profile.css">
<link rel="stylesheet" href="../css/dashboard.css">
<link rel="stylesheet" href="../css/header.css">




<?php include '../includes/header.php'; ?>
<div class="dashboard-container">
    <?php include '../includes/sidebar.php'; ?>
    <div class="dashboard-main">
        <h1>Update Profile</h1>
        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>

            <button type="submit">Update</button>
        </form>
    </div>
</div>
<?php include '../footer.php'; ?>