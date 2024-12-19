<?php
// Include the database connection file
require_once 'db.php';

// Add CORS headers
header("Access-Control-Allow-Origin: http://localhost:5173"); // Allow requests from your frontend
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Allow POST and preflight OPTIONS methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow required headers

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Respond to preflight request
    http_response_code(200);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Validate input
    if (empty($student_id) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Student ID and password are required.']);
        exit;
    }

    try {
        // Prepare the SQL query
        $stmt = $pdo->prepare("SELECT * FROM participant WHERE student_id = :student_id");
        $stmt->bindParam(':student_id', $student_id, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the user data
        $user = $stmt->fetch();

        if ($user) {
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Successful login
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login successful!',
                    'student_id' => $user['student_id']
                ]);
            } else {
                // Invalid password
                echo json_encode(['status' => 'error', 'message' => 'Invalid Student ID or password.']);
            }
        } else {
            // No user found with the given student_id
            echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
