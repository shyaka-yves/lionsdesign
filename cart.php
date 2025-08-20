<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Ensure session is started
if (!session_id()) {
    session_start();
}

$session_id = generateSessionId();
$cart_items = getCart($conn, $session_id);
$cart_total = getCartTotal($conn, $session_id);

// Debug: Log cart information
error_log("Cart debug - Session: $session_id, Items: " . count($cart_items) . ", Total: $cart_total");
foreach ($cart_items as $item) {
    error_log("Item: {$item['title']} - Price: {$item['price']} - Qty: {$item['quantity']} - Subtotal: " . ($item['price'] * $item['quantity']));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Mobile-first responsive cart styles */
        .mobile-cart-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .mobile-cart-header {
            background: white;
            color: black;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .cart-item-mobile {
            background: white;
            border-radius: 15px;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .cart-item-mobile:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border-radius: 25px;
            padding: 0.25rem;
        }
        
        .quantity-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: #28a745;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }
        
        .quantity-btn:hover {
            background: #218838;
            transform: scale(1.1);
        }
        
        .quantity-input-mobile {
            border: none;
            background: transparent;
            text-align: center;
            width: 40px;
            font-weight: 600;
            color: #333;
        }
        
        .cart-summary-mobile {
            background: white;
            border-radius: 20px 20px 0 0;
            padding: 1.5rem;
            position: sticky;
            bottom: 0;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.1);
        }
        
        .checkout-btn-mobile {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 25px;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
        }
        
        .checkout-btn-mobile:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }
        
        .empty-cart-mobile {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }
        
        .empty-cart-icon {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }
        
        .remove-btn-mobile {
            background: #dc3545;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }
        
        .remove-btn-mobile:hover {
            background: #c82333;
            transform: scale(1.1);
        }
        
        .price-mobile {
            font-size: 1.1rem;
            font-weight: 700;
            color: #28a745;
        }
        
        .service-badge {
            background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        /* Desktop styles */
        @media (min-width: 768px) {
            .mobile-only {
                display: none;
            }
            
            .desktop-cart {
                display: block;
            }
        }
        
        /* Mobile styles */
        @media (max-width: 767px) {
            .desktop-cart {
                display: none;
            }
            
            .mobile-only {
                display: block;
            }
            
            .container {
                padding: 0;
            }
            
            .mobile-cart-container {
                padding: 0;
            }
        }
        
        .payment-methods-mobile {
            background: white;
            border-radius: 15px;
            padding: 1rem;
            margin: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }
        
        .payment-method-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .payment-method-item:last-child {
            border-bottom: none;
        }
        
        .payment-method-icon {
            width: 40px;
            height: 40px;
            background: #f8f9fa;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Mobile Cart Layout -->
    <div class="mobile-only">
        <div class="mobile-cart-container">
            <!-- Mobile Header -->
            <div class="mobile-cart-header">
                <div class="container-fluid px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <a href="shop.php" class="text-white me-3">
                                <i class="fas fa-arrow-left fa-lg"></i>
                            </a>
                            <h4 class="mb-0 fw-bold">Shopping Cart</h4>
                        </div>
                        <?php if (!empty($cart_items)): ?>
                            <span class="badge bg-white text-success rounded-pill px-3 py-2">
                                <?php echo count($cart_items); ?> items
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="container-fluid px-3 py-3">
                <?php if (empty($cart_items)): ?>
                    <div class="empty-cart-mobile">
                        <div class="empty-cart-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3 class="mb-3">Your cart is empty</h3>
                        <p class="mb-4">Add some amazing products to get started!</p>
                        <a href="shop.php" class="btn btn-success btn-lg rounded-pill px-4">
                            <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                        </a>
                    </div>
                <?php else: ?>
                    <div id="cartItemsMobile">
                        <?php foreach ($cart_items as $item): ?>
                            <?php if ($item['product_id'] < 0): // Service Request ?>
                                <?php $service = getServiceRequestByCartProductId($conn, $item['product_id']); ?>
                                <div class="cart-item-mobile" data-cart-id="<?php echo $item['id']; ?>">
                                    <div class="p-3">
                                        <div class="d-flex align-items-start">
                                            <div class="service-badge me-3">
                                                <i class="fas fa-tools me-1"></i>Service
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($service['title']); ?></h6>
                                                <p class="text-muted small mb-1">From: <?php echo htmlspecialchars($service['full_name']); ?></p>
                                                <p class="text-muted small mb-2">Phone: <?php echo htmlspecialchars($service['phone']); ?></p>
                                                <?php if ($service['message']): ?>
                                                    <p class="small text-secondary mb-2">
                                                        <i class="fas fa-comment me-1"></i>
                                                        <?php echo htmlspecialchars(substr($service['message'], 0, 50)) . (strlen($service['message']) > 50 ? '...' : ''); ?>
                                                    </p>
                                                <?php endif; ?>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="price-mobile"><?php echo htmlspecialchars($service['price']); ?></span>
                                                    <button class="remove-btn-mobile remove-from-cart" data-cart-id="<?php echo $item['id']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="cart-item-mobile" data-cart-id="<?php echo $item['id']; ?>">
                                    <div class="p-3">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $item['image']; ?>" class="cart-item-image me-3" alt="<?php echo $item['title']; ?>">
                                            <div class="flex-grow-1">
                                                <h6 class="fw-bold mb-1"><?php echo $item['title']; ?></h6>
                                                <p class="text-muted small mb-2"><?php echo formatPrice($item['price']); ?> each</p>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="quantity-controls">
                                                        <button type="button" class="quantity-btn quantity-btn-decrease" data-action="decrease">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number" class="quantity-input-mobile quantity-input" 
                                                               value="<?php echo $item['quantity']; ?>" 
                                                               min="1" 
                                                               data-cart-id="<?php echo $item['id']; ?>"
                                                               readonly>
                                                        <button type="button" class="quantity-btn quantity-btn-increase" data-action="increase">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <span class="price-mobile me-3"><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                                                        <button class="remove-btn-mobile remove-from-cart" data-cart-id="<?php echo $item['id']; ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- Payment Methods -->
                    <div class="payment-methods-mobile">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-credit-card me-2 text-success"></i>Payment Methods
                        </h6>
                        <div class="payment-method-item">
                            <div class="payment-method-icon">
                                <i class="fas fa-mobile-alt text-success"></i>
                            </div>
                            <span>MTN Mobile Money</span>
                        </div>
                        
                        <div class="payment-method-item">
                            <div class="payment-method-icon">
                                <i class="fas fa-money-bill-wave text-success"></i>
                            </div>
                            <span>Cash on Delivery</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cart Summary (Fixed Bottom) -->
            <?php if (!empty($cart_items)): ?>
                <div class="cart-summary-mobile">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <p class="mb-1 text-muted">Subtotal</p>
                            <h5 class="mb-0 fw-bold text-success"><?php echo formatPrice($cart_total); ?></h5>
                        </div>
                        <div class="text-end">
                            <p class="mb-1 text-muted small">Shipping</p>
                            <p class="mb-0 fw-bold text-success">Free</p>
                        </div>
                    </div>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="d-grid">
                            <a href="checkout.php" class="btn checkout-btn-mobile text-white">
                                <i class="fas fa-lock me-2"></i>Secure Checkout
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning rounded-pill text-center mb-3" role="alert">
                            <i class="fas fa-user me-2"></i>Please login to checkout
                        </div>
                        <div class="d-grid">
                            <a href="login.php?redirect=cart.php" class="btn checkout-btn-mobile text-white">
                                <i class="fas fa-sign-in-alt me-2"></i>Login to Checkout
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-danger btn-sm rounded-pill px-4" onclick="clearCart()">
                            <i class="fas fa-trash me-2"></i>Clear Cart
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Desktop Cart Layout (Original) -->
    <div class="desktop-cart">
        <!-- Cart Section -->
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php if (empty($cart_items)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                    <h3>Your cart is empty</h3>
                                    <p class="text-muted">Add some products to your cart to get started.</p>
                                    <a href="shop.php" class="btn btn-success">
                                        <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                                    </a>
                                </div>
                            <?php else: ?>
                                <div id="cartItems">
                                    <?php foreach ($cart_items as $item): ?>
                                        <?php if ($item['product_id'] < 0): // Service Request ?>
                                            <?php $service = getServiceRequestByCartProductId($conn, $item['product_id']); ?>
                                            <div class="row mb-3 align-items-center cart-item" data-cart-id="<?php echo $item['id']; ?>">
                                                <div class="col-md-2">
                                                    <span class="badge bg-success">Service Request</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($service['title']); ?></h6>
                                                    <p class="text-muted mb-0">From: <?php echo htmlspecialchars($service['full_name']); ?></p>
                                                    <p class="mb-0"><small>Phone: <?php echo htmlspecialchars($service['phone']); ?></small></p>
                                                    <?php if ($service['file']): ?>
                                                        <a href="<?php echo $service['file']; ?>" target="_blank" class="btn btn-sm btn-outline-dark mt-1">View File</a>
                                                    <?php endif; ?>
                                                    <?php if ($service['message']): ?>
                                                        <div class="mt-1"><small><b>Instructions:</b> <?php echo htmlspecialchars($service['message']); ?></small></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-3">
                                                    <span class="text-success fw-bold">1</span>
                                                </div>
                                                <div class="col-md-2">
                                                    <p class="mb-0 fw-bold"><?php echo htmlspecialchars($service['price']); ?></p>
                                                </div>
                                                <div class="col-md-1">
                                                    <button class="btn btn-outline-danger btn-sm remove-from-cart" data-cart-id="<?php echo $item['id']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="row mb-3 align-items-center cart-item" data-cart-id="<?php echo $item['id']; ?>">
                                                <div class="col-md-2">
                                                    <img src="<?php echo $item['image']; ?>" class="img-fluid rounded" alt="<?php echo $item['title']; ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <h6 class="mb-1"><?php echo $item['title']; ?></h6>
                                                    <p class="text-muted mb-0"><?php echo formatPrice($item['price']); ?> each</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="input-group">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm quantity-btn" data-action="decrease">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number" class="form-control text-center quantity-input" 
                                                               value="<?php echo $item['quantity']; ?>" 
                                                               min="1" 
                                                               data-cart-id="<?php echo $item['id']; ?>">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm quantity-btn" data-action="increase">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <p class="mb-0 fw-bold"><?php echo formatPrice($item['price'] * $item['quantity']); ?></p>
                                                </div>
                                                <div class="col-md-1">
                                                <button class="btn btn-outline-danger btn-sm remove-from-cart" 
                                                        data-cart-id="<?php echo $item['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="shop.php" class="btn btn-outline-dark">
                                        <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                                    </a>
                                    <button class="btn btn-outline-danger" onclick="clearCart()">
                                        <i class="fas fa-trash me-2"></i>Clear Cart
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="col-lg-4">
                    <div class="card checkout-summary">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-calculator me-2"></i>Order Summary
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($cart_items)): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span><?php echo formatPrice($cart_total); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span>Free</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total:</strong>
                                    <strong class="text-success"><?php echo formatPrice($cart_total); ?></strong>
                                </div>

                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <div class="d-grid">
                                        <a href="checkout.php" class="btn btn-success btn-lg">
                                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning" role="alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Please <a href="login.php?redirect=cart.php" class="alert-link">login</a> to checkout.
                                    </div>
                                    <div class="d-grid">
                                        <a href="login.php?redirect=cart.php" class="btn btn-success">
                                            <i class="fas fa-sign-in-alt me-2"></i>Login to Checkout
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="text-muted text-center">No items in cart</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <?php if (!empty($cart_items)): ?>
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>Payment Methods
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-mobile-alt text-success me-2"></i>
                                    <span>MTN Mobile Money</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-university text-primary me-2"></i>
                                    <span>Bank Transfer</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-money-bill-wave text-success me-2"></i>
                                    <span>Cash on Delivery</span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
    <script>
        // Quantity buttons
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentNode.querySelector('.quantity-input');
                const action = this.dataset.action;
                let value = parseInt(input.value);
                
                if (action === 'increase') {
                    value++;
                } else if (action === 'decrease' && value > 1) {
                    value--;
                }
                
                input.value = value;
                input.dispatchEvent(new Event('change'));
            });
        });

        // Clear cart function
        function clearCart() {
            if (confirm('Are you sure you want to clear your cart?')) {
                fetch('ajax/cart_actions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=clear_cart'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        showAlert('Error clearing cart', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error clearing cart', 'danger');
                });
            }
        }

        // Update cart display after changes
        function updateCartDisplay() {
            fetch('ajax/get_cart.php')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('cartItems').innerHTML = html;
                    // Reattach event listeners and update summary
                    if (typeof reattachCartEventListeners === 'function') {
                        reattachCartEventListeners();
                    }
                    updateCartSummary();
                })
                .catch(error => console.error('Error:', error));
        }

        // Update cart summary
        function updateCartSummary() {
            // Add cache-busting parameter to ensure fresh data
            const timestamp = new Date().getTime();
            fetch(`ajax/get_cart_summary.php?t=${timestamp}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Cart summary data:', data); // Debug log
                        
                        // Update all price elements in the checkout summary
                        const checkoutSummary = document.querySelector('.checkout-summary');
                        if (checkoutSummary) {
                            // Update all spans that contain price information
                            const priceSpans = checkoutSummary.querySelectorAll('span');
                            priceSpans.forEach(span => {
                                if (span.textContent.includes('Rwf') || span.textContent.includes('$')) {
                                    span.textContent = data.formatted_total;
                                }
                            });
                            
                            // Update all strong elements that contain price information
                            const priceStrongs = checkoutSummary.querySelectorAll('strong');
                            priceStrongs.forEach(strong => {
                                if (strong.textContent.includes('Rwf') || strong.textContent.includes('$')) {
                                    strong.textContent = data.formatted_total;
                                }
                            });
                        }
                        
                        // Also update cart count in header
                        updateCartCount();
                        
                        // Update individual item totals
                        updateItemTotals();
                        
                        // Debug: Log what we're updating
                        console.log('Updated subtotal and total to:', data.formatted_total);
                        console.log('Debug info:', data.debug);
                        
                        // Additional debug: Log the elements we're updating
                        console.log('Checkout summary found:', !!checkoutSummary);
                        console.log('Price spans updated:', checkoutSummary?.querySelectorAll('span').length || 0);
                        console.log('Price strongs updated:', checkoutSummary?.querySelectorAll('strong').length || 0);
                    }
                })
                .catch(error => console.error('Error updating cart summary:', error));
        }

        // Update individual item totals
        function updateItemTotals() {
            const cartItems = document.querySelectorAll('.cart-item');
            cartItems.forEach(item => {
                const quantityInput = item.querySelector('.quantity-input');
                const priceText = item.querySelector('.text-muted').textContent;
                const totalElement = item.querySelector('.fw-bold');
                
                if (quantityInput && priceText && totalElement) {
                    const quantity = parseInt(quantityInput.value);
                    const price = parseFloat(priceText.replace(/[^\d.]/g, ''));
                    const total = quantity * price;
                    
                    // Format the total with Rwf currency
                    const formattedTotal = new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(total) + ' Rwf';
                    
                    totalElement.textContent = formattedTotal;
                }
            });
        }

        // Initialize cart event listeners when page loads
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof reattachCartEventListeners === 'function') {
                reattachCartEventListeners();
            }
        });

        // Use event delegation for cart interactions
        document.addEventListener('click', function(e) {
            // Handle quantity buttons
            if (e.target.closest('.quantity-btn')) {
                const button = e.target.closest('.quantity-btn');
                const input = button.parentNode.querySelector('.quantity-input');
                const action = button.dataset.action;
                let value = parseInt(input.value);
                
                if (action === 'increase') {
                    value++;
                } else if (action === 'decrease' && value > 1) {
                    value--;
                }
                
                input.value = value;
                
                // Immediately update the item total
                updateItemTotal(input);
                
                // Trigger both change event and direct update
                input.dispatchEvent(new Event('change'));
                
                // Also directly update cart quantity
                const cartId = input.dataset.cartId;
                const quantity = input.value;
                updateCartQuantity(cartId, quantity);
            }
            
            // Handle remove from cart
            if (e.target.closest('.remove-from-cart')) {
                e.preventDefault();
                const button = e.target.closest('.remove-from-cart');
                const cartId = button.dataset.cartId;
                removeFromCart(cartId);
            }
        });

        // Handle quantity input changes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('quantity-input')) {
                // Immediately update the item total
                updateItemTotal(e.target);
                
                const cartId = e.target.dataset.cartId;
                const quantity = e.target.value;
                updateCartQuantity(cartId, quantity);
            }
        });

        // Update individual item total
        function updateItemTotal(quantityInput) {
            const cartItem = quantityInput.closest('.cart-item');
            const priceText = cartItem.querySelector('.text-muted').textContent;
            const totalElement = cartItem.querySelector('.fw-bold');
            
            if (priceText && totalElement) {
                const quantity = parseInt(quantityInput.value);
                const price = parseFloat(priceText.replace(/[^\d.]/g, ''));
                const total = quantity * price;
                
                // Format the total with Rwf currency
                const formattedTotal = new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(total) + ' Rwf';
                
                totalElement.textContent = formattedTotal;
            }
        }
    </script>
</body>
</html>