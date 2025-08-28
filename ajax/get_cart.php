<?php
// Ensure session is started
if (!session_id()) {
    session_start();
}

require_once '../config/database.php';
require_once '../includes/functions.php';

$session_id = generateSessionId();
$cart_items = getCart($conn, $session_id);

// Debug: Log cart items
error_log("Cart items for session $session_id: " . print_r($cart_items, true));

if (empty($cart_items)) {
    echo '<div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted">Add some products to your cart to get started.</p>
            <a href="shop.php" class="btn btn-success">
                <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
            </a>
          </div>';
    exit();
}

foreach ($cart_items as $item): 
    if ($item['type'] === 'service'): ?>
        <!-- Service Request Item -->
        <div class="row mb-3 align-items-center cart-item" data-cart-id="<?php echo $item['id']; ?>">
            <div class="col-md-2">
                <span class="badge bg-success">Service</span>
            </div>
            <div class="col-md-4">
                <h6 class="mb-1"><?php echo htmlspecialchars($item['title']); ?></h6>
                <p class="text-muted mb-0"><?php echo htmlspecialchars($item['price']); ?></p>
            </div>
            <div class="col-md-3">
                <span class="text-success fw-bold">1</span>
            </div>
            <div class="col-md-2">
                <p class="mb-0 fw-bold"><?php echo htmlspecialchars($item['price']); ?></p>
            </div>
            <div class="col-md-1">
                <button class="btn btn-outline-danger btn-sm remove-from-cart" 
                        data-cart-id="<?php echo $item['id']; ?>">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    <?php else: ?>
        <!-- Regular Product Item -->
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
                <p class="mb-0 fw-bold">
                    <?php 
                    $item_total = $item['price'] * $item['quantity'];
                    echo formatPrice($item_total);
                    ?>
                </p>
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
