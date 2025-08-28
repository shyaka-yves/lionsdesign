<?php require_once '../includes/functions.php'; ?>
<nav class="col-md-3 col-lg-2 d-md-block admin-sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4 admin-brand">
            <h5 class="text-white m-0">
                <i class="fas fa-user-shield me-2"></i>Admin Panel
            </h5>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="products.php">
                    <i class="fas fa-box"></i><span>Products</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="services.php">
                    <i class="fas fa-cogs" style="color:#009e3c;"></i><span>Services</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="orders.php">
                    <i class="fas fa-shopping-cart"></i><span>Orders</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="users.php">
                    <i class="fas fa-users"></i><span>Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="categories.php">
                    <i class="fas fa-tags"></i><span>Categories</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="feedback.php">
                    <i class="fas fa-comments"></i><span>Customer Feedback</span>
                    <?php 
                    $unread_count = getUnreadFeedbackCount($conn);
                    if ($unread_count > 0): 
                    ?>
                        <span class="badge bg-danger ms-2"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../index.php">
                    <i class="fas fa-home"></i><span>View Site</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i><span>Logout</span>
                </a>
            </li>
            <li class="nav-item">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'super'): ?>
                <a class="nav-link" href="super_dashboard.php">
                    <i class="fas fa-user-shield"></i><span>Super User Dashboard</span>
                </a>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</nav> 