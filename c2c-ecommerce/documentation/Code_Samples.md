# 2.4 Coding - Code Samples with Explanations - NB Connect Platform

## Sample PHP Code

### Database Connection (config/database.php)
```php
<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'c2c_ecommerce');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");
?>
```
**Purpose:** This file establishes a secure connection to the MySQL database using mysqli. It defines database credentials, creates the connection object, checks for connection errors, and sets the character encoding to utf8mb4 for proper handling of special characters and emojis. This file is included in all PHP pages that require database access.

### User Registration (register.php)
```php
// Insert user if no errors
if (empty($errors)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $insertQuery = "INSERT INTO users (username, email, password, full_name, phone, address, role, status) 
                   VALUES (?, ?, ?, ?, ?, ?, 'customer', 'active')";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("ssssss", $username, $email, $hashedPassword, $fullName, $phone, $address);
    
    if ($insertStmt->execute()) {
        header('Location: login.php?registered=1');
        exit;
    } else {
        $errors[] = "Registration failed. Please try again.";
    }
}
```
**Purpose:** This code handles user registration by securely hashing the password using PHP's built-in password_hash() function with the bcrypt algorithm (PASSWORD_DEFAULT). It uses prepared statements to prevent SQL injection attacks. The code inserts the new user into the database with a default role of 'customer' and status of 'active', then redirects to the login page upon success.

### Add to Cart API (api/add_to_cart.php)
```php
// Check if item already in cart
$cartQuery = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
$cartStmt = $conn->prepare($cartQuery);
$cartStmt->bind_param("ii", $userId, $productId);
$cartStmt->execute();
$cartResult = $cartStmt->get_result();

if ($cartResult->num_rows > 0) {
    // Update quantity
    $cartItem = $cartResult->fetch_assoc();
    $newQuantity = $cartItem['quantity'] + $quantity;
    
    if ($newQuantity > $product['quantity']) {
        echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
        exit;
    }
    
    $updateQuery = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ii", $newQuantity, $cartItem['cart_id']);
    $updateStmt->execute();
} else {
    // Add to cart
    $insertQuery = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("iii", $userId, $productId, $quantity);
    $insertStmt->execute();
}
```
**Purpose:** This API endpoint handles adding items to the shopping cart. It first checks if the item is already in the cart - if so, it updates the quantity; if not, it creates a new cart entry. The code includes stock validation to prevent ordering more items than available. It uses prepared statements for security and returns JSON responses for AJAX requests.

---

## Sample HTML Code

### Product Card (products.php)
```html
<div class="col-lg-4 col-md-6 mb-4">
    <div class="card product-card h-100">
        <?php if ($product['image_url']): ?>
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['title']); ?>">
        <?php else: ?>
            <img src="assets/images/placeholder.jpg" class="card-img-top" alt="Placeholder">
        <?php endif; ?>
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars(substr($product['title'], 0, 50)); ?>...</h5>
            <p class="card-text">
                <small class="text-muted">
                    <i class="bi bi-person"></i> <?php echo htmlspecialchars($product['username']); ?>
                </small>
            </p>
            <p class="product-price">R<?php echo number_format($product['price'], 2); ?></p>
            <p class="card-text">
                <span class="badge bg-info"><?php echo htmlspecialchars($product['category_name']); ?></span>
                <span class="badge bg-secondary"><?php echo htmlspecialchars($product['condition_status']); ?></span>
            </p>
            <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary w-100">View Details</a>
        </div>
    </div>
</div>
```
**Purpose:** This HTML code displays a product card in the products listing page. It uses Bootstrap card components for styling, displays the product image (with fallback to placeholder), shows truncated title, seller username, formatted price, category and condition badges, and a "View Details" button. The htmlspecialchars() function prevents XSS attacks by escaping user-generated content.

### Navigation Header (includes/header.php)
```html
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-shop"></i> C2C Marketplace
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products.php">Products</a>
                </li>
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="sell.php">Sell Item</a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <form class="d-flex search-bar me-3" action="products.php" method="GET">
                <div class="input-group">
                    <input class="form-control" type="search" name="search" placeholder="Search products..." aria-label="Search">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</nav>
```
**Purpose:** This HTML creates the main navigation bar using Bootstrap's navbar component. It includes the brand logo, responsive hamburger menu for mobile devices, navigation links that conditionally display based on login status, and a search form. The sticky-top class keeps the navigation visible while scrolling, improving user experience.

---

## Sample JavaScript Code

### Cart Functionality (assets/js/script.js)
```javascript
function addToCart(productId, quantity) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    
    fetch('api/add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount();
            showAlert('Product added to cart successfully!', 'success');
        } else {
            showAlert(data.message || 'Error adding product to cart', 'danger');
        }
    })
    .catch(error => {
        showAlert('Error adding product to cart', 'danger');
    });
}
```
**Purpose:** This JavaScript function handles adding products to the shopping cart asynchronously using the Fetch API. It creates a FormData object with the product ID and quantity, sends a POST request to the API endpoint, processes the JSON response, updates the cart count display, and shows appropriate success or error alerts to the user. This provides a seamless user experience without page reloads.

### Form Validation (assets/js/script.js)
```javascript
function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    // Email validation
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(field.value)) {
            isValid = false;
            field.classList.add('is-invalid');
        }
    });
    
    // Password confirmation
    const password = form.querySelector('input[name="password"]');
    const confirmPassword = form.querySelector('input[name="confirm_password"]');
    
    if (password && confirmPassword && password.value !== confirmPassword.value) {
        isValid = false;
        confirmPassword.classList.add('is-invalid');
        showAlert('Passwords do not match', 'danger');
    }
    
    return isValid;
}
```
**Purpose:** This JavaScript function performs client-side form validation before submission. It checks that all required fields are filled, validates email format using regex, and ensures password confirmation matches the original password. It adds Bootstrap's is-invalid class to invalid fields for visual feedback and displays alert messages for validation errors. This improves user experience by catching errors before server submission.

---

## Sample CSS Code

### Product Card Styling (assets/css/style.css)
```css
.product-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border: none;
    border-radius: 10px;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

.product-card img {
    height: 200px;
    object-fit: cover;
}

.product-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
}
```
**Purpose:** This CSS styles the product cards with smooth hover effects. The transition property creates smooth animations for transform and box-shadow changes. On hover, the card lifts slightly (translateY) and gains a deeper shadow, providing visual feedback to users. The image is set to a fixed height with object-fit: cover to ensure consistent card dimensions while maintaining aspect ratio. The price is styled prominently with the primary theme color.

### Responsive Design (assets/css/style.css)
```css
@media (max-width: 768px) {
    .hero-section h1 {
        font-size: 2rem;
    }
    
    .hero-section {
        padding: 40px 0;
    }
    
    .product-card img {
        height: 150px;
    }
}

@media (max-width: 576px) {
    .navbar-brand {
        font-size: 1.2rem;
    }
    
    .hero-section h1 {
        font-size: 1.5rem;
    }
}
```
**Purpose:** This CSS implements responsive design using media queries to adapt the layout for different screen sizes. At 768px (tablet breakpoint), it reduces the hero section heading size and padding, and shrinks product card images. At 576px (mobile breakpoint), it further reduces the navbar brand size and hero heading. This ensures the platform provides an optimal user experience across smartphones, tablets, and desktop devices.

### Admin Sidebar Styling (assets/css/style.css)
```css
.admin-sidebar {
    background-color: var(--dark-color);
    min-height: 100vh;
    color: white;
}

.admin-sidebar .nav-link {
    color: rgba(255,255,255,0.8);
    padding: 15px 20px;
}

.admin-sidebar .nav-link:hover,
.admin-sidebar .nav-link.active {
    background-color: var(--primary-color);
    color: white;
}
```
**Purpose:** This CSS styles the admin dashboard sidebar with a dark background that spans the full viewport height. Navigation links have semi-transparent white text for better readability against the dark background. On hover or when active, links highlight with the primary theme color and solid white text, providing clear visual feedback about the current page. This creates a professional, modern admin interface that's easy to navigate.

---

## Sample MySQL Table Structure

### Users Table
```sql
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('customer', 'seller', 'admin') DEFAULT 'customer',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```
**Purpose:** This table stores user account information with unique constraints on username and email to prevent duplicates. The password field stores bcrypt-hashed passwords for security. The role ENUM implements RBAC by restricting users to specific roles (customer, seller, admin). The status field allows administrators to control account access. Timestamp fields automatically track creation and modification times.

### Products Table
```sql
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT DEFAULT 1,
    image_url VARCHAR(500),
    condition_status ENUM('new', 'used', 'refurbished') DEFAULT 'used',
    status ENUM('available', 'sold', 'reserved', 'inactive') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
);
```
**Purpose:** This table stores product listings with foreign key relationships to users (seller) and categories. The price field uses DECIMAL(10,2) for accurate monetary values. The condition_status ENUM classifies product condition, while the status field tracks availability. Foreign key constraints with CASCADE DELETE ensure referential integrity - when a user or category is deleted, associated products are automatically removed.

### Orders Table
```sql
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(user_id) ON DELETE CASCADE
);
```
**Purpose:** This table stores order information linking buyers and sellers through foreign key relationships. The status ENUM tracks the order lifecycle from pending through delivery or cancellation. The shipping_address field stores delivery information. Timestamps track when orders were created and last updated. Cascade delete ensures order records are removed when associated users are deleted.
