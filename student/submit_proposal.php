<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Proposal</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            display: flex;
            flex-direction: row;
        }

        .dashboard-main {
            flex-grow: 1;
            padding: 20px;
        }

        h1 {
            margin-bottom: 20px;
        }

        form {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            max-width: 600px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        button {
            background-color: #007BFF;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Submit Proposal</h1>

            <!-- Display success message after form submission -->
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $stmt): ?>
                <div class="success-message">Your proposal has been submitted successfully!</div>
            <?php endif; ?>

            <form method="POST">
                <label for="proposal_title">Proposal Title:</label>
                <input type="text" id="proposal_title" name="proposal_title" placeholder="Enter your proposal title" required>

                <label for="proposal_description">Proposal Description:</label>
                <textarea id="proposal_description" name="proposal_description" placeholder="Describe your proposal" required></textarea>

                <button type="submit">Submit Proposal</button>
            </form>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>
