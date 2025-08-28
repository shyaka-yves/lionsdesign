<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'search_services':
        searchServices($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function searchServices($conn) {
    $search_term = trim($_POST['search_term'] ?? '');
    
    if (empty($search_term)) {
        // If no search term, return all services
        $sql = "SELECT * FROM services ORDER BY id DESC";
        $params = [];
    } else {
        // Search in title and description
        $sql = "SELECT * FROM services WHERE title LIKE ? OR description LIKE ? ORDER BY id DESC";
        $params = ["%$search_term%", "%$search_term%"];
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'services' => $services,
        'search_term' => $search_term
    ]);
}
?>
