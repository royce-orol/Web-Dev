<?php
session_start();
include('../db_connection.php'); 

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Initialize messages
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add announcement
    if (isset($_POST['add_announcement'])) {
        $description = htmlspecialchars(trim($_POST['description']));
        if (!empty($description)) {
            $stmt = $conn->prepare("INSERT INTO announcements (description) VALUES (?)");
            if ($stmt) {
                $stmt->bind_param("s", $description);
                if ($stmt->execute()) {
                    $success_message = "Announcement added successfully!";
                } else {
                    $error_message = "Error adding announcement: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Error preparing statement: " . $conn->error;
            }
        } else {
            $error_message = "Description cannot be empty!";
        }
    }

    // Edit announcement
    if (isset($_POST['edit_announcement'])) {
        $id = intval($_POST['id']);
        $description = htmlspecialchars(trim($_POST['description']));
        if (!empty($description)) {
            $stmt = $conn->prepare("UPDATE announcements SET description = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("si", $description, $id);
                if ($stmt->execute()) {
                    $success_message = "Announcement updated successfully!";
                } else {
                    $error_message = "Error updating announcement: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Error preparing statement: " . $conn->error;
            }
        } else {
            $error_message = "Description cannot be empty!";
        }
    }

    // Delete announcement
    if (isset($_POST['delete_announcement'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $success_message = "Announcement deleted successfully!";
            } else {
                $error_message = "Error deleting announcement: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Error preparing statement: " . $conn->error;
        }
    }
}

// Fetch all announcements
$result = $conn->query("SELECT * FROM announcements ORDER BY id DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Manage Announcements</h1>

            <!-- Display success or error message -->
            <?php if (!empty($success_message)): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php elseif (!empty($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <!-- Add Announcement Form -->
            <form method="POST">
                <h2>Add Announcement</h2>
                <textarea name="description" rows="3" placeholder="Enter announcement" required></textarea>
                <button type="submit" name="add_announcement">Add Announcement</button>
            </form>

            <hr>

            <!-- List of Announcements -->
            <h2>Current Announcements</h2>
            <?php if ($result && $result->num_rows > 0): ?>
                <ul>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <li>
                            <strong>#<?php echo $row['id']; ?></strong>: <?php echo htmlspecialchars($row['description']); ?>

                            <!-- Edit Form -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="text" name="description" value="<?php echo htmlspecialchars($row['description']); ?>" required>
                                <button type="submit" name="edit_announcement">Edit</button>
                            </form>

                            <!-- Delete Form -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete_announcement" onclick="return confirm('Are you sure you want to delete this announcement?');">Delete</button>
                            </form>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No announcements found.</p>
            <?php endif; ?>

        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>