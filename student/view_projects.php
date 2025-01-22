<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Projects</title>
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
            margin: 0;
        }

        .dashboard-main {
            flex-grow: 1;
            padding: 20px;
        }

        h1 {
            margin-bottom: 20px;
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
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?> <!-- Include header -->

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?> <!-- Include sidebar -->

        <div class="dashboard-main">
            <h1>View Projects</h1>

            <!-- Display projects in a table -->
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Project ID</th>
                        <th>Project Title</th>
                        <th>Proposal Status</th>
                        <th>Presentation Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['project_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['project_title']); ?></td>
                                <td><?php echo htmlspecialchars($row['proposal_status']); ?></td>
                                <td>
                                    <?php 
                                    echo $row['presentation_date'] 
                                        ? htmlspecialchars($row['presentation_date']) 
                                        : 'Not Scheduled'; 
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-data">No projects found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../footer.php'; ?> <!-- Include footer -->
</body>
</html>
