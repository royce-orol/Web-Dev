<?php
session_start();
include '../db_connection.php';

// Retrieve proposals from the proposal table
$query = "SELECT
            p.proposal_id,
            u1.first_name AS sender_first_name,
            u1.last_name AS sender_last_name,
            p.title,
            p.description,
            p.status,
            u2.first_name AS sv_first_name,
            u2.last_name AS sv_last_name,
            p.marks
          FROM proposal p
          JOIN users u1 ON p.sender_id = u1.id
          LEFT JOIN users u2 ON p.assigned_sv = u2.id
          ORDER BY p.proposal_id DESC";

$result = $conn->query($query);

// Check for errors in query execution
if (!$result) {
    die("Error retrieving proposals: " . $conn->error);
}

$proposals = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $proposals[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Proposals</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/header.css">
    <style>
        /* Tile container styling */
        .proposals-container {
            display: flex;
            flex-wrap: wrap; /* Allow tiles to wrap to the next line */
            justify-content: flex-start; /* Align tiles to the start */
            gap: 20px; /* Spacing between tiles */
            margin-top: 20px;
        }

        /* Individual proposal tile styling */
        .proposal-tile {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            width: calc(33% - 20px); /* Adjust width for 3 tiles per row with gap */
            min-width: 300px; /* Minimum width for each tile */
            box-sizing: border-box; /* Include padding and border in width */
        }

        .proposal-tile h3 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #333;
        }

        .proposal-tile p {
            margin: 5px 0;
            color: #555;
            font-size: 0.9em;
        }

        .proposal-tile p strong {
            font-weight: bold;
            color: #333;
        }

        /* Responsive adjustments for tile layout */
        @media (max-width: 1000px) {
            .proposal-tile {
                width: calc(50% - 20px); /* 2 tiles per row on medium screens */
            }
        }

        @media (max-width: 700px) {
            .proposal-tile {
                width: 100%; /* 1 tile per row on small screens */
            }
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Proposals</h1>

            <div class="proposals-container">
                <?php if (!empty($proposals)): ?>
                    <?php foreach ($proposals as $proposal): ?>
                        <div class="proposal-tile">
                            <h3><?php echo htmlspecialchars($proposal['title']); ?></h3>
                            <p><strong>Proposal ID:</strong> <?php echo htmlspecialchars($proposal['proposal_id']); ?></p>
                            <p><strong>Sender:</strong> <?php echo htmlspecialchars($proposal['sender_first_name']) . ' ' . htmlspecialchars($proposal['sender_last_name']); ?></p>
                            <p><strong>Status:</strong> <?php echo ucfirst(htmlspecialchars($proposal['status'])); ?></p>
                            <p><strong>Supervisor:</strong> <?php
                                echo $proposal['sv_first_name'] && $proposal['sv_last_name']
                                    ? htmlspecialchars($proposal['sv_first_name']) . ' ' . htmlspecialchars($proposal['sv_last_name'])
                                    : '<em>Not Assigned</em>';
                            ?></p>
                            <p><strong>Marks:</strong> <?php echo $proposal['marks'] !== null ? htmlspecialchars($proposal['marks']) : '<em>N/A</em>'; ?></p>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars(substr($proposal['description'], 0, 100)) . '...'; ?></p> <!-- Shorten description, you can adjust length -->
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No proposals available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../footer.php'; ?>
</body>
</html>