<?php
// Ensure session is started
if (!session_id()) {
    session_start();
}

require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $session_id = generateSessionId();

    switch ($action) {
        case 'add_to_cart':
            $product_id = $_POST['product_id'] ?? 0;
            $quantity = $_POST['quantity'] ?? 1;
            
            if ($product_id && $quantity > 0) {
                if (addToCart($conn, $session_id, $product_id, $quantity)) {
                    $response['success'] = true;
                    $response['message'] = 'Product added to cart successfully!';
                } else {
                    $response['message'] = 'Error adding product to cart.';
                }
            } else {
                $response['message'] = 'Invalid product or quantity.';
            }
            break;

        case 'update_quantity':
            $cart_id = $_POST['cart_id'] ?? 0;
            $quantity = $_POST['quantity'] ?? 1;
            
            if ($cart_id && $quantity > 0) {
                if (updateCartQuantity($conn, $cart_id, $quantity)) {
                    $response['success'] = true;
                    $response['message'] = 'Cart updated successfully!';
                } else {
                    $response['message'] = 'Error updating cart.';
                }
            } else {
                $response['message'] = 'Invalid cart item or quantity.';
            }
            break;

        case 'remove_from_cart':
            $cart_id = $_POST['cart_id'] ?? 0;
            
            if ($cart_id) {
                if (removeFromCart($conn, $cart_id)) {
                    $response['success'] = true;
                    $response['message'] = 'Item removed from cart successfully!';
                } else {
                    $response['message'] = 'Error removing item from cart.';
                }
            } else {
                $response['message'] = 'Invalid cart item.';
            }
            break;

        case 'clear_cart':
            if (clearCart($conn, $session_id)) {
                $response['success'] = true;
                $response['message'] = 'Cart cleared successfully!';
            } else {
                $response['message'] = 'Error clearing cart.';
            }
            break;

        default:
            $response['message'] = 'Invalid action.';
            break;
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?> 