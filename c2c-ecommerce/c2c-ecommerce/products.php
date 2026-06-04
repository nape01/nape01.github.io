<?php
require_once 'includes/header.php';

// Get filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$priceRange = isset($_GET['price']) ? $_GET['price'] : '';
$condition = isset($_GET['condition']) ? $_GET['condition'] : '';

// Build query
$query = "SELECT p.*, u.username, c.category_name 
          FROM products p 
          JOIN users u ON p.seller_id = u.user_id 
          JOIN categories c ON p.category_id = c.category_id 
          WHERE p.status = 'available'";

$params = [];
$types = '';

if ($search) {
    $query .= " AND (p.title LIKE ? OR p.description LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'ss';
}

if ($category) {
    $query .= " AND p.category_id = ?";
    $params[] = $category;
    $types .= 'i';
}

if ($condition) {
    $query .= " AND p.condition_status = ?";
    $params[] = $condition;
    $types .= 's';
}

if ($priceRange) {
    switch ($priceRange) {
        case '0-100':
            $query .= " AND p.price BETWEEN 0 AND 100";
            break;
        case '100-500':
            $query .= " AND p.price BETWEEN 100 AND 500";
            break;
        case '500-1000':
            $query .= " AND p.price BETWEEN 500 AND 1000";
            break;
        case '1000+':
            $query .= " AND p.price > 1000";
            break;
    }
}

$query .= " ORDER BY p.created_at DESC";

// Execute query
if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

// Get categories for filter
$categoryQuery = "SELECT * FROM categories ORDER BY category_name";
$categories = $conn->query($categoryQuery);
?>

<!-- Page Header -->
<section class="py-4 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Products</li>
            </ol>
        </nav>
        <h1>Browse Products</h1>
    </div>
</section>

<!-- Products Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Filters</h5>
                    </div>
                    <div class="card-body">
                        <form action="products.php" method="GET">
                            <!-- Category Filter -->
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category" id="categoryFilter">
                                    <option value="">All Categories</option>
                                    <?php while ($cat = $categories->fetch_assoc()): ?>
                                        <option value="<?php echo $cat['category_id']; ?>" 
                                                <?php echo $category == $cat['category_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['category_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <!-- Price Filter -->
                            <div class="mb-3">
                                <label class="form-label">Price Range</label>
                                <select class="form-select" name="price" id="priceFilter">
                                    <option value="">All Prices</option>
                                    <option value="0-100" <?php echo $priceRange == '0-100' ? 'selected' : ''; ?>>R0 - R100</option>
                                    <option value="100-500" <?php echo $priceRange == '100-500' ? 'selected' : ''; ?>>R100 - R500</option>
                                    <option value="500-1000" <?php echo $priceRange == '500-1000' ? 'selected' : ''; ?>>R500 - R1000</option>
                                    <option value="1000+" <?php echo $priceRange == '1000+' ? 'selected' : ''; ?>>R1000+</option>
                                </select>
                            </div>
                            
                            <!-- Condition Filter -->
                            <div class="mb-3">
                                <label class="form-label">Condition</label>
                                <select class="form-select" name="condition" id="conditionFilter">
                                    <option value="">All Conditions</option>
                                    <option value="new" <?php echo $condition == 'new' ? 'selected' : ''; ?>>New</option>
                                    <option value="used" <?php echo $condition == 'used' ? 'selected' : ''; ?>>Used</option>
                                    <option value="refurbished" <?php echo $condition == 'refurbished' ? 'selected' : ''; ?>>Refurbished</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                            <a href="products.php" class="btn btn-outline-secondary w-100 mt-2">Clear Filters</a>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="col-lg-9">
                <?php if ($search): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Showing results for: <strong><?php echo htmlspecialchars($search); ?></strong>
                        <a href="products.php" class="float-end">Clear search</a>
                    </div>
                <?php endif; ?>
                
                <?php if ($result->num_rows > 0): ?>
                    <div class="row">
                        <?php while ($product = $result->fetch_assoc()): ?>
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
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        <i class="bi bi-exclamation-triangle"></i> No products found matching your criteria.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
