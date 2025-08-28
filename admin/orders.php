<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super')) {
    header("Location: ../login.php");
    exit();
}

// Get search and filter parameters
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status_filter'] ?? '';
$date_filter = $_GET['date_filter'] ?? '';

// Get all orders first
$all_orders = getAllOrders($conn);

// Filter orders based on parameters
$orders = [];
$search_lower = strtolower(trim($search));

foreach ($all_orders as $order) {
    $matches_search = empty($search_lower);
    $matches_status = empty($status_filter) || $order['status'] === $status_filter;
    $matches_date = empty($date_filter);
    
    // Search filter
    if (!$matches_search) {
        $customer_name = strtolower($order['first_name'] . ' ' . $order['last_name']);
        $customer_email = strtolower($order['email']);
        $order_id = strval($order['id']);
        
        $matches_search = strpos($customer_name, $search_lower) !== false ||
                         strpos($customer_email, $search_lower) !== false ||
                         strpos($order_id, $search_lower) !== false;
    }
    
    // Date filter
    if (!$matches_date) {
        $order_date = strtotime($order['created_at']);
        $today = strtotime('today');
        $week_start = strtotime('monday this week');
        $month_start = strtotime('first day of this month');
        
        switch ($date_filter) {
            case 'today':
                $matches_date = date('Y-m-d', $order_date) === date('Y-m-d', $today);
                break;
            case 'week':
                $matches_date = $order_date >= $week_start;
                break;
            case 'month':
                $matches_date = $order_date >= $month_start;
                break;
        }
    }
    
    if ($matches_search && $matches_status && $matches_date) {
        $orders[] = $order;
    }
}

function getStatusColor($status) {
    switch ($status) {
        case 'pending': return 'warning';
        case 'paid': return 'success';
        case 'shipped': return 'info';
        case 'delivered': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

// Handle status update (admin/super only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['status'];
    if (isValidOrderStatus($newStatus)) {
        updateOrderStatus($conn, $orderId, $newStatus);
        if (isset($_SESSION['user_id'])) {
            logAdminActivity($conn, (int)$_SESSION['user_id'], 'update_order_status', 'order', $orderId, 'Status set to ' . $newStatus);
        }
    }
    header('Location: orders.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
    <link rel="icon" type="image/png" href="../image/logo.png">
    <link rel="shortcut icon" type="image/png" href="../image/logo.png">
    <link rel="apple-touch-icon" href="../image/logo.png">
    <meta name="msapplication-TileImage" content="../image/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="admin.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="admin-mobile-header d-md-none mb-2">
                <button class="btn btn-dark" type="button" data-bs-toggle="collapse" data-bs-target=".admin-sidebar">
                    <i class="fas fa-bars me-2"></i>Menu
                </button>
            </div>
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Orders</h1>
            </div>
            
            <!-- Search and Filter -->
            <div class="admin-search-container">
                <div class="admin-search-form">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search Orders</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" 
                                   placeholder="Search by customer name, email, order ID...">
                        </div>
                        <div class="col-md-3">
                            <label for="status_filter" class="form-label">Status</label>
                            <select class="form-select" id="status_filter" name="status_filter">
                                <option value="">All Status</option>
                                <option value="pending" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="paid" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
                                <option value="shipped" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_filter" class="form-label">Date Range</label>
                            <select class="form-select" id="date_filter" name="date_filter">
                                <option value="">All Time</option>
                                <option value="today" <?php echo (isset($_GET['date_filter']) && $_GET['date_filter'] == 'today') ? 'selected' : ''; ?>>Today</option>
                                <option value="week" <?php echo (isset($_GET['date_filter']) && $_GET['date_filter'] == 'week') ? 'selected' : ''; ?>>This Week</option>
                                <option value="month" <?php echo (isset($_GET['date_filter']) && $_GET['date_filter'] == 'month') ? 'selected' : ''; ?>>This Month</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                                <a href="orders.php" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-refresh me-2"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?><br><small><?php echo htmlspecialchars($order['email']); ?></small></td>
                                    <td><?php echo formatPrice($order['total_amount']); ?></td>
                                    <td>
                                        <form method="POST" class="d-flex align-items-center gap-2">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status" class="form-select form-select-sm bg-<?php echo getStatusColor($order['status']); ?> text-white" onchange="this.form.submit()">
                                                <option value="pending" <?php if ($order['status']==='pending') echo 'selected'; ?>>Pending</option>
                                                <option value="paid" <?php if ($order['status']==='paid') echo 'selected'; ?>>Paid</option>
                                                <option value="shipped" <?php if ($order['status']==='shipped') echo 'selected'; ?>>Shipped</option>
                                                <option value="delivered" <?php if ($order['status']==='delivered') echo 'selected'; ?>>Delivered</option>
                                                <option value="cancelled" <?php if ($order['status']==='cancelled') echo 'selected'; ?>>Cancelled</option>
                                            </select>
                                            <span class="badge bg-<?php echo getStatusColor($order['status']); ?> ms-2"><?php echo ucfirst($order['status']); ?></span>
                                        </form>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i> View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 