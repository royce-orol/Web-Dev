<?php
session_start();

// Hardcoded team members
$team_members = [
    [
        "name" => "Haziq Zairul",
        "student_id" => "1221303388",
        "section" => "TC1L",
        "photo" => "uploads/haziq1.jpg",
        "contact" => "1221303388@student.mmu.edu.my"
    ],
    [
        "name" => "Lim Jia Hao",
        "student_id" => "1211101810",
        "section" => "TC1L",
        "photo" => "uploads/bryan.jpg",
        "contact" => "1211101810@student.mmu.edu.my"
    ],
    [
        "name" => "Muhammad Ammar Ajwad",
        "student_id" => "1211303991",
        "section" => "TC1L",
        "photo" => "uploads/ajwad.jpg",
        "contact" => "1211303991@student.mmu.edu.my"
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meet the Developers</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/team.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="dashboard-main">
            <h1>Meet the Developers</h1>

            <div class="team-container">
                <?php if (!empty($team_members)): ?>
                    <?php foreach ($team_members as $member): ?>
                        <div class="team-card">
                            <img src="<?php echo htmlspecialchars($member['photo']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" class="team-photo">
                            <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                            <p><strong>ID:</strong> <?php echo htmlspecialchars($member['student_id']); ?></p>
                            <p><strong>Section:</strong> <?php echo htmlspecialchars($member['section']); ?></p>
                            <p><strong>Contact:</strong> <a href="mailto:<?php echo htmlspecialchars($member['contact']); ?>"><?php echo htmlspecialchars($member['contact']); ?></a></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No team members available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
