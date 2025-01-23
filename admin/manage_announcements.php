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

    <style>
        .announcement-item {
            margin-bottom: 10px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            background-color: #f9f9f9;
        }

        .announcement-item:hover {
            background-color: #e9e9e9;
        }

        .announcement-item strong {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .button-container {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn {
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            border: none;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background-color: #007bff;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .announcement-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .announcement-form h2 {
            margin-bottom: 15px;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .announcement-form textarea {
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
            margin-bottom: 15px;
            min-height: 120px;
            resize: vertical;
        }

        .announcement-list {
            margin-top: 30px;
        }

        .announcement-list h2 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
        }

        /* Edit Form Styling */
        .edit-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .edit-form input, .edit-form textarea {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .edit-form button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            color: #fff;
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-form button:hover {
            background-color: #218838;
        }

        /* Modal Styling */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .modal-content h3 {
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .modal-content button {
            background-color: #dc3545;
        }

        .modal-content button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Manage Announcements</h1>

            <!-- Display success or error message -->
            <?php if (!empty($success_message)): ?>
                <div class="message success-message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php elseif (!empty($error_message)): ?>
                <div class="message error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <!-- Add Announcement Form -->
            <section class="announcement-form">
                <h2>Add Announcement</h2>
                <form method="POST">
                    <textarea name="description" rows="4" placeholder="Enter your announcement..." required></textarea>
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
            <form id="editForm" method="POST" class="edit-form">
                <input type="hidden" name="id" id="editId">
                <textarea name="description" id="editDescription" rows="4" required></textarea>
                <button type="submit" name="edit_announcement" class="btn btn-primary">Save Changes</button>
                <button type="button" class="btn btn-danger" onclick="closeEditModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        // Function to open the edit modal with pre-filled data
        function openEditModal(id, description) {
            document.getElementById('editId').value = id;
            document.getElementById('editDescription').value = description;
            document.getElementById('editModal').style.display = 'flex';
        }

        // Function to close the edit modal
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>

    <?php include '../footer.php'; ?>
</body>
</html>
