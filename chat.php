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

// Retrieve announcements from the database
$announcements = [];
$query = "SELECT description FROM Announcements ORDER BY id DESC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row['description'];
    }
}

// Fetch all users except the logged-in user
$users = [];
$user_query = "SELECT id, first_name, last_name, role FROM users WHERE id != $user_id";
$user_result = $conn->query($user_query);

if ($user_result && $user_result->num_rows > 0) {
    while ($row = $user_result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Handle sending messages
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id'], $_POST['message'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = $conn->real_escape_string($_POST['message']);
    $conn->query("INSERT INTO chats (sender_id, receiver_id, message) VALUES ($user_id, $receiver_id, '$message')");
}

// Determine the receiver for the chat
$receiver_id = isset($_GET['receiver_id']) ? $_GET['receiver_id'] : (isset($users[0]['id']) ? $users[0]['id'] : null);

// Fetch chat messages if a receiver is selected
$messages = [];
if ($receiver_id) {
    $message_query = "
        SELECT * FROM chats
        WHERE (sender_id = $user_id AND receiver_id = $receiver_id)
           OR (sender_id = $receiver_id AND receiver_id = $user_id)
        ORDER BY created_at";
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
    <style>
        .dashboard-container {
            display: flex;
            height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #f4f4f4;
            padding: 15px;
        }
        .chat-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .user-list {
            display: flex;
            overflow-x: auto;
            border-bottom: 1px solid #ccc;
            padding: 20px 20px;
            background-color: #f9f9f9;
        }
        .user-list ul {
            display: flex;
            gap: 10px;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .user-list li {
            flex: 0 0 auto;
            padding: 8px 15px;
            border-radius: 18px;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: background-color 0.2s ease, transform 0.2s ease;
            position: relative;
        }
        .user-list li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            display: block;
        }
        .user-list li:hover {
            background-color: #eee;
            transform: translateY(-2px);
        }
        .user-list li a[style*="font-weight: bold;"] {
            font-weight: bold;
            color: #007bff;
        }
        /* REMOVED UNREAD STYLING */
        /*.user-list li.unread {
            background-color: #ffcccc;
        }
        .user-list li.unread a {
            color: #800000;
            font-weight: bold;
        }*/


        .chat-box {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 6px;
            margin: 0 10px 10px 10px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
            min-height: 400px;
        }
        .messages {
            flex-grow: 1;
            overflow-y: auto;
            padding: 10px;
            border-bottom: 1px solid #eee;
            max-height: 65vh;
            display: flex;
            flex-direction: column;
        }
        .messages p {
            padding: 20px 20px;
            border-radius: 12px;
            background-color: #f0f0f0;
            margin-bottom: 8px;
            align-self: flex-start;
            position: relative;
            font-size: 0.9em;
            word-wrap: break-word;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .messages p strong {
            font-weight: bold;
            color: #333;
        }

        .messages p.sent {
            background-color: #e8f0fe;
            align-self: flex-end;
            padding: 20px 20px;
            font-size: 1.0em;
        }

        .messages p .timestamp {
            position: absolute;
            bottom: 2px;
            right: 8px;
            font-size: 0.75em;
            color: #777;
        }


        .message-form {
            padding: 10px;
            display: flex;
            background-color: transparent;
            border-top: 1px solid #ddd;
            align-items: center;
            gap: 8px;
        }
        .message-form input {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 18px;
            margin-bottom: 0;
            flex-grow: 1;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
            height: 45px;
        }
        .message-form button {
            padding: 12px 22px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 18px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            font-weight: normal;
            height: 45px;
        }
        .message-form button:hover {
            background-color: #0056b3;
        }
    </style>
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
                        <li class="<?= $receiver_id != $user['id'] && rand(0, 1) ? 'unread' : '' ?>"><!-- Removed unread class here for demo -->
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
                                <strong><?= $msg['sender_id'] == $user_id ? 'You' : 'User ' . $msg['sender_id'] ?>:</strong>
                                <?= $msg['message'] ?>
                                <span class="timestamp">
                                    <?= date('h:i A', strtotime($msg['created_at'])) ?>
                                </span>
                            </p>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #777;">Select a user to start chatting.</p>
                    <?php endif; ?>
                </div>

                <?php if ($receiver_id): ?>
                    <form method="POST" class="message-form">
                        <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
                        <input type="text" name="message" placeholder="Type your message..." required>
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