<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Retrieve user details from session
$user_id = $_SESSION['user_id'];
$name = $_SESSION['first_name'] . " " . $_SESSION['last_name'];
$role = $_SESSION['role'];

$query = "SELECT m.message, m.sender_id, m.created_at, u.first_name, u.last_name, m.file_name
          FROM messages m
          JOIN users u ON m.sender_id = u.id
          WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
          ORDER BY m.created_at ASC";

// Fetch all users except the logged-in user
$users = [];
$user_query = "SELECT id, first_name, last_name, role FROM users WHERE id != $user_id";
$user_result = $conn->query($user_query);

if ($user_result && $user_result->num_rows > 0) {
    while ($row = $user_result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Handle sending messages and file uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = isset($_POST['message']) ? $conn->real_escape_string($_POST['message']) : "";
    $file_name = "";

    // Handle file upload
    if (!empty($_FILES['file']['name'])) {
        $upload_dir = "uploads/";
        $file_name = basename($_FILES['file']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            // File uploaded successfully
        } else {
            $file_name = ""; // Reset file name on failure
        }
    }

    $conn->query("INSERT INTO chats (sender_id, receiver_id, message, file_name) 
                  VALUES ($user_id, $receiver_id, '$message', '$file_name')");
}

// Determine the receiver for the chat
$receiver_id = isset($_GET['receiver_id']) ? $_GET['receiver_id'] : (isset($users[0]['id']) ? $users[0]['id'] : null);

// Fetch chat messages if a receiver is selected
$messages = [];
if ($receiver_id) {
    $message_query = "
       SELECT c.*, u.first_name, u.last_name
        FROM chats c
        JOIN users u ON c.sender_id = u.id
        WHERE (c.sender_id = $user_id AND c.receiver_id = $receiver_id)
           OR (c.sender_id = $receiver_id AND c.receiver_id = $user_id)
        ORDER BY c.created_at";
    $message_result = $conn->query($message_query);

    if ($message_result && $message_result->num_rows > 0) {
        while ($row = $message_result->fetch_assoc()) {
            $messages[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/chat.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="chat-container">
            <!-- User List -->
            <div class="user-list">
                <ul>
                    <?php foreach ($users as $user): ?>
                        <li class="<?= $receiver_id != $user['id'] && rand(0, 1) ? 'unread' : '' ?>">
                            <a href="?receiver_id=<?= $user['id'] ?>"
                               style="<?= $receiver_id == $user['id'] ? 'font-weight: bold;' : '' ?>">
                                <?= $user['first_name'] . ' ' . $user['last_name'] ?>
                                <span style="font-size: 12px; color: #666;">(<?= ucfirst($user['role']) ?>)</span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Chat Box -->
            <div class="chat-box">
                <div class="messages" id="messages-container">
                    <?php if ($receiver_id): ?>
                        <?php foreach ($messages as $msg): ?>
                            <p class="<?= $msg['sender_id'] == $user_id ? 'sent' : 'received' ?>">
                                <strong><?= $msg['sender_id'] == $user_id ? 'You' : htmlspecialchars($msg['first_name']) . ' ' . htmlspecialchars($msg['last_name']) ?>:</strong>

                                <?= $msg['message'] ?>
                                <span class="timestamp">
                                    <?= date('h:i A', strtotime($msg['created_at'])) ?>
                                </span>
                                <!-- File Download Link -->
                                <?php if (!empty($msg['file_name'])): ?>
                                    <br><a href="uploads/<?= htmlspecialchars($msg['file_name']) ?>" download><?= htmlspecialchars($msg['file_name']) ?></a>
                                <?php endif; ?>
                            </p>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #777;">Select a user to start chatting.</p>
                    <?php endif; ?>
                </div>

                <?php if ($receiver_id): ?>
                    <form method="POST" enctype="multipart/form-data" class="message-form">
                        <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
                        <input type="text" name="message" placeholder="Type your message...">
                        <input type="file" name="file">
                        <button type="submit">Send</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Scroll chat messages to the bottom on load
        const messagesContainer = document.getElementById('messages-container');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    </script>
</body>
</html>
