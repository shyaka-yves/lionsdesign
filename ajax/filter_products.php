<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'filter_by_price':
        filterByPrice($conn);
        break;
    case 'search_products':
        searchProducts($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function filterByPrice($conn) {
    $min_price = floatval($_POST['min_price'] ?? 0);
    $max_price = floatval($_POST['max_price'] ?? 999999);
    $category_id = $_POST['category_id'] ?? null;
    
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1";
    $params = [];
    
    if ($min_price > 0) {
        $sql .= " AND p.price >= ?";
        $params[] = $min_price;
    }
    
    if ($max_price > 0 && $max_price < 999999) {
        $sql .= " AND p.price <= ?";
        $params[] = $max_price;
    }
    
    if ($category_id) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category_id;
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'products' => $products
    ]);
}

function searchProducts($conn) {
    $search_term = trim($_POST['search_term'] ?? '');
    $category_id = $_POST['category_id'] ?? null;
    $min_price = floatval($_POST['min_price'] ?? 0);
    $max_price = floatval($_POST['max_price'] ?? 999999);
    
    if (empty($search_term)) {
        // If no search term, return all products with price filter
        filterByPrice($conn);
        return;
    }
    
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 
            AND (p.title LIKE ? OR p.description LIKE ?)";
    $params = ["%$search_term%", "%$search_term%"];
    
    if ($min_price > 0) {
        $sql .= " AND p.price >= ?";
        $params[] = $min_price;
    }
    
    if ($max_price > 0 && $max_price < 999999) {
        $sql .= " AND p.price <= ?";
        $params[] = $max_price;
    }
    
    if ($category_id) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category_id;
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'search_term' => $search_term
    ]);
}
?> 