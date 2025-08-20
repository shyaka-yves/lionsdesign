<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super')) {
    header("Location: ../login.php");
    exit();
}

// Handle delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    // Only super user can delete admins, admins can only delete customers
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$delete_id]);
    $target = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($target) {
        if ($_SESSION['role'] === 'super' || ($target['role'] === 'customer' && $_SESSION['role'] === 'admin')) {
            $del = $conn->prepare("DELETE FROM users WHERE id = ?");
            $del->execute([$delete_id]);
        }
    }
    header('Location: users.php');
    exit();
}

// Get search and filter parameters
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role_filter'] ?? '';

// Get all users first
$all_users = getAllUsers($conn);

// Filter users based on parameters
$users = [];
$search_lower = strtolower(trim($search));

foreach ($all_users as $user) {
    $matches_search = empty($search_lower);
    $matches_role = empty($role_filter) || $user['role'] === $role_filter;
    
    // Search filter
    if (!$matches_search) {
        $user_name = strtolower($user['first_name'] . ' ' . $user['last_name']);
        $user_email = strtolower($user['email']);
        $user_phone = strtolower($user['phone']);
        
        $matches_search = strpos($user_name, $search_lower) !== false ||
                         strpos($user_email, $search_lower) !== false ||
                         strpos($user_phone, $search_lower) !== false;
    }
    
    if ($matches_search && $matches_role) {
        $users[] = $user;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
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
                <h1 class="h2">Manage Users</h1>
            </div>
            
            <!-- Search and Filter -->
            <div class="admin-search-container">
                <div class="admin-search-form">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search Users</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" 
                                   placeholder="Search by name, email, phone...">
                        </div>
                        <div class="col-md-3">
                            <label for="role_filter" class="form-label">Role</label>
                            <select class="form-select" id="role_filter" name="role_filter">
                                <option value="">All Roles</option>
                                <option value="super" <?php echo (isset($_GET['role_filter']) && $_GET['role_filter'] == 'super') ? 'selected' : ''; ?>>Super User</option>
                                <option value="admin" <?php echo (isset($_GET['role_filter']) && $_GET['role_filter'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                <option value="customer" <?php echo (isset($_GET['role_filter']) && $_GET['role_filter'] == 'customer') ? 'selected' : ''; ?>>Customer</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                                <a href="users.php" class="btn btn-outline-secondary w-100">
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
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Registered</th>
                                    <th>Role</th>

                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>#<?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <?php if ($user['role'] === 'super'): ?>
                                            <span class="badge bg-danger">Super User</span>
                                        <?php elseif ($user['role'] === 'admin'): ?>
                                            <span class="badge bg-success">Admin</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Customer</span>
                                        <?php endif; ?>
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