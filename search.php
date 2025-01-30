<?php
include 'db_connection.php'; // Include database connection

if (isset($_GET['query'])) {
    $search_query = $_GET['query']; // Get the search query from the URL
    $sql = "SELECT * FROM features WHERE feature_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_term = '%' . $search_query . '%';
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    $features = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $features = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="search-results">
        <h2>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h2>

        <?php if ($features): ?>
            <ul>
                <?php foreach ($features as $feature): ?>
                    <li><?php echo htmlspecialchars($feature['feature_name']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No results found for your query.</p>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
