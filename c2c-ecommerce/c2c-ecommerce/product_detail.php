<?php
require_once 'includes/header.php';

// Get product ID
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($productId <= 0) {
    header('Location: products.php');
    exit;
}

// Fetch product details
$query = "SELECT p.*, u.username, u.email, u.phone, c.category_name 
          FROM products p 
          JOIN users u ON p.seller_id = u.user_id 
          JOIN categories c ON p.category_id = c.category_id 
          WHERE p.product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header('Location: products.php');
    exit;
}

// Fetch product reviews
$reviewQuery = "SELECT r.*, u.username 
                FROM reviews r 
                JOIN users u ON r.user_id = u.user_id 
                WHERE r.product_id = ? 
                ORDER BY r.created_at DESC";
$reviewStmt = $conn->prepare($reviewQuery);
$reviewStmt->bind_param("i", $productId);
$reviewStmt->execute();
$reviewResult = $reviewStmt->get_result();
?>

<!-- Page Header -->
<section class="py-4 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="products.php">Products</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['title']); ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Product Detail Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-6 mb-4">
                <?php if ($product['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="img-fluid product-detail-image w-100" alt="<?php echo htmlspecialchars($product['title']); ?>">
                <?php else: ?>
                    <img src="assets/images/placeholder.jpg" class="img-fluid product-detail-image w-100" alt="Placeholder">
                <?php endif; ?>
            </div>
            
            <!-- Product Info -->
            <div class="col-lg-6">
                <h1 class="mb-3"><?php echo htmlspecialchars($product['title']); ?></h1>
                <p class="product-price mb-3">R<?php echo number_format($product['price'], 2); ?></p>
                
                <div class="mb-3">
                    <span class="badge bg-info"><?php echo htmlspecialchars($product['category_name']); ?></span>
                    <span class="badge bg-secondary"><?php echo htmlspecialchars($product['condition_status']); ?></span>
                    <span class="badge <?php echo $product['status'] == 'available' ? 'bg-success' : 'bg-danger'; ?>">
                        <?php echo htmlspecialchars($product['status']); ?>
                    </span>
                </div>
                
                <div class="mb-4">
                    <h5>Description</h5>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
                
                <div class="mb-4">
                    <h5>Seller Information</h5>
                    <p><strong>Seller:</strong> <?php echo htmlspecialchars($product['username']); ?></p>
                    <p><strong>Quantity Available:</strong> <?php echo $product['quantity']; ?></p>
                    <p><strong>Listed:</strong> <?php echo date('F j, Y', strtotime($product['created_at'])); ?></p>
                </div>
                
                <?php if ($product['status'] == 'available' && $product['quantity'] > 0): ?>
                    <?php if ($isLoggedIn): ?>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-lg add-to-cart" 
                                    data-product-id="<?php echo $product['product_id']; ?>"
                                    data-quantity="1">
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </button>
                            <a href="messages.php?product_id=<?php echo $product['product_id']; ?>&user_id=<?php echo $product['seller_id']; ?>" 
                               class="btn btn-outline-secondary">
                                <i class="bi bi-chat-dots"></i> Contact Seller
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Please <a href="login.php">login</a> to purchase this item.
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> This item is currently unavailable.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Reviews Section -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Reviews</h3>
                
                <?php if ($isLoggedIn): ?>
                    <div class="card mb-4">
                        <div class="card-header">Write a Review</div>
                        <div class="card-body">
                            <form action="api/add_review.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Rating</label>
                                    <select class="form-select" name="rating" required>
                                        <option value="">Select Rating</option>
                                        <option value="5">5 Stars - Excellent</option>
                                        <option value="4">4 Stars - Good</option>
                                        <option value="3">3 Stars - Average</option>
                                        <option value="2">2 Stars - Poor</option>
                                        <option value="1">1 Star - Very Poor</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Comment</label>
                                    <textarea class="form-control" name="comment" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Review</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($reviewResult->num_rows > 0): ?>
                    <?php while ($review = $reviewResult->fetch_assoc()): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h5 class="card-title"><?php echo htmlspecialchars($review['username']); ?></h5>
                                    <small class="text-muted"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></small>
                                </div>
                                <div class="mb-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $review['rating']): ?>
                                            <i class="bi bi-star-fill text-warning"></i>
                                        <?php else: ?>
                                            <i class="bi bi-star text-warning"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
