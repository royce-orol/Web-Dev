<?php
session_start();


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection
include('../db_connection.php'); 


// Retrieve student proposals with approved status
$user_id = $_SESSION['user_id'];
$sql = "SELECT proposal_id, title 
        FROM proposal 
        WHERE status = 'approved' AND sender_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error retrieving proposals: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Presentation</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Book Presentation</h1>

            <!-- Check if there are any approved proposals -->
            
                <form action="submit_presentation.php" method="POST">
                    <label for="proposal">Select Proposal:</label>
                    <select name="proposal_id" id="proposal">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['proposal_id']); ?>">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <label for="date">Select Date:</label>
                    <input type="date" name="date" required>

                    <label for="time">Select Time:</label>
                    <input type="time" name="time" required>

                    <button type="submit">Book Presentation</button>
                </form>
           
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>