<?php
session_start();
include('../db_connection.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ensure the uploads directory exists
$upload_dir = "../uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Initialize messages
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add announcement
    if (isset($_POST['add_announcement'])) {
        $description = htmlspecialchars(trim($_POST['description']));
        $file_path = '';

        // Handle file upload
        if (!empty($_FILES['file']['name'])) {
            $file_name = time() . "_" . preg_replace('/[^a-zA-Z0-9.\-_]/', '_', $_FILES["file"]["name"]); // Sanitize file name
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $file_path = $file_name; // Store only the file name in DB
            } else {
                $error_message = "Error uploading file: " . $_FILES['file']['error'];
            }
        }

        if (!empty($description)) {
            $stmt = $conn->prepare("INSERT INTO Announcements (description, file_name) VALUES (?, ?)");
            if ($stmt) {
                $stmt->bind_param("ss", $description, $file_path);
                if ($stmt->execute()) {
                    $success_message = "Announcement added successfully!";
                } else {
                    $error_message = "Error adding announcement: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Database error: " . $conn->error;
            }
        } else {
            $error_message = "Description cannot be empty!";
        }
    }

    // Edit announcement
    if (isset($_POST['edit_announcement'])) {
        $id = intval($_POST['id']);
        $description = htmlspecialchars(trim($_POST['description']));
        $file_path = '';

        // Handle file upload
        if (!empty($_FILES['file']['name'])) {
            $file_name = time() . "_" . preg_replace('/[^a-zA-Z0-9.\-_]/', '_', $_FILES["file"]["name"]); // Sanitize file name
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $file_path = $file_name;
            } else {
                $error_message = "Error uploading file: " . $_FILES['file']['error'];
            }
        }

        if (!empty($description)) {
            if (!empty($file_path)) {
                $stmt = $conn->prepare("UPDATE announcements SET description = ?, file_path = ? WHERE id = ?");
                $stmt->bind_param("ssi", $description, $file_path, $id);
            } else {
                $stmt = $conn->prepare("UPDATE announcements SET description = ? WHERE id = ?");
                $stmt->bind_param("si", $description, $id);
            }

            if ($stmt->execute()) {
                $success_message = "Announcement updated successfully!";
            } else {
                $error_message = "Error updating announcement: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Description cannot be empty!";
        }
    }

    // Delete selected announcements
    if (isset($_POST['delete_selected'])) {
        if (!empty($_POST['selected_announcements'])) {
            $ids = implode(",", array_map('intval', $_POST['selected_announcements']));
            $stmt = $conn->prepare("DELETE FROM announcements WHERE id IN ($ids)");
            if ($stmt->execute()) {
                $success_message = "Selected announcements deleted successfully!";
            } else {
                $error_message = "Error deleting announcements: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Please select at least one announcement to delete.";
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
    <link rel="stylesheet" href="../css/forms.css">
    <link rel="stylesheet" href="../css/announcement.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Manage Announcements</h1>

            <!-- Display success or error message -->
            <?php if (!empty($error_message)): ?>
                <div class="message error-message"><strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?></div>
            <?php elseif (!empty($success_message)): ?>
                <div class="message success-message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <!-- Add Announcement Form -->
            <section class="announcement-form">
                <h2>Add Announcement</h2>
                <form method="POST" enctype="multipart/form-data">
                    <textarea name="description" rows="4" placeholder="Enter your announcement..." required></textarea>
                    <input type="file" name="file">
                    <button type="submit" name="add_announcement" class="btn btn-primary">Add Announcement</button>
                </form>
            </section>

            <hr>

            <!-- List of Announcements -->
            <section class="announcement-list">
                <h2>Current Announcements</h2>
                <form method="POST">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <ul>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <li class="announcement-item">
                                    <?php echo htmlspecialchars($row['description']); ?>
                                    <?php if (!empty($row['file_path'])): ?>
                                        <a href="../uploads/<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank">Download File</a>
                                    <?php endif; ?>
                                    <div class="button-container">
                                        <!-- Edit Button triggers modal -->
                                        <button type="button" class="btn btn-primary" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['description']); ?>')">Edit</button>
                                        <!-- Checkbox for selection -->
                                        <input type="checkbox" name="selected_announcements[]" value="<?php echo $row['id']; ?>">
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p>No announcements found.</p>
                    <?php endif; ?>
                    
                    <!-- Delete Selected Button -->
                    <button type="submit" name="delete_selected" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete the selected announcements?');">Delete</button>
                </form>
            </section>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-content">
            <h3>Edit Announcement</h3>
            <form id="editForm" method="POST" class="edit-form" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editId">
                <textarea name="description" id="editDescription" rows="4" required></textarea>
                <input type="file" name="file">
                <button type="submit" name="edit_announcement" class="btn btn-primary">Save Changes</button>
                <button type="button" class="btn btn-danger" onclick="closeEditModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, description) {
            document.getElementById('editId').value = id;
            document.getElementById('editDescription').value = description;
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>

    <?php include '../includes/footer.php'; ?>

</body>
</html>
