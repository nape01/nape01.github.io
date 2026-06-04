<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user's orders (as buyer)
$query = "SELECT o.*, u.username as seller_name 
          FROM orders o 
          JOIN users u ON o.seller_id = u.user_id 
          WHERE o.buyer_id = ? 
          ORDER BY o.created_at DESC";
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
                <li class="breadcrumb-item active">My Orders</li>
            </ol>
        </nav>
        <h1>My Orders</h1>
    </div>
</section>

<!-- Orders Section -->
<section class="py-5">
    <div class="container">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> Order placed successfully!
            </div>
        <?php endif; ?>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="row">
                <?php while ($order = $result->fetch_assoc()): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong>Order #<?php echo $order['order_id']; ?></strong>
                                <span class="badge <?php 
                                    echo $order['status'] == 'delivered' ? 'bg-success' : 
                                        ($order['status'] == 'cancelled' ? 'bg-danger' : 'bg-warning'); 
                                ?>">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <p><strong>Seller:</strong> <?php echo htmlspecialchars($order['seller_name']); ?></p>
                                <p><strong>Total:</strong> R<?php echo number_format($order['total_amount'], 2); ?></p>
                                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                                <p><strong>Shipping Address:</strong> <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                                <a href="order_detail.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-receipt display-1"></i>
                <h4 class="mt-3">No orders yet</h4>
                <p>Start shopping to place your first order</p>
                <a href="products.php" class="btn btn-primary">Browse Products</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
