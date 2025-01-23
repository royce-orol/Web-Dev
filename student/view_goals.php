<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Specify character encoding for the document -->
    <meta charset="UTF-8">
    <!-- Ensure proper scaling and responsiveness on different devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Set the title of the webpage -->
    <title>Your Goals</title>
    <!-- Link to the dashboard's CSS file -->
    <link rel="stylesheet" href="../css/dashboard.css">
    <!-- Link to the header's CSS file -->
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
            margin: 0;
        }

        .dashboard-main {
            flex-grow: 1;
            padding: 20px;
        }

        h1 {
            margin-bottom: 20px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        td {
            color: #333;
        }

        .no-data {
            text-align: center;
            font-style: italic;
        }

        input[type="checkbox"] {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Include the header file for navigation -->
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <!-- Include the sidebar for navigation links -->
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <!-- Main heading of the page -->
            <h1>Your Goals</h1>

            <div class="table-container">
                <!-- Create a table to display the list of goals -->
                <table>
                    <thead>
                        <tr>
                            <!-- Table column headers -->
                            <th>Goal Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Check if there are any goals in the result -->
                        <?php if ($result->num_rows > 0): ?>
                            <!-- Loop through each goal in the result set -->
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <!-- Display the goal title -->
                                    <td><?php echo htmlspecialchars($row['goal_title']); ?></td>
                                    <!-- Display the goal description -->
                                    <td><?php echo htmlspecialchars($row['goal_description']); ?></td>
                                    <!-- Display the goal status as "Completed" or "Not Completed" -->
                                    <td><?php echo $row['is_completed'] ? 'Completed' : 'Not Completed'; ?></td>
                                    <td>
                                        <!-- Form to update the completion status of the goal -->
                                        <form method="POST">
                                            <!-- Hidden input to store the goal ID -->
                                            <input type="hidden" name="goal_id" value="<?php echo $row['goal_id']; ?>">
                                            <!-- Checkbox to mark the goal as completed -->
                                            <input type="checkbox" name="is_completed" <?php echo $row['is_completed'] ? 'checked' : ''; ?> onchange="this.form.submit();">
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <!-- Message displayed if no goals are assigned -->
                            <tr>
                                <td colspan="4" class="no-data">No goals have been assigned.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Include the footer file -->
    <?php include '../footer.php'; ?>
</body>
</html>
