<?php
include('../db_connection.php');

if (isset($_POST['student_id'])) {
    $student_id = intval($_POST['student_id']);

    // Check if student ID exists in the 'users' table and has the role 'student'
    $query = "SELECT id FROM users WHERE id = ? AND role = 'student'";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(['exists' => false, 'error' => 'Database error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $stmt->store_result();

    // Prepare response
    $response = ['exists' => false];

    if ($stmt->num_rows > 0) {
        $response['exists'] = true;
    }

    echo json_encode($response);
} else {
    echo json_encode(['exists' => false, 'error' => 'No student_id received']);
}
?>
