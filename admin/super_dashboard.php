<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'super') {
    header("Location: ../login.php");
    exit();
}

// Handle privilege changes and deletions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $delete_id = intval($_POST['delete_id']);
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$delete_id]);
        logAdminActivity($conn, (int)$_SESSION['user_id'], 'delete', 'user', $delete_id, '');
    } elseif (isset($_POST['make_admin_id'])) {
        $admin_id = intval($_POST['make_admin_id']);
        $stmt = $conn->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
        $stmt->execute([$admin_id]);
        logAdminActivity($conn, (int)$_SESSION['user_id'], 'grant_admin', 'user', $admin_id, '');
    } elseif (isset($_POST['make_customer_id'])) {
        $customer_id = intval($_POST['make_customer_id']);
        $stmt = $conn->prepare("UPDATE users SET role = 'customer' WHERE id = ?");
        $stmt->execute([$customer_id]);
        logAdminActivity($conn, (int)$_SESSION['user_id'], 'revoke_admin', 'user', $customer_id, '');
    }
    header('Location: super_dashboard.php');
    exit();
}

$users = getAllUsers($conn);

// Filters
$filters = [
    'admin_id' => isset($_GET['admin_id']) ? intval($_GET['admin_id']) : null,
    'action' => $_GET['action'] ?? '',
    'entity_type' => $_GET['entity_type'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? '',
    'q' => $_GET['q'] ?? '',
];
$activities = getAdminActivitiesFiltered($conn, $filters, 200, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super User Dashboard</title>
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
                <h1 class="h2">Super User Dashboard</h1>
            </div>
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-danger text-white">
                                <tr>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Registered</th>
                                    <th>Role</th>
                                    <th>Actions</th>
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
                                    <td>
                                        <?php if ($user['role'] !== 'super'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="delete_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash"></i> Delete</button>
                                            </form>
                                            <?php if ($user['role'] === 'admin'): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="make_customer_id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" class="btn btn-warning btn-sm"><i class="fas fa-user"></i> Revoke Admin</button>
                                                </form>
                                            <?php elseif ($user['role'] === 'customer'): ?>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="make_admin_id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-user-shield"></i> Make Admin</button>
                                                </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-5">
                <div class="card-header bg-dark text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Admin Activity Log</h5>
                        <small>Latest 200 entries</small>
                    </div>
                </div>
                <div class="card-body">
                    <form class="row g-3 mb-3" method="get">
                        <div class="col-md-2">
                            <label class="form-label">Admin</label>
                            <select name="admin_id" class="form-select">
                                <option value="">All</option>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= (int)$u['id'] ?>" <?= ($filters['admin_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(($u['first_name'] ?: '') . ' ' . ($u['last_name'] ?: '')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Action</label>
                            <select name="action" class="form-select">
                                <option value="">All</option>
                                <?php foreach (['create','update','delete','grant_admin','revoke_admin'] as $a): ?>
                                    <option value="<?= $a ?>" <?= ($filters['action'] ?? '') === $a ? 'selected' : '' ?>><?= $a ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Entity</label>
                            <select name="entity_type" class="form-select">
                                <option value="">All</option>
                                <?php foreach (['product','category','service','user'] as $e): ?>
                                    <option value="<?= $e ?>" <?= ($filters['entity_type'] ?? '') === $e ? 'selected' : '' ?>><?= $e ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">From</label>
                            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>" class="form-control" />
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To</label>
                            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>" class="form-control" />
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Search</label>
                            <input type="text" name="q" value="<?= htmlspecialchars($filters['q'] ?? '') ?>" class="form-control" placeholder="Details or admin" />
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button class="btn btn-success" type="submit"><i class="fas fa-search me-2"></i>Filter</button>
                            <a href="super_dashboard.php" class="btn btn-outline-secondary"><i class="fas fa-times me-2"></i>Clear</a>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Admin</th>
                                    <th>Action</th>
                                    <th>Entity</th>
                                    <th>Details</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activities as $log): ?>
                                    <tr>
                                        <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($log['created_at']))) ?></td>
                                        <td><?= htmlspecialchars(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? '')) ?><br><small class="text-muted"><?= htmlspecialchars($log['email'] ?? '') ?></small></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($log['action']) ?></span></td>
                                        <td><?= htmlspecialchars($log['entity_type']) ?><?php if ($log['entity_id']): ?> #<?= (int)$log['entity_id'] ?><?php endif; ?></td>
                                        <td style="max-width: 360px; white-space: pre-line;"><?= htmlspecialchars($log['details'] ?? '') ?></td>
                                        <td><small class="text-muted"><?= htmlspecialchars($log['ip_address'] ?? '') ?></small></td>
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