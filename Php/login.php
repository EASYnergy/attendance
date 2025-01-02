<?php
// Include the database connection file
require_once 'db.php';

// Add CORS headers
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON data sent by Svelte
    $input = json_decode(file_get_contents('php://input'), true);

    // Sanitize and validate input
    $student_id = filter_var($input['student_id'] ?? '', FILTER_SANITIZE_STRING);
    $password = $input['password'] ?? '';

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
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify the password
            if (password_verify($password, $user['password'])) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login successful!',
                    'student_id' => $user['student_id'],
                    'firstname' => $user['firstname'],
                    'lastname' => $user['lastname'],
                    'email' => $user['email'],
                    'department' => $user['department']
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid Student ID or password.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}
?>
