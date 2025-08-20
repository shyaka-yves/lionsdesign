<?php
// Ensure session is started
if (!session_id()) {
    session_start();
}

require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$session_id = generateSessionId();
$cart_items = getCart($conn, $session_id);
$cart_total = getCartTotal($conn, $session_id);

// Debug information
$debug_info = [];
foreach ($cart_items as $item) {
    $debug_info[] = [
        'product' => $item['title'],
        'price' => $item['price'],
        'quantity' => $item['quantity'],
        'subtotal' => $item['price'] * $item['quantity']
    ];
}

echo json_encode([
    'success' => true,
    'total' => $cart_total,
    'formatted_total' => formatPrice($cart_total),
    'debug' => $debug_info,
    'session_id' => $session_id
]);
?>
