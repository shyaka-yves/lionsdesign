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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$feedback_id = intval($_GET['id'] ?? 0);

if ($feedback_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid feedback ID']);
    exit();
}

$feedback = getFeedbackById($conn, $feedback_id);

if (!$feedback) {
    echo json_encode(['success' => false, 'message' => 'Feedback not found']);
    exit();
}

echo json_encode([
    'success' => true,
    'feedback' => $feedback
]);
?> 