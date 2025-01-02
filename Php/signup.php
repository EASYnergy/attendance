<?php
require_once 'db.php';

// Allow Cross-Origin requests for the frontend origin
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Accept");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Include the database connection
require_once 'db.php';

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON data sent by Svelte
    $input = json_decode(file_get_contents('php://input'), true);

    // Sanitize and validate inputs
    $firstname = filter_var($input['firstname'] ?? '', FILTER_SANITIZE_STRING);
    $lastname = filter_var($input['lastname'] ?? '', FILTER_SANITIZE_STRING);
    $email = filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $department = filter_var($input['department'] ?? '', FILTER_SANITIZE_STRING);
    $student_id = filter_var($input['student_id'] ?? '', FILTER_SANITIZE_STRING);
    $password = $input['password'] ?? '';

    if (empty($firstname) || empty($lastname) || empty($email) || empty($department) || empty($student_id) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    try {
        // Check if email or student_id already exists
        $checkStmt = $pdo->prepare("SELECT participant_id FROM participant WHERE email = :email OR student_id = :student_id");
        $checkStmt->bindParam(':email', $email, PDO::PARAM_STR);
        $checkStmt->bindParam(':student_id', $student_id, PDO::PARAM_STR);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Email or Student ID already exists.']);
            exit;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new participant into the database
        $stmt = $pdo->prepare("
            INSERT INTO participant 
            (firstname, lastname, email, department, student_id, password) 
            VALUES 
            (:firstname, :lastname, :email, :department, :student_id, :password)
        ");
        $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
        $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':department', $department, PDO::PARAM_STR);
        $stmt->bindParam(':student_id', $student_id, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Signup successful!']);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Signup failed. Please try again.']);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}
?>
