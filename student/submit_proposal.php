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

// Initialize success and error messages
$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the submitted proposal data
    $proposal_title = trim($_POST['proposal_title']);
    $proposal_description = trim($_POST['proposal_description']);

    // Handle file upload (optional, if students can upload files)
    $file_upload_success = false;
    if (isset($_FILES['proposal_file']) && $_FILES['proposal_file']['error'] === 0) {
        $file_name = $_FILES['proposal_file']['name'];
        $file_tmp_name = $_FILES['proposal_file']['tmp_name'];
        $file_path = 'uploads/' . basename($file_name);
        if (move_uploaded_file($file_tmp_name, $file_path)) {
            $file_upload_success = true;
        } else {
            $error_message = "Error uploading the file.";
        }
    }

    // Save proposal to the database (this example uses a dummy database interaction)
    if ($proposal_title && $proposal_description) {
        // Database logic for inserting the proposal (e.g., using PDO or MySQLi)
        // Assuming you have a database table `proposals` with columns for title, description, and file path.
        
        // Example: Save to database (replace with actual database connection and queries)
        $conn = new mysqli('localhost', 'username', 'password', 'database');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO proposals (user_id, title, description, file_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $_SESSION['user_id'], $proposal_title, $proposal_description, $file_upload_success ? $file_path : null);

        if ($stmt->execute()) {
            $success_message = "Your proposal has been submitted successfully!";
        } else {
            $error_message = "Error submitting your proposal. Please try again.";
        }

        $stmt->close();
        $conn->close();
    } else {
        $error_message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Proposal</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">

</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Submit Proposal</h1>

            <!-- Display success or error message -->
            <?php if (!empty($success_message)): ?>
                <p class="success-message"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <!-- Proposal form -->
            <form method="POST" enctype="multipart/form-data">
                <label for="proposal_title">Proposal Title:</label>
                <input type="text" id="proposal_title" name="proposal_title" required>

                <label for="proposal_description">Proposal Description:</label>
                <textarea id="proposal_description" name="proposal_description" required></textarea>

                <label for="proposal_file">Upload Proposal File (optional):</label>
                <input type="file" id="proposal_file" name="proposal_file">

                <button type="submit">Submit Proposal</button>
            </form>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>