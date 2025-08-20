<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_status':
        updateFeedbackStatusAction($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function updateFeedbackStatusAction($conn) {
    $feedback_id = intval($_POST['feedback_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    
    if ($feedback_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid feedback ID']);
        return;
    }
    
    if (!in_array($status, ['unread', 'read', 'replied'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        return;
    }
    
    if (updateFeedbackStatus($conn, $feedback_id, $status)) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating status']);
    }
}
?> 