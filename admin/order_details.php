<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super')) {
    header("Location: ../login.php");
    exit();
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$order = getOrderDetails($conn, $order_id);
$order_items = getOrderItems($conn, $order_id);

if (!$order) {
    echo '<div class="alert alert-danger m-4">Order not found.</div>';
    exit();
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Admin</title>
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
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Order #<?php echo $order['id']; ?> Details</h1>
                <div>
                    <a href="orders.php" class="btn btn-outline-dark me-2"><i class="fas fa-arrow-left me-2"></i>Back to Orders</a>
                    <button onclick="window.print();" class="btn btn-info text-white"><i class="fas fa-print me-2"></i>Print Invoice</button>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <strong>Order Information</strong>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="mb-3">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <div class="mb-2">
                                    <label for="status" class="form-label"><strong>Status:</strong></label>
                                    <div class="d-flex align-items-center gap-2">
                                        <select name="status" id="status" class="form-select form-select-sm w-auto bg-<?php echo getStatusColor($order['status']); ?> text-white" onchange="this.form.submit()">
                                            <option value="pending" <?php if ($order['status']==='pending') echo 'selected'; ?>>Pending</option>
                                            <option value="paid" <?php if ($order['status']==='paid') echo 'selected'; ?>>Paid</option>
                                            <option value="shipped" <?php if ($order['status']==='shipped') echo 'selected'; ?>>Shipped</option>
                                            <option value="delivered" <?php if ($order['status']==='delivered') echo 'selected'; ?>>Delivered</option>
                                            <option value="cancelled" <?php if ($order['status']==='cancelled') echo 'selected'; ?>>Cancelled</option>
                                        </select>
                                        <span class="badge bg-<?php echo getStatusColor($order['status']); ?> ms-2"><?php echo ucfirst($order['status']); ?></span>
                                    </div>
                                </div>
                            </form>
                            <p><strong>Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
                            <p><strong>Payment:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?></p>
                            <p><strong>Total:</strong> <?php echo formatPrice($order['total_amount']); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <strong>Customer & Shipping</strong>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                            <p><strong>Shipping Address:</strong><br><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <strong>Order Items</strong>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total = 0; foreach ($order_items as $item): $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; ?>
                                <tr>
                                    <td><img src="../<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width:60px; height:60px; object-fit:cover;" class="rounded"></td>
                                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo formatPrice($item['price']); ?></td>
                                    <td><?php echo formatPrice($subtotal); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total</th>
                                    <th><?php echo formatPrice($total); ?></th>
                                </tr>
                            </tfoot>
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