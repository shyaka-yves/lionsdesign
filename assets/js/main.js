// Lions Design E-commerce Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const quantity = 1;
            
            addToCart(productId, quantity);
        });
    });

    // Quantity change handlers
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const cartId = this.dataset.cartId;
            const quantity = this.value;
            updateCartQuantity(cartId, quantity);
        });
    });

    // Remove from cart handlers
    document.querySelectorAll('.remove-from-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const cartId = this.dataset.cartId;
            removeFromCart(cartId);
        });
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function() {
            const query = this.value;
            if (query.length >= 2) {
                searchProducts(query);
            }
        }, 300));
    }

    // Category filter
    document.querySelectorAll('.category-filter').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.dataset.category;
            filterByCategory(category);
        });
    });
});

// Add to cart function
function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('action', 'add_to_cart');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch('ajax/cart_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Product added to cart successfully!', 'success');
            updateCartCount();
        } else {
            showAlert(data.message || 'Error adding product to cart', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error adding product to cart', 'danger');
    });
}

// Update cart quantity
function updateCartQuantity(cartId, quantity) {
    const formData = new FormData();
    formData.append('action', 'update_quantity');
    formData.append('cart_id', cartId);
    formData.append('quantity', quantity);

    fetch('ajax/cart_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart display first
            updateCartDisplay();
            updateCartCount();
            
            // Force update cart summary after a short delay
            setTimeout(() => {
                if (typeof updateCartSummary === 'function') {
                    updateCartSummary();
                }
            }, 100);
        } else {
            showAlert(data.message || 'Error updating cart', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error updating cart', 'danger');
    });
}

// Remove from cart
function removeFromCart(cartId) {
    if (!confirm('Are you sure you want to remove this item from cart?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'remove_from_cart');
    formData.append('cart_id', cartId);

    fetch('ajax/cart_actions.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart display first
            updateCartDisplay();
            updateCartCount();
            
            // Force update cart summary after a short delay
            setTimeout(() => {
                if (typeof updateCartSummary === 'function') {
                    updateCartSummary();
                }
            }, 100);
            
            showAlert('Item removed from cart', 'success');
        } else {
            showAlert(data.message || 'Error removing item', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error removing item', 'danger');
    });
}

// Update cart count
function updateCartCount() {
    fetch('ajax/get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = data.count;
            }
        })
        .catch(error => console.error('Error:', error));
}

// Update cart display
function updateCartDisplay() {
    const cartContainer = document.getElementById('cartItems');
    if (cartContainer) {
        fetch('ajax/get_cart.php')
            .then(response => response.text())
            .then(html => {
                cartContainer.innerHTML = html;
                // Reattach event listeners after updating cart
                reattachCartEventListeners();
                // Also update cart summary
                updateCartSummary();
            })
            .catch(error => console.error('Error:', error));
    }
}

// Reattach cart event listeners (simplified with event delegation)
function reattachCartEventListeners() {
    // Event delegation is handled in cart.php, so this function is simplified
    // Just update the cart summary after any changes
    updateCartSummary();
}

// Search products
function searchProducts(query) {
    const formData = new FormData();
    formData.append('action', 'search');
    formData.append('search_term', query);

    fetch('ajax/search.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displaySearchResults(data.results);

        }
    })
    .catch(error => console.error('Error:', error));
}

// Display search results
function displaySearchResults(products) {
    const resultsContainer = document.getElementById('searchResults');
    if (!resultsContainer) return;

    if (products.length === 0) {
        resultsContainer.innerHTML = '<p class="text-muted">No products found</p>';
        return;
    }

    let html = '<div class="row">';
    products.forEach(product => {
        html += `
            <div class="col-md-4 mb-3">
                <div class="card product-card">
                    <img src="${product.image}" class="card-img-top" alt="${product.title}">
                    <div class="card-body">
                        <h5 class="card-title">${product.title}</h5>
                        <p class="card-text">${product.description.substring(0, 100)}...</p>
                        <p class="price">${parseFloat(product.price).toFixed(2)} Rwf</p>
                        <button class="btn btn-success add-to-cart" data-product-id="${product.id}">Add to Cart</button>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    resultsContainer.innerHTML = html;
}

// Filter by category
function filterByCategory(category) {
    window.location.href = `shop.php?category=${category}`;
}

// Show alert message
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) {
        // Create alert container if it doesn't exist
        const container = document.createElement('div');
        container.id = 'alertContainer';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.getElementById('alertContainer').appendChild(alertDiv);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Debounce function
function debounce(func, wait, immediate) {
    var timeout;
    return function executedFunction() {
        var context = this;
        var args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    // Email validation
    const emailField = form.querySelector('input[type="email"]');
    if (emailField && emailField.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailField.value)) {
            emailField.classList.add('is-invalid');
            isValid = false;
        }
    }

    return isValid;
}

// Image preview for file uploads
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Confirm delete
function confirmDelete(message = 'Are you sure you want to delete this item?') {
    return confirm(message);
}

// Format price
function formatPrice(price) {
    return parseFloat(price).toFixed(2) + ' Rwf';
}

// Loading spinner
function showLoading(element) {
    element.innerHTML = '<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>';
}

function hideLoading(element, originalContent) {
    element.innerHTML = originalContent;
} 