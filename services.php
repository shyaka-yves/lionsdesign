<?php
session_start();
require_once 'config/database.php';

// Get search parameter from URL (for global search redirects)
$search_param = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch all services (AJAX will handle filtering)
$stmt = $conn->prepare('SELECT * FROM services ORDER BY id DESC');
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services | Lions Design</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Remove custom body font to use global style */
        .service-card { border: 1px solid #eee; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: box-shadow .2s; background: #fff; }
        .service-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.10); }
        .service-img { width: 100%; height: 200px; object-fit: cover; border-radius: 12px 12px 0 0; }
        .service-title { font-weight: 700; font-size: 1.2rem; }
        .service-price { color: #009e3c; font-weight: 600; }
        .btn-request { background: #009e3c; color: #fff; border: none; font-weight: 600; }
        .btn-request:hover { background: #007a2c; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <h2 class="mb-4 text-center" style="font-weight:700;">Custom Printing Services</h2>
    
    <!-- Search Form -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-8 col-lg-6">
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput" 
                       placeholder="Search services..." 
                       onkeyup="searchServices()"
                       value="<?= htmlspecialchars($search_param) ?>"
                       style="border-radius: 25px 0 0 25px; padding: 12px 20px;">
                <button class="btn btn-request px-4" type="button" onclick="searchServices()" style="border-radius: 0 25px 25px 0;">
                    <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-outline-secondary ms-2" type="button" onclick="clearSearch()" style="border-radius: 25px;" title="Clear search">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Search Results Container -->
    <div id="searchResults"></div>
    
    <!-- Services Display -->
    <div id="servicesDisplay">
        <div class="row g-4">
            <?php foreach ($services as $service): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="service-card h-100 d-flex flex-column">
                        <img src="<?= htmlspecialchars($service['image']) ?>" class="service-img" alt="<?= htmlspecialchars($service['title']) ?>">
                        <div class="p-3 flex-grow-1 d-flex flex-column">
                            <div class="service-title mb-2"><?= htmlspecialchars($service['title']) ?></div>
                            <div class="mb-2" style="font-size:0.95rem; color:#444;">
                                <?= nl2br(htmlspecialchars($service['description'])) ?>
                            </div>
                            <div class="service-price mb-3">Starting at <?= htmlspecialchars($service['price']) ?></div>
                            <div class="mt-auto">
                               <p style="position: relative; bottom: 20px;color:#333;">"If you are interested in this service, please contact us." </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Request Service Modal (to be implemented in next step) -->
<div class="modal fade" id="requestServiceModal" tabindex="-1" aria-labelledby="requestServiceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="serviceRequestForm" method="post" enctype="multipart/form-data" action="ajax/request_service.php">
        <div class="modal-header">
          <h5 class="modal-title" id="requestServiceModalLabel">Request Service</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="service_id" id="modalServiceId">
          <div class="mb-3">
            <label for="modalServiceTitle" class="form-label">Service</label>
            <input type="text" class="form-control" id="modalServiceTitle" readonly>
          </div>
          <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" name="full_name" id="full_name" required>
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" class="form-control" name="phone" id="phone" required>
          </div>
          <div class="mb-3">
            <label for="file" class="form-label">Upload Logo/Design (jpg, png, pdf, max 5MB)</label>
            <input type="file" class="form-control" name="file" id="file" accept=".jpg,.jpeg,.png,.pdf">
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">Additional Instructions</label>
            <textarea class="form-control" name="message" id="message" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-request">Submit Request</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
<script>
// Fill modal with service info
var requestServiceModal = document.getElementById('requestServiceModal');
requestServiceModal.addEventListener('show.bs.modal', function (event) {
  var button = event.relatedTarget;
  var serviceId = button.getAttribute('data-service-id');
  var serviceTitle = button.getAttribute('data-service-title');
  document.getElementById('modalServiceId').value = serviceId;
  document.getElementById('modalServiceTitle').value = serviceTitle;
});

let searchTimeout;

// Search services function
function searchServices() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const searchTerm = document.getElementById('searchInput').value;
        
        const formData = new FormData();
        formData.append('action', 'search_services');
        formData.append('search_term', searchTerm);

        fetch('ajax/filter_services.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayFilteredServices(data.services, data.search_term);
            }
        })
        .catch(error => console.error('Error:', error));
    }, 300); // Debounce search for better performance
}

// Display filtered services
function displayFilteredServices(services, searchTerm = '') {
    const container = document.getElementById('servicesDisplay');
    
    if (services.length === 0) {
        let message = 'No services found';
        if (searchTerm) {
            message = `No services found for "${searchTerm}"`;
        }
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-concierge-bell fa-3x text-muted mb-3"></i>
                <h3>${message}</h3>
                <p class="text-muted">Try adjusting your search criteria.</p>
                <button class="btn btn-request" onclick="clearSearch()">
                    <i class="fas fa-refresh me-2"></i>Clear Search
                </button>
            </div>
        `;
        return;
    }

    let html = '';
    if (searchTerm) {
        html += `<div class="mb-3">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Found ${services.length} service(s) matching "${searchTerm}"
            </div>
        </div>`;
    }
    
    html += '<div class="row g-4">';
    services.forEach(service => {
        html += `
            <div class="col-md-4 col-lg-3">
                <div class="service-card h-100 d-flex flex-column">
                    <img src="${service.image}" class="service-img" alt="${service.title}">
                    <div class="p-3 flex-grow-1 d-flex flex-column">
                        <div class="service-title mb-2">${service.title}</div>
                        <div class="mb-2" style="font-size:0.95rem; color:#444;">
                            ${service.description.replace(/\n/g, '<br>')}
                        </div>
                        <div class="service-price mb-3">Starting at ${service.price}</div>
                        <div class="mt-auto">
                        <p style="position: relative; bottom: 20px;color:#333;">"If you are interested in this service, please contact us." </p>    
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
}

// Clear search
function clearSearch() {
    document.getElementById('searchInput').value = '';
    
    // Reload the page to show all services
    window.location.reload();
}

// Auto-search on page load if search parameter exists
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput && searchInput.value.trim()) {
        // Trigger search automatically if there's a search term from URL
        searchServices();
    }
});

// AJAX submit for service request
const form = document.getElementById('serviceRequestForm');
form.addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(form);
  
  // Show loading state
  const submitBtn = form.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.innerHTML = 'Submitting...';
  submitBtn.disabled = true;
  
  fetch('ajax/request_service.php', {
    method: 'POST',
    body: formData
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    if (data.redirect) {
      window.location.href = data.redirect;
      return;
    }
    if (data.success) {
      form.reset();
      var modal = bootstrap.Modal.getInstance(requestServiceModal);
      modal.hide();
      alert('Service requested and added to cart!');
      if (typeof updateCartCount === 'function') updateCartCount();
    } else {
      alert(data.error || 'Request failed.');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Request failed: ' + error.message);
  })
  .finally(() => {
    // Reset button state
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
  });
});
</script>
</body>
</html>