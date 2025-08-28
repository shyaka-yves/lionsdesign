<?php
// Simple test file to debug search
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

echo "<h2>Search Test</h2>";

// Test database connection
if (isset($conn)) {
    echo "<p>✅ Database connection: OK</p>";
} else {
    echo "<p>❌ Database connection: FAILED</p>";
    exit;
}

// Test products table
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>✅ Products table: " . $result['count'] . " active products found</p>";
} catch (Exception $e) {
    echo "<p>❌ Products table error: " . $e->getMessage() . "</p>";
}

// Test search functionality
$search_term = "crystal"; // Test search term
try {
    $product_sql = "SELECT id, title, description, price, image, stock_quantity FROM products WHERE is_active = 1 AND title LIKE ? ORDER BY title ASC LIMIT 5";
    
    $search_param = '%' . $search_term . '%';
    $stmt = $conn->prepare($product_sql);
    $stmt->execute([$search_param]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>✅ Search test for '$search_term': " . count($products) . " results</p>";
    
    if (count($products) > 0) {
        echo "<ul>";
        foreach ($products as $product) {
            echo "<li>" . htmlspecialchars($product['title']) . " - " . $product['price'] . " Rwf</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Search test error: " . $e->getMessage() . "</p>";
}

// Test AJAX endpoint
echo "<h3>Testing AJAX endpoint:</h3>";
echo "<button onclick='testSearch()'>Test Search</button>";
echo "<div id='searchResult'></div>";

?>

<script>
function testSearch() {
    const formData = new FormData();
    formData.append('search_term', 'crystal');
    formData.append('limit', '5');
    
    fetch('ajax/global_search.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('searchResult').innerHTML = '<pre>' + data + '</pre>';
    })
    .catch(error => {
        document.getElementById('searchResult').innerHTML = '<p style="color: red;">Error: ' + error + '</p>';
    });
}
</script>
