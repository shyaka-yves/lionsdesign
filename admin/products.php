<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is admin or super user
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super')) {
    header("Location: ../login.php");
    exit();
}

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $title = sanitizeInput($_POST['title']);
            $description = sanitizeInput($_POST['description']);
            $price = floatval($_POST['price']);
            $category_id = intval($_POST['category_id']);
            $stock_quantity = intval($_POST['stock_quantity']);
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            
            // Handle image upload
            $image = 'assets/images/products/default.jpg';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_dir = '../assets/images/products/';
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $file_name = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = 'assets/images/products/' . $file_name;
                }
            }
            
            if (addProduct($conn, $title, $description, $price, $image, $category_id, $stock_quantity, $is_featured)) {
                $message = 'Product added successfully!';
                logAdminActivity($conn, (int)$_SESSION['user_id'], 'create', 'product', null, "title=$title, price=$price");
            } else {
                $error = 'Error adding product.';
            }
            break;
            
        case 'edit':
            $id = intval($_POST['id']);
            $title = sanitizeInput($_POST['title']);
            $description = sanitizeInput($_POST['description']);
            $price = floatval($_POST['price']);
            $category_id = intval($_POST['category_id']);
            $stock_quantity = intval($_POST['stock_quantity']);
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            // Handle image upload
            $image = $_POST['current_image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_dir = '../assets/images/products/';
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $file_name = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = 'assets/images/products/' . $file_name;
                }
            }
            
            if (updateProduct($conn, $id, $title, $description, $price, $image, $category_id, $stock_quantity, $is_featured, $is_active)) {
                $message = 'Product updated successfully!';
                logAdminActivity($conn, (int)$_SESSION['user_id'], 'update', 'product', $id, "title=$title, price=$price");
            } else {
                $error = 'Error updating product.';
            }
            break;
            
        case 'delete':
            $id = intval($_POST['id']);
            if (deleteProduct($conn, $id)) {
                $message = 'Product deleted successfully!';
                logAdminActivity($conn, (int)$_SESSION['user_id'], 'delete', 'product', $id, '');
            } else {
                $error = 'Error deleting product.';
            }
            break;
            
        case 'toggle_status':
            $id = intval($_POST['id']);
            $new_status = intval($_POST['new_status']);
            
            $stmt = $conn->prepare("UPDATE products SET is_active = ? WHERE id = ?");
            if ($stmt->execute([$new_status, $id])) {
                $status_text = $new_status ? 'activated' : 'deactivated';
                $message = "Product $status_text successfully!";
                logAdminActivity($conn, (int)$_SESSION['user_id'], $new_status ? 'activate' : 'deactivate', 'product', $id, '');
            } else {
                $error = 'Error updating product status.';
            }
            break;
    }
}

// Get search and filter parameters
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category_filter'] ?? '';
$status_filter = $_GET['status_filter'] ?? '';

// Build the query with filters
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(p.title LIKE ? OR p.description LIKE ? OR c.name LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if (!empty($category_filter)) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $category_filter;
}

if ($status_filter !== '') {
    $where_conditions[] = "p.is_active = ?";
    $params[] = $status_filter;
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get filtered products
$sql = "SELECT p.*, c.name as category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        $where_clause
        ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for form
$categories = getCategories($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="admin.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="admin-mobile-header d-md-none mb-2">
                    <button class="btn btn-dark" type="button" data-bs-toggle="collapse" data-bs-target=".admin-sidebar">
                        <i class="fas fa-bars me-2"></i>Menu
                    </button>
                </div>
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Products</h1>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus me-2"></i>Add Product
                    </button>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <!-- Search and Filter -->
                <div class="admin-search-container">
                    <div class="admin-search-form">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search Products</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" 
                                       placeholder="Search by title, description, category...">
                            </div>
                            <div class="col-md-3">
                                <label for="category_filter" class="form-label">Category</label>
                                <select class="form-select" id="category_filter" name="category_filter">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                                <?php echo (isset($_GET['category_filter']) && $_GET['category_filter'] == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status_filter" class="form-label">Status</label>
                                <select class="form-select" id="status_filter" name="status_filter">
                                    <option value="">All Status</option>
                                    <option value="1" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == '1') ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == '0') ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary w-100 mb-2">
                                        <i class="fas fa-search me-2"></i>Search
                                    </button>
                                    <a href="products.php" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-refresh me-2"></i>Clear
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-success text-white">
                                    <tr>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Featured</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td>
                                                <img src="../<?php echo $product['image']; ?>" alt="<?php echo $product['title']; ?>" 
                                                     style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                            </td>
                                            <td><?php echo $product['title']; ?></td>
                                            <td><?php echo $product['category_name']; ?></td>
                                            <td><?php echo formatPrice($product['price']); ?></td>
                                            <td>
                                                <?php 
                                                $stock = $product['stock_quantity'];
                                                if ($stock <= 0): ?>
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                <?php elseif ($stock <= 5): ?>
                                                    <span class="badge bg-warning">Low Stock (<?php echo $stock; ?>)</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success"><?php echo $stock; ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($product['is_featured']): ?>
                                                    <span class="badge bg-success">Yes</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($product['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="deleteProduct(<?php echo $product['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <button class="btn btn-<?php echo $product['is_active'] ? 'secondary' : 'success'; ?> btn-sm" onclick="toggleProductStatus(<?php echo $product['id']; ?>, <?php echo $product['is_active'] ? 'false' : 'true'; ?>)">
                                                    <i class="fas fa-<?php echo $product['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                </button>
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

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Product Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Price *</label>
                                            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="0" min="0" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-control" id="category_id" name="category_id">
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Product Image</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <div class="form-text">Recommended size: 400x400px</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                                        <label class="form-check-label" for="is_featured">
                                            Featured Product
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        <input type="hidden" name="current_image" id="edit_current_image">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="edit_title" class="form-label">Product Title *</label>
                                    <input type="text" class="form-control" id="edit_title" name="title" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit_description" class="form-label">Description</label>
                                    <textarea class="form-control" id="edit_description" name="description" rows="4"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_price" class="form-label">Price *</label>
                                            <input type="number" class="form-control" id="edit_price" name="price" step="0.01" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_stock_quantity" class="form-label">Stock Quantity *</label>
                                            <input type="number" class="form-control" id="edit_stock_quantity" name="stock_quantity" min="0" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit_category_id" class="form-label">Category</label>
                                    <select class="form-control" id="edit_category_id" name="category_id">
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                                                    <label for="edit_image" class="form-label">Product Image</label>
                                <div class="mb-2">
                                    <img id="edit_current_image_preview" src="" alt="Current Image" class="img-thumbnail" style="max-width: 200px; max-height: 200px; display: none;">
                                    <small class="text-muted d-block">Current image will be replaced if a new one is selected</small>
                                </div>
                                <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                                    <div class="form-text">Leave empty to keep current image</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="edit_is_featured" name="is_featured">
                                        <label class="form-check-label" for="edit_is_featured">
                                            Featured Product
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                                        <label class="form-check-label" for="edit_is_active">
                                            Active (Visible to customers)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete_id">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editProduct(product) {
    document.getElementById('edit_id').value = product.id;
    document.getElementById('edit_title').value = product.title;
    document.getElementById('edit_description').value = product.description;
    document.getElementById('edit_price').value = product.price;
    document.getElementById('edit_stock_quantity').value = product.stock_quantity;
    document.getElementById('edit_category_id').value = product.category_id;
    document.getElementById('edit_current_image').value = product.image;
    document.getElementById('edit_is_featured').checked = product.is_featured == 1;
    document.getElementById('edit_is_active').checked = product.is_active == 1;
    
    // Show current image preview
    const imagePreview = document.getElementById('edit_current_image_preview');
    if (product.image) {
        imagePreview.src = '../' + product.image;
        imagePreview.style.display = 'block';
    } else {
        imagePreview.style.display = 'none';
    }
    
    new bootstrap.Modal(document.getElementById('editProductModal')).show();
}

        function deleteProduct(id) {
            document.getElementById('delete_id').value = id;
            new bootstrap.Modal(document.getElementById('deleteProductModal')).show();
        }
        
        function toggleProductStatus(id, newStatus) {
            const statusText = newStatus ? 'activate' : 'deactivate';
            if (confirm(`Are you sure you want to ${statusText} this product?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="toggle_status">
                    <input type="hidden" name="id" value="${id}">
                    <input type="hidden" name="new_status" value="${newStatus ? 1 : 0}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html> 