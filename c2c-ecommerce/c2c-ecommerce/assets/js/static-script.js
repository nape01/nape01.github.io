// Sample product data for static version
const products = [
    {
        id: 1,
        title: "iPhone 13 Pro Max",
        description: "Latest iPhone with excellent condition. Comes with original charger and box.",
        price: 15999,
        category: "Electronics",
        condition: "used",
        image: "https://via.placeholder.com/300x300?text=iPhone+13",
        seller: "John D."
    },
    {
        id: 2,
        title: "Nike Air Jordan 1",
        description: "Brand new Nike Air Jordan 1 sneakers, size 10. Never worn.",
        price: 3500,
        category: "Clothing",
        condition: "new",
        image: "https://via.placeholder.com/300x300?text=Nike+Jordan",
        seller: "Sarah M."
    },
    {
        id: 3,
        title: "Gaming Laptop",
        description: "High-performance gaming laptop with RTX 3060, 16GB RAM, 512GB SSD.",
        price: 12500,
        category: "Electronics",
        condition: "refurbished",
        image: "https://via.placeholder.com/300x300?text=Gaming+Laptop",
        seller: "Tech Store"
    },
    {
        id: 4,
        title: "Mountain Bike",
        description: "Professional mountain bike, excellent for trails. Well maintained.",
        price: 4500,
        category: "Sports",
        condition: "used",
        image: "https://via.placeholder.com/300x300?text=Mountain+Bike",
        seller: "Mike R."
    },
    {
        id: 5,
        title: "Designer Handbag",
        description: "Authentic designer handbag, gently used. Certificate of authenticity included.",
        price: 2800,
        category: "Clothing",
        condition: "used",
        image: "https://via.placeholder.com/300x300?text=Handbag",
        seller: "Emma W."
    },
    {
        id: 6,
        title: "Smart TV 55 inch",
        description: "4K Smart TV with streaming apps. Perfect condition, 1 year old.",
        price: 6999,
        category: "Electronics",
        condition: "used",
        image: "https://via.placeholder.com/300x300?text=Smart+TV",
        seller: "David K."
    },
    {
        id: 7,
        title: "Garden Tool Set",
        description: "Complete garden tool set including shovel, rake, pruning shears, and more.",
        price: 850,
        category: "Home & Garden",
        condition: "new",
        image: "https://via.placeholder.com/300x300?text=Garden+Tools",
        seller: "Green Thumb"
    },
    {
        id: 8,
        title: "Textbooks Bundle",
        description: "University textbooks for business studies. All in good condition.",
        price: 1200,
        category: "Books",
        condition: "used",
        image: "https://via.placeholder.com/300x300?text=Textbooks",
        seller: "Student Books"
    }
];

// Cart functionality using localStorage
let cart = JSON.parse(localStorage.getItem('nbConnectCart')) || [];

// Update cart count badge
function updateCartCount() {
    const count = cart.reduce((total, item) => total + item.quantity, 0);
    const badges = document.querySelectorAll('#cart-count');
    badges.forEach(badge => {
        badge.textContent = count;
    });
}

// Add item to cart
function addToCart(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    const existingItem = cart.find(item => item.id === productId);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: product.id,
            title: product.title,
            price: product.price,
            image: product.image,
            quantity: 1
        });
    }

    localStorage.setItem('nbConnectCart', JSON.stringify(cart));
    updateCartCount();
    alert('Item added to cart!');
}

// Remove item from cart
function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    localStorage.setItem('nbConnectCart', JSON.stringify(cart));
    updateCartCount();
    renderCart();
}

// Update cart item quantity
function updateCartQuantity(productId, change) {
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(productId);
            return;
        }
    }
    localStorage.setItem('nbConnectCart', JSON.stringify(cart));
    updateCartCount();
    renderCart();
}

// Render cart items
function renderCart() {
    const cartItemsContainer = document.getElementById('cart-items');
    const emptyCart = document.getElementById('empty-cart');
    const subtotal = document.getElementById('subtotal');
    const total = document.getElementById('total');

    if (!cartItemsContainer) return;

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '';
        emptyCart.style.display = 'block';
        subtotal.textContent = 'R0.00';
        total.textContent = 'R50.00';
        return;
    }

    emptyCart.style.display = 'none';
    
    let html = '';
    let subtotalAmount = 0;

    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        subtotalAmount += itemTotal;
        
        html += `
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <img src="${item.image}" class="img-fluid rounded" alt="${item.title}">
                        </div>
                        <div class="col-6">
                            <h5 class="card-title">${item.title}</h5>
                            <p class="card-text text-muted">R${item.price.toFixed(2)}</p>
                        </div>
                        <div class="col-3 text-end">
                            <div class="input-group input-group-sm mb-2">
                                <button class="btn btn-outline-secondary" onclick="updateCartQuantity(${item.id}, -1)">-</button>
                                <input type="text" class="form-control text-center" value="${item.quantity}" readonly>
                                <button class="btn btn-outline-secondary" onclick="updateCartQuantity(${item.id}, 1)">+</button>
                            </div>
                            <button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    cartItemsContainer.innerHTML = html;
    subtotal.textContent = `R${subtotalAmount.toFixed(2)}`;
    total.textContent = `R${(subtotalAmount + 50).toFixed(2)}`;
}

// Render products grid
function renderProducts(productsToRender) {
    const productsGrid = document.getElementById('products-grid');
    const featuredProducts = document.getElementById('featured-products');
    
    if (!productsGrid && !featuredProducts) return;

    const html = productsToRender.map(product => `
        <div class="col-md-4 col-sm-6">
            <div class="card h-100">
                <img src="${product.image}" class="card-img-top" alt="${product.title}" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">${product.title}</h5>
                    <p class="card-text text-muted small">${product.category} • ${product.condition}</p>
                    <p class="card-text">R${product.price.toLocaleString()}</p>
                    <button class="btn btn-primary w-100" onclick="addToCart(${product.id})">
                        <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                </div>
            </div>
        </div>
    `).join('');

    if (productsGrid) {
        productsGrid.innerHTML = html;
    }
    
    if (featuredProducts) {
        featuredProducts.innerHTML = html;
    }
}

// Apply filters
function applyFilters() {
    const categoryFilter = document.getElementById('category-filter')?.value || '';
    const priceFilter = document.getElementById('price-filter')?.value || '';
    const conditionFilter = document.getElementById('condition-filter')?.value || '';

    let filteredProducts = [...products];

    if (categoryFilter) {
        filteredProducts = filteredProducts.filter(p => p.category === categoryFilter);
    }

    if (priceFilter) {
        if (priceFilter === '0-500') {
            filteredProducts = filteredProducts.filter(p => p.price <= 500);
        } else if (priceFilter === '500-1000') {
            filteredProducts = filteredProducts.filter(p => p.price > 500 && p.price <= 1000);
        } else if (priceFilter === '1000-5000') {
            filteredProducts = filteredProducts.filter(p => p.price > 1000 && p.price <= 5000);
        } else if (priceFilter === '5000+') {
            filteredProducts = filteredProducts.filter(p => p.price > 5000);
        }
    }

    if (conditionFilter) {
        filteredProducts = filteredProducts.filter(p => p.condition === conditionFilter);
    }

    renderProducts(filteredProducts);
}

// Form handling
document.addEventListener('DOMContentLoaded', function() {
    // Update cart count on page load
    updateCartCount();

    // Render products on products page
    if (document.getElementById('products-grid') || document.getElementById('featured-products')) {
        renderProducts(products);
    }

    // Render cart on cart page
    if (document.getElementById('cart-items')) {
        renderCart();
    }

    // Contact form handling
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Thank you for your message! This is a demo - no email will be sent.');
            contactForm.reset();
        });
    }

    // Login form handling
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Login successful! This is a demo - no actual authentication.');
            window.location.href = 'static-index.html';
        });
    }

    // Register form handling
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Registration successful! This is a demo - no account will be created.');
            window.location.href = 'static-login.html';
        });
    }
});
