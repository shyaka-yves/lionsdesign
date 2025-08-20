<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !$_SESSION['role']) {
    header('Location: ../login.php');
    exit;
}
require_once '../config/database.php';

// Handle add/edit/delete actions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add or edit service
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $id = $_POST['service_id'] ?? '';
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['image']['size'] <= 5*1024*1024) {
            $filename = uniqid().'.'.$ext;
            $target = '../uploads/services/'.$filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image_path = 'uploads/services/'.$filename;
            }
        }
    }
    if ($id) {
        // Edit
        $sql = 'UPDATE services SET title=?, description=?, price=?'.($image_path?', image=?':'').' WHERE id=?';
        $params = [$title, $description, $price];
        if ($image_path) $params[] = $image_path;
        $params[] = $id;
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $message = 'Service updated!';
        require_once '../includes/functions.php';
        logAdminActivity($conn, (int)$_SESSION['user_id'], 'update', 'service', (int)$id, "title=$title");
    } else {
        // Add
        $sql = 'INSERT INTO services (title, description, image, price) VALUES (?, ?, ?, ?)';
        $stmt = $conn->prepare($sql);
        $stmt->execute([$title, $description, $image_path, $price]);
        $message = 'Service added!';
        require_once '../includes/functions.php';
        logAdminActivity($conn, (int)$_SESSION['user_id'], 'create', 'service', (int)$conn->lastInsertId(), "title=$title");
    }
}
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare('DELETE FROM services WHERE id=?');
    $stmt->execute([$id]);
    $message = 'Service deleted!';
    require_once '../includes/functions.php';
    logAdminActivity($conn, (int)$_SESSION['user_id'], 'delete', 'service', (int)$id, '');
}
// Get search parameter
$search = $_GET['search'] ?? '';

// Fetch all services
$stmt = $conn->prepare('SELECT * FROM services ORDER BY id DESC');
$stmt->execute();
$all_services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filter services based on search
$services = [];
if (!empty($search)) {
    $search_lower = strtolower(trim($search));
    foreach ($all_services as $service) {
        if (strpos(strtolower($service['title']), $search_lower) !== false ||
            strpos(strtolower($service['description']), $search_lower) !== false) {
            $services[] = $service;
        }
    }
} else {
    $services = $all_services;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="admin.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="admin-mobile-header d-md-none mb-2">
                <button class="btn btn-dark" type="button" data-bs-toggle="collapse" data-bs-target=".admin-sidebar">
                    <i class="fas fa-bars me-2"></i>Menu
                </button>
            </div>
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Services</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#serviceModal" onclick="clearServiceForm()">
                        <i class="fas fa-plus me-2"></i>Add Service
                    </button>
                </div>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Search and Filter -->
            <div class="admin-search-container">
                <div class="admin-search-form">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <label for="search" class="form-label">Search Services</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" 
                                   placeholder="Search by title, description...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                                <a href="services.php" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-refresh me-2"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Services List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($services as $service): ?>
                                <tr>
                                    <td><?= $service['id'] ?></td>
                                    <td>
                                        <?php if ($service['image']): ?>
                                            <img src="../<?= htmlspecialchars($service['image']) ?>" class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover;">
                                        <?php else: ?>
                                            <span class="text-muted">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($service['title']) ?></td>
                                    <td style="max-width:200px; white-space:pre-line;"><?= htmlspecialchars($service['description']) ?></td>
                                    <td><?= htmlspecialchars($service['price']) ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#serviceModal" onclick='editService(<?= json_encode($service) ?>)'>
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </button>
                                        <a href="?delete=<?= $service['id'] ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Delete this service?')">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Service Modal -->
            <div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="post" enctype="multipart/form-data" id="serviceForm">
                    <div class="modal-header">
                      <h5 class="modal-title" id="serviceModalLabel">Add/Edit Service</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" name="service_id" id="service_id">
                      <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" id="title" required>
                      </div>
                      <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
                      </div>
                      <div class="mb-3">
                        <label for="price" class="form-label">Price (e.g. From 10,000 RWF)</label>
                        <input type="text" class="form-control" name="price" id="price" required>
                      </div>
                      <div class="mb-3">
                        <label for="image" class="form-label">Image (jpg, png, webp, max 5MB)</label>
                        <input type="file" class="form-control" name="image" id="image" accept=".jpg,.jpeg,.png,.webp">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-success">Save</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
<script>
function editService(service) {
    document.getElementById('service_id').value = service.id;
    document.getElementById('title').value = service.title;
    document.getElementById('description').value = service.description;
    document.getElementById('price').value = service.price;
}
function clearServiceForm() {
    document.getElementById('service_id').value = '';
    document.getElementById('title').value = '';
    document.getElementById('description').value = '';
    document.getElementById('price').value = '';
    document.getElementById('image').value = '';
}
</script>
</body>
</html>