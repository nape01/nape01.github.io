<?php
require_once 'includes/header.php';

// Fetch featured products
$featuredQuery = "SELECT p.*, u.username, c.category_name 
                 FROM products p 
                 JOIN users u ON p.seller_id = u.user_id 
                 JOIN categories c ON p.category_id = c.category_id 
                 WHERE p.status = 'available' 
                 ORDER BY p.created_at DESC 
                 LIMIT 8";
$featuredResult = $conn->query($featuredQuery);

// Fetch categories
$categoryQuery = "SELECT * FROM categories ORDER BY category_name";
$categories = $conn->query($categoryQuery);
?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1>Welcome to C2C Marketplace</h1>
        <p class="lead my-4">Buy and sell goods securely in South Africa</p>
        <a href="products.php" class="btn btn-light btn-lg">Browse Products</a>
        <?php if (!$isLoggedIn): ?>
            <a href="register.php" class="btn btn-outline-light btn-lg ms-2">Join Now</a>
        <?php else: ?>
            <a href="sell.php" class="btn btn-outline-light btn-lg ms-2">Sell Item</a>
        <?php endif; ?>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Browse by Category</h2>
        <div class="row">
            <?php while ($category = $categories->fetch_assoc()): ?>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="bi bi-box-seam display-4 text-primary mb-3"></i>
                            <h5 class="card-title"><?php echo htmlspecialchars($category['category_name']); ?></h5>
                            <a href="products.php?category=<?php echo $category['category_id']; ?>" class="btn btn-outline-primary btn-sm">View Items</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center mb-4">Featured Products</h2>
        <div class="row">
            <?php while ($product = $featuredResult->fetch_assoc()): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
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
            <?php endwhile; ?>
        </div>
        <div class="text-center mt-4">
            <a href="products.php" class="btn btn-outline-primary">View All Products</a>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">How It Works</h2>
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <div class="mb-3">
                    <i class="bi bi-person-plus display-1 text-primary"></i>
                </div>
                <h4>1. Register</h4>
                <p>Create your free account in minutes</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="mb-3">
                    <i class="bi bi-cart display-1 text-primary"></i>
                </div>
                <h4>2. Browse & Buy</h4>
                <p>Find great deals from local sellers</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="mb-3">
                    <i class="bi bi-cash-coin display-1 text-primary"></i>
                </div>
                <h4>3. Sell & Earn</h4>
                <p>List your items and reach thousands of buyers</p>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <h2 class="display-4">10K+</h2>
                <p>Active Users</p>
            </div>
            <div class="col-md-3 mb-4">
                <h2 class="display-4">5K+</h2>
                <p>Products Listed</p>
            </div>
            <div class="col-md-3 mb-4">
                <h2 class="display-4">2K+</h2>
                <p>Successful Transactions</p>
            </div>
            <div class="col-md-3 mb-4">
                <h2 class="display-4">98%</h2>
                <p>Customer Satisfaction</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
