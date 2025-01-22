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

// Sample announcements data (replace with database retrieval)
$announcements = [
    ["title" => "Welcome to the FYP Management System!", "content" => "Please check your assigned projects and schedules."],
    ["title" => "Submission Deadline", "content" => "Ensure your proposals are submitted by December 20th."],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">

</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Announcements</h1>
            <?php foreach ($announcements as $announcement): ?>
                <div class="announcement">
                    <h2><?php echo htmlspecialchars($announcement['title']); ?></h2>
                    <p><?php echo htmlspecialchars($announcement['content']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>