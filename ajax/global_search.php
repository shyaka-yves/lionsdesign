<?php
session_start();

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once '../config/database.php';
    require_once '../includes/functions.php';
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to load required files: ' . $e->getMessage()]);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$search_term = isset($_POST['search_term']) ? trim($_POST['search_term']) : '';
$limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;

if (empty($search_term)) {
    echo json_encode(['success' => false, 'message' => 'Search term is required']);
    exit;
}

// Check if database connection exists
if (!isset($conn)) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    $results = [];
    $search_param = '%' . $search_term . '%';
    
    // Calculate limits for products and services (split the total limit)
    $product_limit = intval($limit / 2);
    $service_limit = $limit - $product_limit;
    
    // Search Products
    $product_sql = "SELECT id, title, description, price, image, stock_quantity FROM products WHERE is_active = 1 AND (title LIKE ? OR description LIKE ?) ORDER BY title ASC LIMIT " . $product_limit;
    
    $stmt = $conn->prepare($product_sql);
    $stmt->execute([$search_param, $search_param]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($products as $product) {
        $price_formatted = number_format($product['price'], 2) . ' Rwf';
        $results[] = [
            'id' => $product['id'],
            'title' => $product['title'],
            'description' => substr($product['description'], 0, 100) . '...',
            'price' => $price_formatted,
            'image' => $product['image'],
            'type' => 'product',
            'url' => 'shop.php#product-' . $product['id'],
            'stock_quantity' => $product['stock_quantity'],
            'in_stock' => $product['stock_quantity'] > 0
        ];
    }
    
    // Search Services
    $service_sql = "SELECT id, title, description, price, image FROM services WHERE (title LIKE ? OR description LIKE ?) ORDER BY title ASC LIMIT " . $service_limit;
    
    $stmt = $conn->prepare($service_sql);
    $stmt->execute([$search_param, $search_param]);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($services as $service) {
        $results[] = [
            'id' => $service['id'],
            'title' => $service['title'],
            'description' => substr($service['description'], 0, 100) . '...',
            'price' => 'Starting at ' . $service['price'],
            'image' => $service['image'],
            'type' => 'service',
            'url' => 'services.php#service-' . $service['id'],
            'stock_quantity' => null,
            'in_stock' => true // Services are always "available"
        ];
    }
    
    echo json_encode([
        'success' => true,
        'results' => $results,
        'total' => count($results),
        'search_term' => $search_term
    ]);
    
} catch (Exception $e) {
    error_log("Global search error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Search failed: ' . $e->getMessage()
    ]);
}
?>
