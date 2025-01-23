<?php
session_start();

include 'db_connection.php'; // Include the database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all users' emails
$query = "SELECT email, first_name, last_name FROM users";
$result = $conn->query($query);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email List</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/header.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Email List</h1>
            <?php if (!empty($users)): ?>
                <ul>
                    <?php foreach ($users as $user): ?>
                        <li>
                            <!-- Create mailto links -->
                            <a href="mailto:<?= htmlspecialchars($user['email']); ?>">
                                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?> (<?= htmlspecialchars($user['email']); ?>)
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No users found.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
