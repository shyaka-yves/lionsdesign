<?php
// Helper functions for Lions Design E-commerce

// Include PHPMailer at the top of the file
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Get featured products
function getFeaturedProducts($conn, $limit = 6) {
    $limit = intval($limit); // Ensure it's an integer
    $stmt = $conn->prepare("SELECT * FROM products WHERE is_featured = 1 AND is_active = 1 ORDER BY created_at DESC LIMIT " . $limit);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all products with optional category filter
function getProducts($conn, $category_id = null, $limit = null) {
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1";
    $params = [];
    
    if ($category_id) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category_id;
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    if ($limit) {
        $limit = intval($limit); // Ensure it's an integer
        $sql .= " LIMIT " . $limit;
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get single product by ID
function getProduct($conn, $id) {
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.id = ? AND p.is_active = 1");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all categories
function getCategories($conn) {
    $stmt = $conn->prepare("SELECT * FROM categories ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get category by slug
function getCategoryBySlug($conn, $slug) {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// User authentication functions
function registerUser($conn, $email, $password, $first_name, $last_name, $phone = null, $address = null) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (email, password, first_name, last_name, phone, address) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$email, $hashed_password, $first_name, $last_name, $phone, $address]);
}

function loginUser($conn, $email, $password) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

function getUserById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Cart functions
function addToCart($conn, $session_id, $product_id, $quantity = 1) {
    // Check if product already in cart
    $stmt = $conn->prepare("SELECT * FROM cart WHERE session_id = ? AND product_id = ?");
    $stmt->execute([$session_id, $product_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        // Update quantity
        $new_quantity = $existing['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        return $stmt->execute([$new_quantity, $existing['id']]);
    } else {
        // Add new item
        $stmt = $conn->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)");
        return $stmt->execute([$session_id, $product_id, $quantity]);
    }
}

function getCart($conn, $session_id) {
    // Get regular products
    $stmt = $conn->prepare("SELECT c.*, p.title, p.price, p.image, 'product' as type FROM cart c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.session_id = ? AND c.product_id > 0");
    $stmt->execute([$session_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get service requests
    $stmt = $conn->prepare("SELECT c.*, sr.full_name as title, s.price, s.image, 'service' as type, sr.id as service_request_id 
                           FROM cart c 
                           JOIN service_requests sr ON c.product_id = -sr.id 
                           JOIN services s ON sr.service_id = s.id 
                           WHERE c.session_id = ? AND c.product_id < 0");
    $stmt->execute([$session_id]);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Combine both arrays
    return array_merge($products, $services);
}

function updateCartQuantity($conn, $cart_id, $quantity) {
    if ($quantity <= 0) {
        return removeFromCart($conn, $cart_id);
    }
    
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    return $stmt->execute([$quantity, $cart_id]);
}

function removeFromCart($conn, $cart_id) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
    return $stmt->execute([$cart_id]);
}

function clearCart($conn, $session_id) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE session_id = ?");
    return $stmt->execute([$session_id]);
}

function getCartTotal($conn, $session_id) {
    $cart_items = getCart($conn, $session_id);
    $total = 0;
    foreach ($cart_items as $item) {
        if ($item['type'] === 'product') {
            $total += $item['price'] * $item['quantity'];
        } else if ($item['type'] === 'service') {
            // For services, we need to extract the numeric price from the string
            $price_str = $item['price'];
            if (preg_match('/From\s+([\d,]+)/', $price_str, $matches)) {
                $price = str_replace(',', '', $matches[1]);
                $total += floatval($price) * $item['quantity'];
            }
        }
    }
    return $total;
}

// Order functions
function createOrder($conn, $user_id, $total_amount, $shipping_address, $phone, $payment_method = 'mtn_mobile_money') {
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, phone, payment_method) 
                           VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $total_amount, $shipping_address, $phone, $payment_method]);
    return $conn->lastInsertId();
}

function addOrderItems($conn, $order_id, $cart_items) {
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    
    foreach ($cart_items as $item) {
        // Only process regular products (not service requests)
        if ($item['type'] === 'product' && $item['product_id'] > 0) {
            // Check if product exists and has sufficient stock
            $product = getProduct($conn, $item['product_id']);
            if ($product && $product['stock_quantity'] >= $item['quantity']) {
                // Reduce stock
                $new_stock = $product['stock_quantity'] - $item['quantity'];
                $update_stmt = $conn->prepare("UPDATE products SET stock_quantity = ? WHERE id = ?");
                $update_stmt->execute([$new_stock, $item['product_id']]);
                
                // Add order item
                $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
            } else {
                // Insufficient stock - throw exception to rollback transaction
                throw new Exception("Insufficient stock for product: " . ($product['title'] ?? 'Unknown'));
            }
        } else {
            // For service requests or other items, just add to order items
            $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
        }
    }
}

// Check if cart items have sufficient stock
function checkCartStock($conn, $session_id) {
    $cart_items = getCart($conn, $session_id);
    $stock_errors = [];
    
    foreach ($cart_items as $item) {
        if ($item['type'] === 'product' && $item['product_id'] > 0) {
            $product = getProduct($conn, $item['product_id']);
            if ($product && $product['stock_quantity'] < $item['quantity']) {
                $stock_errors[] = "Insufficient stock for {$product['title']}. Available: {$product['stock_quantity']}, Requested: {$item['quantity']}";
            }
        }
    }
    
    return $stock_errors;
}

// Update product stock (for admin use)
function updateProductStock($conn, $product_id, $new_quantity) {
    $stmt = $conn->prepare("UPDATE products SET stock_quantity = ? WHERE id = ?");
    return $stmt->execute([$new_quantity, $product_id]);
}

function getUserOrders($conn, $user_id) {
    $stmt = $conn->prepare("SELECT o.*, COUNT(oi.id) as item_count FROM orders o 
                           LEFT JOIN order_items oi ON o.id = oi.order_id 
                           WHERE o.user_id = ? 
                           GROUP BY o.id 
                           ORDER BY o.created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOrderDetails($conn, $order_id) {
    $stmt = $conn->prepare("SELECT o.*, u.first_name, u.last_name, u.email FROM orders o 
                           JOIN users u ON o.user_id = u.id 
                           WHERE o.id = ?");
    $stmt->execute([$order_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getOrderItems($conn, $order_id) {
    $stmt = $conn->prepare("SELECT oi.*, p.title, p.image FROM order_items oi 
                           JOIN products p ON oi.product_id = p.id 
                           WHERE oi.order_id = ?");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Admin functions
function getAllOrders($conn) {
    $stmt = $conn->prepare("SELECT o.*, u.first_name, u.last_name, u.email, COUNT(oi.id) as item_count 
                           FROM orders o 
                           JOIN users u ON o.user_id = u.id 
                           LEFT JOIN order_items oi ON o.id = oi.order_id 
                           GROUP BY o.id 
                           ORDER BY o.created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllUsers($conn) {
    $stmt = $conn->prepare("SELECT id, email, first_name, last_name, phone, created_at, role FROM users");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// --- Admin activity logging ---
function ensureActivityLogTable(PDO $conn): void {
    $conn->exec(
        "CREATE TABLE IF NOT EXISTS admin_activity_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NOT NULL,
            action VARCHAR(100) NOT NULL,
            entity_type VARCHAR(50) NOT NULL,
            entity_id INT NULL,
            details TEXT NULL,
            ip_address VARCHAR(100) NULL,
            user_agent VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_admin_id (admin_id),
            INDEX idx_entity (entity_type, entity_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );
}

function logAdminActivity(PDO $conn, int $adminId, string $action, string $entityType, ?int $entityId, string $details = ''): void {
    ensureActivityLogTable($conn);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $stmt = $conn->prepare(
        "INSERT INTO admin_activity_log (admin_id, action, entity_type, entity_id, details, ip_address, user_agent)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$adminId, $action, $entityType, $entityId, $details, $ip, $ua]);
}

function getAdminActivities(PDO $conn, ?int $adminId = null, int $limit = 100): array {
    ensureActivityLogTable($conn);
    if ($adminId) {
        $stmt = $conn->prepare(
            "SELECT l.*, u.first_name, u.last_name, u.email
             FROM admin_activity_log l
             JOIN users u ON u.id = l.admin_id
             WHERE l.admin_id = ?
             ORDER BY l.id DESC
             LIMIT ?"
        );
        $stmt->bindValue(1, $adminId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare(
            "SELECT l.*, u.first_name, u.last_name, u.email
             FROM admin_activity_log l
             JOIN users u ON u.id = l.admin_id
             ORDER BY l.id DESC
             LIMIT ?"
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAdminActivitiesFiltered(
    PDO $conn,
    array $filters,
    int $limit = 200,
    int $offset = 0
): array {
    ensureActivityLogTable($conn);
    $where = [];
    $params = [];

    if (!empty($filters['admin_id'])) {
        $where[] = 'l.admin_id = ?';
        $params[] = (int)$filters['admin_id'];
    }
    if (!empty($filters['action'])) {
        $where[] = 'l.action = ?';
        $params[] = $filters['action'];
    }
    if (!empty($filters['entity_type'])) {
        $where[] = 'l.entity_type = ?';
        $params[] = $filters['entity_type'];
    }
    if (!empty($filters['date_from'])) {
        $where[] = 'l.created_at >= ?';
        $params[] = $filters['date_from'] . ' 00:00:00';
    }
    if (!empty($filters['date_to'])) {
        $where[] = 'l.created_at <= ?';
        $params[] = $filters['date_to'] . ' 23:59:59';
    }
    if (!empty($filters['q'])) {
        $where[] = '(l.details LIKE ? OR u.email LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)';
        $q = '%' . $filters['q'] . '%';
        array_push($params, $q, $q, $q, $q);
    }

    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
    $sql = "SELECT l.*, u.first_name, u.last_name, u.email
            FROM admin_activity_log l
            JOIN users u ON u.id = l.admin_id
            $whereSql
            ORDER BY l.id DESC
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
    $i = 1;
    foreach ($params as $p) {
        $stmt->bindValue($i++, $p);
    }
    $stmt->bindValue($i++, $limit, PDO::PARAM_INT);
    $stmt->bindValue($i, $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateOrderStatus($conn, $order_id, $status) {
    // Validate status before updating
    if (!isValidOrderStatus($status)) {
        return false;
    }
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $order_id]);
}

// Validate order status against allowed values
function isValidOrderStatus($status) {
    $allowed = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
    return in_array($status, $allowed, true);
}

// Determine if an order can be cancelled by the customer (within 15 minutes and still pending)
function canCancelOrder(array $order) {
    if (!$order) return false;
    if ($order['status'] !== 'pending') return false;
    $createdAt = strtotime($order['created_at']);
    return ($createdAt !== false) && ($createdAt >= (time() - 15 * 60));
}

// Cancel an order for a specific user, restoring product stock when applicable
function cancelOrder(PDO $conn, int $orderId, int $userId) {
    $order = getOrderDetails($conn, $orderId);
    if (!$order || (int)$order['user_id'] !== (int)$userId) {
        return false;
    }
    if (!canCancelOrder($order)) {
        return false;
    }

    try {
        $conn->beginTransaction();

        // Update status
        $stmt = $conn->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute(['cancelled', $orderId]);

        // Restore stock for product items
        $items = getOrderItems($conn, $orderId);
        foreach ($items as $item) {
            if ((int)$item['product_id'] > 0) {
                // Increase stock back
                $update = $conn->prepare('UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?');
                $update->execute([(int)$item['quantity'], (int)$item['product_id']]);
            }
        }

        $conn->commit();
        return true;
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        return false;
    }
}

// Product management functions
function addProduct($conn, $title, $description, $price, $image, $category_id, $stock_quantity, $is_featured = false) {
    $stmt = $conn->prepare("INSERT INTO products (title, description, price, image, category_id, stock_quantity, is_featured) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$title, $description, $price, $image, $category_id, $stock_quantity, $is_featured]);
}

function updateProduct($conn, $id, $title, $description, $price, $image, $category_id, $stock_quantity, $is_featured = false, $is_active = true) {
    $stmt = $conn->prepare("UPDATE products SET title = ?, description = ?, price = ?, image = ?, 
                           category_id = ?, stock_quantity = ?, is_featured = ?, is_active = ? WHERE id = ?");
    return $stmt->execute([$title, $description, $price, $image, $category_id, $stock_quantity, $is_featured, $is_active, $id]);
}

function deleteProduct($conn, $id) {
    $stmt = $conn->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
    return $stmt->execute([$id]);
}

// Utility functions
function formatPrice($price) {
    return number_format($price, 2) . ' Rwf';
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generateSessionId() {
    if (!session_id()) {
        session_start();
    }
    return session_id();
}

// MTN Mobile Money API placeholder
function processMTNPayment($amount, $phone_number) {
    // This is a placeholder function for MTN Mobile Money integration
    // In a real implementation, you would integrate with MTN's API
    return [
        'success' => true,
        'transaction_id' => 'MTN_' . uniqid(),
        'message' => 'Payment processed successfully'
    ];
}

// Feedback functions
function saveFeedback($conn, $name, $email, $subject, $message) {
    $stmt = $conn->prepare("INSERT INTO feedback (name, email, subject, message) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$name, $email, $subject, $message]);
}

function getAllFeedback($conn) {
    $stmt = $conn->prepare("SELECT * FROM feedback ORDER BY created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFeedbackById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM feedback WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateFeedbackStatus($conn, $id, $status) {
    $stmt = $conn->prepare("UPDATE feedback SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $id]);
}

function getUnreadFeedbackCount($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM feedback WHERE status = 'unread'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

// Service request helper for cart integration
function getServiceRequestByCartProductId($conn, $product_id) {
    // product_id is negative request_id
    $request_id = abs($product_id);
    $stmt = $conn->prepare('SELECT sr.*, s.title, s.price FROM service_requests sr JOIN services s ON sr.service_id = s.id WHERE sr.id = ?');
    $stmt->execute([$request_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// OTP Functions
function generateOTP() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function storeOTP($conn, $email, $otp, $type) {
    // Delete any existing unused OTPs for this email and type
    $stmt = $conn->prepare("DELETE FROM otp_codes WHERE email = ? AND type = ? AND is_used = 0");
    $stmt->execute([$email, $type]);
    
    // Set expiration time (10 minutes from now)
    $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    
    // Store new OTP
    $stmt = $conn->prepare("INSERT INTO otp_codes (email, otp_code, type, expires_at) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$email, $otp, $type, $expires_at]);
}

function validateOTP($conn, $email, $otp, $type) {
    $stmt = $conn->prepare("SELECT * FROM otp_codes WHERE email = ? AND otp_code = ? AND type = ? AND is_used = 0 AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$email, $otp, $type]);
    $otp_record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($otp_record) {
        // Mark OTP as used
        $stmt = $conn->prepare("UPDATE otp_codes SET is_used = 1 WHERE id = ?");
        $stmt->execute([$otp_record['id']]);
        return true;
    }
    return false;
}

function sendOTPEmail($email, $otp, $type) {
    try {
        // Load email configuration with proper path
        $config_file = __DIR__ . '/../config/email.php';
        if (!file_exists($config_file)) {
            error_log("Email config file not found: $config_file");
            return false;
        }
        
        $email_config = require $config_file;
        if (!is_array($email_config)) {
            error_log("Email config file did not return an array");
            return false;
        }
        
        // Include the full PHPMailer library explicitly
        require_once __DIR__ . '/../src/PHPMailer.php';
        require_once __DIR__ . '/../src/SMTP.php';
        require_once __DIR__ . '/../src/Exception.php';
        
        // Create PHPMailer instance using the full library
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = $email_config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $email_config['smtp_username'];
        $mail->Password = $email_config['smtp_password'];
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $email_config['smtp_port'];
        
        // Enable debug output (set to 2 for troubleshooting)
        $mail->SMTPDebug = 2;
        $smtp_debug_buffer = '';
        $mail->Debugoutput = function ($str, $level) use (&$smtp_debug_buffer) {
            $smtp_debug_buffer .= '[' . $level . '] ' . $str . "\n";
        };

        // Local dev: relax SSL verification to avoid local CA issues
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
        
        // Recipients
        $mail->setFrom($email_config['smtp_username'], 'Lions Design');
        $mail->addAddress($email);
        
        // Content
        $subject = $type === 'signup' ? 'Email Verification - Lions Design' : 'Password Reset - Lions Design';
        $message = $type === 'signup' 
            ? "Your verification code is: <strong>$otp</strong><br><br>This code will expire in 10 minutes.<br><br>If you didn't request this, please ignore this email."
            : "Your password reset code is: <strong>$otp</strong><br><br>This code will expire in 10 minutes.<br><br>If you didn't request this, please ignore this email.";
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
        
        // Send email
        if ($mail->send()) {
            error_log("Email sent successfully to: $email with OTP: $otp");
            $GLOBALS['LAST_EMAIL_ERROR'] = '';
            $GLOBALS['LAST_EMAIL_DEBUG'] = $smtp_debug_buffer;
            return true;
        } else {
            $GLOBALS['LAST_EMAIL_ERROR'] = $mail->ErrorInfo;
            $GLOBALS['LAST_EMAIL_DEBUG'] = $smtp_debug_buffer;
            error_log("Email sending failed: " . $mail->ErrorInfo);
            return false;
        }
        
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        $GLOBALS['LAST_EMAIL_ERROR'] = $e->getMessage();
        if (!isset($GLOBALS['LAST_EMAIL_DEBUG'])) {
            $GLOBALS['LAST_EMAIL_DEBUG'] = '';
        }
        error_log("Email sending exception: " . $e->getMessage());
        return false;
    }
}

function cleanupExpiredOTPs($conn) {
    $stmt = $conn->prepare("DELETE FROM otp_codes WHERE expires_at < NOW()");
    return $stmt->execute();
}
?> 