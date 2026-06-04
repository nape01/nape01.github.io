<?php
$pageTitle = 'Product Management';
require_once 'header.php';

// Handle product actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $productId = intval($_GET['id']);
    
    if ($action == 'delete') {
        $deleteQuery = "DELETE FROM products WHERE product_id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $productId);
        $deleteStmt->execute();
        header('Location: products.php?deleted=1');
        exit;
    } elseif ($action == 'approve' || $action == 'reject') {
        $status = $action == 'approve' ? 'available' : 'inactive';
        $updateQuery = "UPDATE products SET status = ? WHERE product_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("si", $status, $productId);
        $updateStmt->execute();
        header('Location: products.php?updated=1');
        exit;
    }
}

// Fetch products with status filter
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$query = "SELECT p.*, u.username as seller_name, c.category_name 
          FROM products p 
          JOIN users u ON p.seller_id = u.user_id 
          JOIN categories c ON p.category_id = c.category_id";
if ($statusFilter) {
    $query .= " WHERE p.status = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $statusFilter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}
?>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form action="products.php" method="GET" class="row g-3">
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="available" <?php echo $statusFilter == 'available' ? 'selected' : ''; ?>>Available</option>
                    <option value="sold" <?php echo $statusFilter == 'sold' ? 'selected' : ''; ?>>Sold</option>
                    <option value="reserved" <?php echo $statusFilter == 'reserved' ? 'selected' : ''; ?>>Reserved</option>
                    <option value="inactive" <?php echo $statusFilter == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">Product deleted successfully!</div>
<?php endif; ?>

<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Product status updated successfully!</div>
<?php endif; ?>

<!-- Products Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Products</h5>
    </div>
    <div class="card-body">
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Seller</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($product = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $product['product_id']; ?></td>
                                <td><?php echo htmlspecialchars(substr($product['title'], 0, 30)); ?>...</td>
                                <td><?php echo htmlspecialchars($product['seller_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td>R<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['quantity']; ?></td>
                                <td>
                                    <span class="badge <?php 
                                        echo $product['status'] == 'available' ? 'bg-success' : 
                                            ($product['status'] == 'sold' ? 'bg-danger' : 'bg-warning'); 
                                    ?>">
                                        <?php echo htmlspecialchars($product['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($product['created_at'])); ?></td>
                                <td>
                                    <?php if ($product['status'] != 'available'): ?>
                                        <a href="products.php?action=approve&id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-success">Approve</a>
                                    <?php endif; ?>
                                    <a href="../product_detail.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-info">View</a>
                                    <a href="products.php?action=delete&id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">No products found.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>
