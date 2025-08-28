<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is admin or super user
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super')) {
    header("Location: ../login.php");
    exit();
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $feedback_id = intval($_POST['feedback_id']);
    $status = $_POST['status'];
    
    if (updateFeedbackStatus($conn, $feedback_id, $status)) {
        $success_message = "Feedback status updated successfully!";
    } else {
        $error_message = "Error updating feedback status.";
    }
}

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'])) {
    $selected_feedback = $_POST['selected_feedback'] ?? [];
    $bulk_action = $_POST['bulk_action'];
    
    if (!empty($selected_feedback)) {
        foreach ($selected_feedback as $feedback_id) {
            updateFeedbackStatus($conn, $feedback_id, $bulk_action);
        }
        $success_message = "Bulk action completed successfully!";
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$search_term = html_entity_decode(strip_tags(trim($_GET['search'] ?? '')));

// Get all feedback
$all_feedback = getAllFeedback($conn);

// Filter feedback based on parameters
$filtered_feedback = [];
$search_term_lower = strtolower(trim($search_term));

foreach ($all_feedback as $feedback) {
    $matches_status = ($status_filter === 'all' || $feedback['status'] === $status_filter);
    
    // Enhanced search functionality
    $matches_search = empty($search_term_lower);
    if (!$matches_search) {
        $name_lower = strtolower($feedback['name']);
        $email_lower = strtolower($feedback['email']);
        $subject_lower = strtolower($feedback['subject']);
        $message_lower = strtolower($feedback['message']);
        
        $matches_search = strpos($name_lower, $search_term_lower) !== false ||
                         strpos($email_lower, $search_term_lower) !== false ||
                         strpos($subject_lower, $search_term_lower) !== false ||
                         strpos($message_lower, $search_term_lower) !== false;
    }
    
    if ($matches_status && $matches_search) {
        $filtered_feedback[] = $feedback;
    }
}

function getStatusBadge($status) {
    switch ($status) {
        case 'unread': return 'danger';
        case 'read': return 'warning';
        case 'replied': return 'success';
        default: return 'secondary';
    }
}

function getStatusIcon($status) {
    switch ($status) {
        case 'unread': return 'fas fa-envelope';
        case 'read': return 'fas fa-envelope-open';
        case 'replied': return 'fas fa-reply';
        default: return 'fas fa-question-circle';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Feedback - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="admin.css?v=<?php echo time(); ?>" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .feedback-card {
            transition: transform 0.2s;
            border-left: 4px solid;
            margin-bottom: 1rem;
        }
        .feedback-card:hover {
            transform: translateY(-2px);
        }
        .feedback-card.unread {
            border-left-color: #dc3545;
            background-color: #fff5f5;
        }
        .feedback-card.read {
            border-left-color: #ffc107;
        }
        .feedback-card.replied {
            border-left-color: #198754;
        }
        .message-preview {
            max-height: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.4;
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        
        /* Mobile responsive improvements */
        @media (max-width: 768px) {
            .feedback-card .card-body {
                padding: 0.75rem;
            }
            .feedback-card .card-title {
                font-size: 0.9rem;
            }
            .feedback-card .card-text {
                font-size: 0.8rem;
            }
            .message-preview {
                max-height: 80px;
            }
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 0.5rem;
            }
            .d-flex.justify-content-between > div:first-child {
                order: 2;
            }
            .d-flex.justify-content-between > div:last-child {
                order: 1;
            }
        }
        
        @media (max-width: 576px) {
            .feedback-card .card-body {
                padding: 0.5rem;
            }
            .feedback-card .card-title {
                font-size: 0.85rem;
            }
            .feedback-card .card-text {
                font-size: 0.75rem;
            }
            .message-preview {
                max-height: 60px;
            }
            .badge {
                font-size: 0.7rem;
                padding: 0.2rem 0.4rem;
            }
        }
    </style>
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
                    <h1 class="h2">
                        <i class="fas fa-comments me-2"></i>Customer Feedback
                    </h1>
                    <div>
                        <span class="badge bg-danger me-2">
                            <?php echo getUnreadFeedbackCount($conn); ?> Unread
                        </span>
                        <button onclick="window.print();" class="btn btn-outline-secondary">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                    </div>
                </div>

                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3" id="searchForm">
                            <div class="col-12 col-md-4">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?php echo htmlspecialchars($search_term); ?>" 
                                       placeholder="Search by name, email, subject, message..."
                                       onkeyup="debounceSearch()">
                                <!-- Debug: <?php echo htmlspecialchars($search_term); ?> -->
                            </div>
                            <div class="col-12 col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" onchange="submitForm()">
                                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                                    <option value="unread" <?php echo $status_filter === 'unread' ? 'selected' : ''; ?>>Unread</option>
                                    <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Read</option>
                                    <option value="replied" <?php echo $status_filter === 'replied' ? 'selected' : ''; ?>>Replied</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-5">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid gap-2 d-md-block">
                                    <button type="submit" class="btn btn-primary w-100 w-md-auto">
                                        <i class="fas fa-search me-2"></i>Search
                                    </button>
                                    <a href="feedback.php" class="btn btn-outline-secondary w-100 w-md-auto">
                                        <i class="fas fa-refresh me-2"></i>Clear
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <form method="POST" id="bulkForm">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="mb-0">
                                Feedback Messages 
                                <?php if ($search_term || $status_filter !== 'all'): ?>
                                    <span class="text-muted">(<?php echo count($filtered_feedback); ?> found)</span>
                                <?php else: ?>
                                    <span class="text-muted">(<?php echo count($filtered_feedback); ?> total)</span>
                                <?php endif; ?>
                                <?php if ($search_term): ?>
                                    <small class="text-info">
                                        <i class="fas fa-search me-1"></i>Searching for: "<?php echo htmlspecialchars($search_term); ?>"
                                    </small>
                                <?php endif; ?>
                            </h5>
                            <div class="d-flex gap-2 flex-wrap">
                                <select class="form-select form-select-sm" name="bulk_action" style="width: auto; min-width: 120px;">
                                    <option value="">Bulk Actions</option>
                                    <option value="read">Mark as Read</option>
                                    <option value="replied">Mark as Replied</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Are you sure you want to perform this action?')">
                                    Apply
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($filtered_feedback)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h4>No feedback found</h4>
                                    <?php if ($search_term || $status_filter !== 'all'): ?>
                                        <p class="text-muted">
                                            No feedback messages match your search criteria.
                                            <?php if ($search_term): ?>
                                                <br>Try adjusting your search term or 
                                            <?php endif; ?>
                                            <a href="feedback.php" class="text-decoration-none">clear all filters</a>.
                                        </p>
                                    <?php else: ?>
                                        <p class="text-muted">No feedback messages available.</p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($filtered_feedback as $feedback): ?>
                                        <div class="col-12 col-sm-6 col-lg-4 mb-4">
                                            <div class="card feedback-card h-100 <?php echo $feedback['status']; ?>">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <input type="checkbox" class="form-check-input me-2" 
                                                               name="selected_feedback[]" value="<?php echo $feedback['id']; ?>">
                                                        <strong><?php echo htmlspecialchars($feedback['name']); ?></strong>
                                                    </div>
                                                    <span class="badge bg-<?php echo getStatusBadge($feedback['status']); ?> status-badge">
                                                        <i class="<?php echo getStatusIcon($feedback['status']); ?> me-1"></i>
                                                        <?php echo ucfirst($feedback['status']); ?>
                                                    </span>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-2">
                                                        <strong>Subject:</strong> <?php echo htmlspecialchars($feedback['subject']); ?>
                                                    </div>
                                                    <div class="mb-2">
                                                        <strong>Email:</strong> 
                                                        <a href="mailto:<?php echo htmlspecialchars($feedback['email']); ?>">
                                                            <?php echo htmlspecialchars($feedback['email']); ?>
                                                        </a>
                                                    </div>
                                                    <div class="mb-2">
                                                        <strong>Date:</strong> 
                                                        <?php echo date('M d, Y H:i', strtotime($feedback['created_at'])); ?>
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Message:</strong>
                                                        <div class="message-preview mt-1">
                                                            <?php echo htmlspecialchars(substr($feedback['message'], 0, 150)); ?>
                                                            <?php if (strlen($feedback['message']) > 150): ?>...<?php endif; ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-grid gap-2 d-sm-flex">
                                                        <button type="button" class="btn btn-sm btn-outline-primary flex-fill" 
                                                                onclick="viewFeedback(<?php echo $feedback['id']; ?>)">
                                                            <i class="fas fa-eye me-1"></i>View
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-success flex-fill" 
                                                                onclick="updateStatus(<?php echo $feedback['id']; ?>, 'replied')">
                                                            <i class="fas fa-reply me-1"></i>Replied
                                                        </button>
                                                        <?php if ($feedback['status'] === 'unread'): ?>
                                                            <button type="button" class="btn btn-sm btn-outline-warning flex-fill" 
                                                                    onclick="updateStatus(<?php echo $feedback['id']; ?>, 'read')">
                                                                <i class="fas fa-check me-1"></i>Read
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <!-- Feedback Detail Modal -->
    <div class="modal fade" id="feedbackModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Feedback Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="feedbackModalBody">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer flex-wrap gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" onclick="markAsReplied()">
                        <i class="fas fa-reply me-2"></i>Mark as Replied
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentFeedbackId = null;

        function viewFeedback(feedbackId) {
            currentFeedbackId = feedbackId;
            
            fetch(`ajax/get_feedback.php?id=${feedbackId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const feedback = data.feedback;
                        document.getElementById('feedbackModalBody').innerHTML = `
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <p><strong>Name:</strong> ${feedback.name}</p>
                                    <p><strong>Email:</strong> <a href="mailto:${feedback.email}">${feedback.email}</a></p>
                                    <p><strong>Subject:</strong> ${feedback.subject}</p>
                                    <p><strong>Date:</strong> ${new Date(feedback.created_at).toLocaleString()}</p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-${getStatusBadge(feedback.status)}">
                                            ${feedback.status.charAt(0).toUpperCase() + feedback.status.slice(1)}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-12 col-md-6">
                                    <p><strong>Message:</strong></p>
                                    <div class="border p-3 bg-light rounded">
                                        ${feedback.message.replace(/\n/g, '<br>')}
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        // Mark as read if unread
                        if (feedback.status === 'unread') {
                            updateStatus(feedbackId, 'read');
                        }
                        
                        new bootstrap.Modal(document.getElementById('feedbackModal')).show();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function updateStatus(feedbackId, status) {
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('feedback_id', feedbackId);
            formData.append('status', status);

            fetch('ajax/feedback_actions.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function markAsReplied() {
            if (currentFeedbackId) {
                updateStatus(currentFeedbackId, 'replied');
                bootstrap.Modal.getInstance(document.getElementById('feedbackModal')).hide();
            }
        }

        function getStatusBadge(status) {
            switch (status) {
                case 'unread': return 'danger';
                case 'read': return 'warning';
                case 'replied': return 'success';
                default: return 'secondary';
            }
        }

        // Search functionality
        let searchTimeout;

        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                submitForm();
            }, 500); // 500ms delay for better performance
        }

        function submitForm() {
            document.getElementById('searchForm').submit();
        }

        // Highlight search terms in results
        function highlightSearchTerms() {
            const searchTerm = '<?php echo htmlspecialchars($search_term); ?>';
            if (searchTerm && searchTerm.length > 0) {
                const elements = document.querySelectorAll('.feedback-card .card-body');
                elements.forEach(element => {
                    const text = element.innerHTML;
                    const highlightedText = text.replace(
                        new RegExp(searchTerm, 'gi'),
                        match => `<mark class="bg-warning">${match}</mark>`
                    );
                    element.innerHTML = highlightedText;
                });
            }
        }

        // Initialize highlighting when page loads
        document.addEventListener('DOMContentLoaded', function() {
            highlightSearchTerms();
        });
    </script>
</body>
</html> 