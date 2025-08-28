<head>
    <link rel="icon" type="image/png" href="image/logo.png">
    <link rel="shortcut icon" type="image/png" href="image/logo.png">
    <link rel="apple-touch-icon" href="image/logo.png">
    <meta name="msapplication-TileImage" content="image/logo.png">
    <link rel="stylesheet" href="https://unpkg.com/@geist/font@latest/css/geist.css" />
<style>
    body, html {
        font-family: 'Geist', 'Geist Variable', 'Geist Mono', 'Geist Mono Variable', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
    }
    
    /* Global Search Styles */
    .global-search-container {
        position: relative;
        max-width: 400px;
        width: 100%;
    }
    
    /* Mobile Search Styles */
    @media (max-width: 991px) {
        .navbar .container {
            padding: 0.5rem 1rem;
        }
        
        .global-search-container {
            max-width: 100%;
            width: 100%;
            flex: 1;
            margin: 0 0.5rem;
        }
        
        .global-search-input {
            font-size: 16px; /* Prevents zoom on iOS */
            padding: 0.6rem 0.8rem 0.6rem 2.2rem;
            height: 44px; /* Touch-friendly height */
            border-radius: 22px;
        }
        
        .global-search-icon {
            left: 0.8rem;
            font-size: 16px;
            top: 50%;
            transform: translateY(-50%);
        }
        
        .navbar-brand {
            margin-right: 0.5rem;
            flex-shrink: 0;
        }
        
        .navbar-brand .logo {
            width: 100px !important;
            height: 58px !important;
        }
        
        .mobile-header-row {
            min-height: 60px;
        }
        
        .mobile-search-wrapper {
            display: flex;
            align-items: center;
            min-width: 0; /* Allow shrinking */
        }

        .search-results-dropdown {
            position: fixed !important;
            top: 70px !important;
            left: 1rem !important;
            right: 1rem !important;
            width: auto !important;
            max-height: calc(100vh - 80px);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            z-index: 9999;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }

        .search-result-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-image {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            margin-right: 1rem;
        }

        .search-result-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .search-result-description {
            font-size: 0.85rem;
            color: #666;
            line-height: 1.3;
            margin-bottom: 0.25rem;
        }
        
        .search-result-price {
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        /* Badge adjustments for mobile */
        .search-result-title .badge {
            font-size: 0.65rem;
            padding: 0.25rem 0.5rem;
            margin-right: 0.5rem;
        }
    }

    /* Small tablets and large phones */
    @media (max-width: 768px) {
        .mobile-header-row {
            min-height: 55px;
        }
        
        .global-search-input {
            font-size: 15px;
            height: 42px;
            padding: 0.55rem 0.75rem 0.55rem 2.1rem;
        }
        
        .navbar-brand .logo {
            width: 90px !important;
            height: 52px !important;
        }
        
        .navbar-toggler {
            padding: 0.25rem 0.5rem;
            font-size: 1rem;
        }
    }

    /* Extra small phones */
    @media (max-width: 480px) {
        .navbar .container {
            padding: 0.4rem 0.8rem;
        }
        
        .mobile-header-row {
            min-height: 50px;
        }
        
        .mobile-header-row .mx-2 {
            margin-left: 0.75rem !important;
            margin-right: 0.75rem !important;
        }
        
        .global-search-input {
            font-size: 16px; /* Prevents zoom */
            height: 40px;
            padding: 0.5rem 0.7rem 0.5rem 2rem;
            border-radius: 20px;
        }
        
        .global-search-icon {
            left: 0.7rem;
            font-size: 14px;
        }
        
        .navbar-brand .logo {
            width: 80px !important;
            height: 46px !important;
        }
        
        .navbar-toggler {
            padding: 0.2rem 0.4rem;
            font-size: 0.9rem;
            border: 1px solid rgba(255,255,255,.1);
        }
        
        .search-results-dropdown {
            top: 60px !important;
            left: 0.5rem !important;
            right: 0.5rem !important;
        }
        
        .search-result-item {
            padding: 0.8rem;
        }
        
        .search-result-image {
            width: 45px;
            height: 45px;
        }
        
        .search-result-title {
            font-size: 0.9rem;
        }
        
        .search-result-description {
            font-size: 0.8rem;
        }
        
        .search-result-title .badge {
            font-size: 0.6rem;
            padding: 0.2rem 0.4rem;
        }
    }
    
    /* Very small screens */
    @media (max-width: 360px) {
        .mobile-header-row {
            min-height: 48px;
        }
        
        .mobile-header-row .mx-2 {
            margin-left: 0.5rem !important;
            margin-right: 0.5rem !important;
        }
        
        .global-search-input {
            font-size: 15px;
            height: 38px;
            padding: 0.45rem 0.6rem 0.45rem 1.8rem;
        }
        
        .navbar-brand .logo {
            width: 70px !important;
            height: 40px !important;
        }
        
        .navbar-toggler {
            padding: 0.15rem 0.3rem;
            font-size: 0.8rem;
        }
        
        .search-result-description {
            display: none; /* Hide on very small screens */
        }
    }
    
    @media (min-width: 992px) {
        .mobile-search-wrapper {
            display: none !important;
        }
        
        /* Ensure desktop search is properly positioned */
        .global-search-container.d-none.d-lg-block {
            position: relative;
            left: -130px;
        }
    }
    
    .global-search-input {
        background: #333;
        border: none;
        border-radius: 15px;
        color: white;
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        width: 100%;
        transition: all 0.3s ease;
        -webkit-appearance: none; /* Remove iOS styling */
        -moz-appearance: none; /* Remove Firefox styling */
        appearance: none;
    }
    
    .global-search-input:focus {
        background: #333;
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        color: white;
        outline: none;
    }
    
    .global-search-input::placeholder {
        color: #666;
    }
    
    .global-search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
        pointer-events: none;
    }
    
    .search-results-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
    }
    
    /* WhatsApp Floating Button Styles */
    .whatsapp-float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 40px;
        right: 40px;
        background-color: #25d366;
        color: #FFF;
        border-radius: 50px;
        text-align: center;
        font-size: 30px;
        box-shadow: 2px 2px 3px #999;
        z-index: 100;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .whatsapp-float:hover {
        background-color: #128c7e;
        color: #FFF;
        transform: scale(1.1);
        text-decoration: none;
    }
    
    .whatsapp-float i {
        margin-top: 0;
    }
    
    .search-results-dropdown {
        border-radius: 10px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        max-height: 400px;
        overflow-y: auto;
        z-index: 2000;
        display: none;
        margin-top: 0.5rem;
    }
    
    .search-result-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f3f4;
        text-decoration: none;
        color: #333;
        transition: background-color 0.2s ease;
        gap: 10px;
    }
    
    .search-result-item:hover {
        background-color: #f8f9fa;
        color: #333;
        text-decoration: none;
    }
    
    .search-result-item:last-child {
        border-bottom: none;
    }
    
    .search-result-image {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 0.75rem;
        background: #f8f9fa;
    }
    
    .search-result-content {
        flex-grow: 1;
        min-width: 0;
    }
    
    .search-result-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }
    
    .search-result-description {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    
    .search-result-price {
        font-size: 0.8rem;
        color: #28a745;
        font-weight: 600;
    }
    
    .search-result-type {
        background: #28a745;
        color: white;
        padding: 0.2rem 0.5rem;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-left: 0.5rem;
    }
    
    .search-result-type.service {
        background: #17a2b8;
    }
    
    .search-result-type.category {
        background: #6f42c1;
    }
    
    .search-no-results {
        padding: 2rem 1rem;
        text-align: center;
        color: #6c757d;
    }
    
    .search-loading {
        padding: 1rem;
        text-align: center;
        color: #6c757d;
    }
    
    /* Mobile responsive */
    @media (max-width: 991px) {
        .global-search-container {
            max-width: 100%;
            margin: 0.5rem 0;
        }
    }
</style>

</head>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <!-- Mobile Header Layout - All elements on same line -->
        <div class="d-flex align-items-center w-100 d-lg-none mobile-header-row">
            <a class="navbar-brand" href="index.php">
                <img  src="image/logo.png"  class="logo" style="width: 120px; height: 70px;">
            </a>
            
            <!-- Mobile Search (visible only on mobile) -->
            <div class="mobile-search-wrapper flex-grow-1 mx-2">
                <div class="global-search-container">
                    <div class="position-relative">
                        <i class="fas fa-search global-search-icon"></i>
                        <input type="text" 
                               class="form-control global-search-input" 
                               id="mobileGlobalSearchInput"
                               placeholder="Search products, services..."
                               autocomplete="off">
                        <div class="search-results-dropdown" id="mobileSearchResultsDropdown">
                            <!-- Search results will be populated here -->
                        </div>
                    </div>
                </div>
            </div>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        
        <!-- Desktop Header Layout -->
        <a class="navbar-brand d-none d-lg-block" href="index.php">
            <img  src="image/logo.png"  class="logo" style="width: 120px; height: 70px;">
        </a>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="shop.php">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="services.php">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
            </ul>
            
            <!-- Global Search Bar (Desktop only) -->
            <div style="position: relative; left: -130px; "  class="global-search-container mx-3 d-none d-lg-block">
                <div class="position-relative">
                    <i class="fas fa-search global-search-icon"></i>
                    <input type="text" 
                           class="form-control global-search-input" 
                           id="globalSearchInput"
                           placeholder="Search products, services..."
                           autocomplete="off">
                    <div class="search-results-dropdown" id="searchResultsDropdown">
                        <!-- Search results will be populated here -->
                    </div>
                </div>
            </div>
            
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="cart.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge bg-success cart-count">0</span>
                    </a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                            <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                            <li><a class="dropdown-item" href="track_order.php">Track Orders</a></li>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role']=== 'admin' || $_SESSION['role']=== 'super'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="admin/">Admin Panel</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/250786551353?text=Hello%20Lions%20Design!%20I'm%20interested%20in%20your%20products%20and%20services." class="whatsapp-float" target="_blank" title="Chat with us on WhatsApp">
    <i class="fab fa-whatsapp"></i>
</a>

<script>
// Update cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    initializeGlobalSearch();
});

function updateCartCount() {
    fetch('ajax/get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            document.querySelector('.cart-count').textContent = data.count;
        })
        .catch(error => console.error('Error:', error));
}

// Global Search Functionality
function initializeGlobalSearch() {
    // Initialize both desktop and mobile search
    initializeSearchInput('globalSearchInput', 'searchResultsDropdown');
    initializeSearchInput('mobileGlobalSearchInput', 'mobileSearchResultsDropdown');
}

function initializeSearchInput(inputId, dropdownId) {
    const searchInput = document.getElementById(inputId);
    const searchResults = document.getElementById(dropdownId);
    let searchTimeout;
    
    if (!searchInput || !searchResults) return;
    
    // Mobile-specific enhancements
    const isMobile = inputId.includes('mobile');
    
    // Search on input
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (searchTerm.length < 2) {
            hideSearchResults(dropdownId);
            return;
        }
        
        // Debounce search (shorter delay on mobile for better responsiveness)
        const delay = isMobile ? 200 : 300;
        searchTimeout = setTimeout(() => {
            performGlobalSearch(searchTerm, dropdownId);
        }, delay);
    });
    
    // Enhanced mobile touch handling
    if (isMobile) {
        // Prevent body scroll when search results are open
        searchInput.addEventListener('focus', function() {
            document.body.style.overflow = 'hidden';
        });
        
        searchInput.addEventListener('blur', function() {
            // Delay to allow click on results
            setTimeout(() => {
                if (!searchResults.matches(':hover')) {
                    document.body.style.overflow = '';
                }
            }, 150);
        });
        
        // Add touch-friendly close button behavior
        searchResults.addEventListener('touchstart', function(e) {
            e.stopPropagation();
        });
    }
    
    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            hideSearchResults(dropdownId);
            if (isMobile) {
                document.body.style.overflow = '';
            }
        }
    });
    
    // Handle touch events for mobile
    if (isMobile) {
        document.addEventListener('touchstart', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                hideSearchResults(dropdownId);
                document.body.style.overflow = '';
            }
        });
    }
    
    // Handle Enter key
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const firstResult = searchResults.querySelector('.search-result-item');
            if (firstResult) {
                window.location.href = firstResult.href;
            } else if (this.value.trim()) {
                // Redirect to shop with search term
                window.location.href = `shop.php?search=${encodeURIComponent(this.value.trim())}`;
            }
        }
        
        // Handle Escape key to close search
        if (e.key === 'Escape') {
            hideSearchResults(dropdownId);
            searchInput.blur();
            if (isMobile) {
                document.body.style.overflow = '';
            }
        }
    });
    
    // Add visual feedback for focus states on mobile
    if (isMobile) {
        searchInput.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
        });
        
        searchInput.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    }
}

function performGlobalSearch(searchTerm, dropdownId) {
    const searchResults = document.getElementById(dropdownId);
    
    // Show loading state
    searchResults.innerHTML = '<div class="dropdown-item text-center"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';
    searchResults.style.display = 'block';
    
    const formData = new FormData();
    formData.append('search_term', searchTerm);
    formData.append('limit', '8');
    
    // Add timeout to prevent infinite loading
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout
    
    fetch('ajax/global_search.php', {
        method: 'POST',
        body: formData,
        signal: controller.signal
    })
    .then(response => {
        clearTimeout(timeoutId);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Search response:', data); // Debug log
        console.log('data.success:', data.success);
        console.log('data.results:', data.results);
        console.log('data.results.length:', data.results ? data.results.length : 'undefined');
        
        if (data.success && data.results && data.results.length > 0) {
            console.log('Calling displayGlobalSearchResults with:', data.results.length, 'results');
            console.log('displayGlobalSearchResults function exists:', typeof displayGlobalSearchResults);
            console.log('About to call displayGlobalSearchResults...');
            displayGlobalSearchResults(data.results, searchTerm, dropdownId);
            console.log('displayGlobalSearchResults call completed');
        } else {
            console.log('No results condition triggered');
            searchResults.innerHTML = '<div class="dropdown-item text-muted">No results found for "' + searchTerm + '"</div>';
            searchResults.style.display = 'block';
        }
    })
    .catch(error => {
        clearTimeout(timeoutId);
        console.error('Search error:', error);
        if (error.name === 'AbortError') {
            searchResults.innerHTML = '<div class="dropdown-item text-warning">Search timeout, try again</div>';
        } else {
            searchResults.innerHTML = '<div class="dropdown-item text-danger">Search failed, try again</div>';
        }
    });
}

function displayGlobalSearchResults(results, searchTerm, dropdownId) {
    const searchResults = document.getElementById(dropdownId);
    
    if (!searchResults) {
        console.error('Search dropdown not found:', dropdownId);
        return;
    }
    
    if (results.length === 0) {
        searchResults.innerHTML = '<div class="dropdown-item text-muted">No results found</div>';
        searchResults.style.display = 'block';
        return;
    }
    
    let html = '';
    let hasProducts = false;
    let hasServices = false;
    
    results.forEach(function(result) {
        // Track what types of results we have
        if (result.type === 'product') hasProducts = true;
        if (result.type === 'service') hasServices = true;
        
        // Add type badge for better distinction
        let typeBadge = '';
        if (result.type === 'product') {
            typeBadge = '<span class="badge bg-primary me-2" style="font-size: 0.7rem;">Product</span>';
        } else if (result.type === 'service') {
            typeBadge = '<span class="badge bg-success me-2" style="font-size: 0.7rem;">Service</span>';
        }
        
        html +=
            '<a href="' + result.url + '" class="search-result-item">' +
                (result.image ? '<img src="' + result.image + '" alt="' + result.title + '" class="search-result-image"/>' : '') +
                '<div class="search-result-content">' +
                    '<div class="search-result-title">' + typeBadge + result.title + '</div>' +
                    (result.description ? '<div class="search-result-description">' + result.description + '</div>' : '') +
                    (result.price ? '<div class="search-result-price">' + result.price + '</div>' : '') +
                '</div>' +
            '</a>';
    });
    
    // Add appropriate "View all" links based on what types of results we have
    if (hasProducts && hasServices) {
        html += '<div class="dropdown-divider"></div>';
        html += '<a href="shop.php?search=' + encodeURIComponent(searchTerm) + '" class="dropdown-item text-center"><i class="fas fa-shopping-bag me-2"></i>View all products</a>';
        html += '<a href="services.php?search=' + encodeURIComponent(searchTerm) + '" class="dropdown-item text-center"><i class="fas fa-concierge-bell me-2"></i>View all services</a>';
    } else if (hasProducts) {
        html += '<a href="shop.php?search=' + encodeURIComponent(searchTerm) + '" class="dropdown-item text-center font-weight-bold">View all products</a>';
    } else if (hasServices) {
        html += '<a href="services.php?search=' + encodeURIComponent(searchTerm) + '" class="dropdown-item text-center font-weight-bold">View all services</a>';
    }
    
    searchResults.innerHTML = html;
    searchResults.style.display = 'block';
}

function hideSearchResults(dropdownId) {
    const searchResults = document.getElementById(dropdownId);
    if (searchResults) {
        searchResults.style.display = 'none';
    }
}
</script> 