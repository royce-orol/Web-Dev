<?php
// Include database connection
include('../db_connection.php'); // Adjusted for consistent relative path

session_start();

// Query to fetch supervisors' names (combined first and last name) and their emails
$sql = "SELECT CONCAT(first_name, ' ', last_name) AS name, email FROM users WHERE role = 'supervisor'";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Supervisors</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/view_supervisors.css">
   
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>View List of Supervisors</h1>

            <!-- Display supervisors in a table -->
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="no-data">No supervisors found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>
