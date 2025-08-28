<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../includes/functions.php';

try {
    // Check database connection
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'redirect' => 'login.php']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $service_id = intval($_POST['service_id'] ?? 0);
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $file_path = '';

    // Validate required fields
    if (empty($service_id) || empty($full_name) || empty($phone)) {
        echo json_encode(['success' => false, 'error' => 'Please fill in all required fields.']);
        exit;
    }

    // Validate file
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg','jpeg','png','pdf'];
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            echo json_encode(['success' => false, 'error' => 'Invalid file type. Only jpg, jpeg, png, pdf allowed.']);
            exit;
        }
        if ($_FILES['file']['size'] > 5*1024*1024) {
            echo json_encode(['success' => false, 'error' => 'File too large. Maximum 5MB allowed.']);
            exit;
        }
        $filename = uniqid().'.'.$ext;
        $target = '../uploads/services/'.$filename;
        
        // Ensure directory exists
        if (!is_dir('../uploads/services/')) {
            mkdir('../uploads/services/', 0777, true);
        }
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
            $file_path = 'uploads/services/'.$filename;
        } else {
            echo json_encode(['success' => false, 'error' => 'File upload failed. Please try again.']);
            exit;
        }
    }

    // Save to service_requests
    $stmt = $conn->prepare('INSERT INTO service_requests (user_id, service_id, full_name, phone, file, message) VALUES (?, ?, ?, ?, ?, ?)');
    if (!$stmt->execute([$user_id, $service_id, $full_name, $phone, $file_path, $message])) {
        echo json_encode(['success' => false, 'error' => 'Failed to save service request.']);
        exit;
    }
    $request_id = $conn->lastInsertId();

    // Add to cart (as a special product type)
    $session_id = generateSessionId();
    $stmt = $conn->prepare('INSERT INTO cart (session_id, product_id, quantity, created_at) VALUES (?, ?, ?, NOW())');
    if (!$stmt->execute([$session_id, -$request_id, 1])) {
        echo json_encode(['success' => false, 'error' => 'Failed to add to cart.']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Service requested and added to cart!']);

} catch (Exception $e) {
    error_log('Service request error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'An error occurred: ' . $e->getMessage()]);
}
?>