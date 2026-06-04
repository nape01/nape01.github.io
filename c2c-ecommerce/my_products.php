<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user's products
$query = "SELECT p.*, c.category_name, 
          (SELECT COUNT(*) FROM order_items oi JOIN orders o ON oi.order_id = o.order_id WHERE oi.product_id = p.product_id) as sales_count
          FROM products p 
          JOIN categories c ON p.category_id = c.category_id 
          WHERE p.seller_id = ? 
          ORDER BY p.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Page Header -->
<section class="py-4 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">My Products</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <h1>My Products</h1>
            <a href="sell.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> List New Item
            </a>
        </div>
    </div>
</section>

<!-- My Products Section -->
<section class="py-5">
    <div class="container">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> Product listed successfully!
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> Product updated successfully!
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> Product deleted successfully!
            </div>
        <?php endif; ?>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Sales</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($product = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if ($product['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="img-thumbnail" style="max-width: 80px;" alt="">
                                    <?php else: ?>
                                        <img src="assets/images/placeholder.jpg" class="img-thumbnail" style="max-width: 80px;" alt="">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars(substr($product['title'], 0, 40)); ?>...</td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td>R<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['quantity']; ?></td>
                                <td>
                                    <span class="badge <?php echo $product['status'] == 'available' ? 'bg-success' : ($product['status'] == 'sold' ? 'bg-danger' : 'bg-warning'); ?>">
                                        <?php echo htmlspecialchars($product['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $product['sales_count']; ?></td>
                                <td>
                                    <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete('delete_product.php?id=<?php echo $product['product_id']; ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-inbox display-1"></i>
                <h4 class="mt-3">No products listed yet</h4>
                <p>Start selling by listing your first item</p>
                <a href="sell.php" class="btn btn-primary">List Your First Item</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
