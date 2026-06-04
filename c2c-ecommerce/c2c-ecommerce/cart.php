<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch cart items
$query = "SELECT c.*, p.title, p.price, p.image_url, p.quantity as available_quantity, u.username
          FROM cart c
          JOIN products p ON c.product_id = p.product_id
          JOIN users u ON p.seller_id = u.user_id
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total
$total = 0;
?>

<!-- Page Header -->
<section class="py-4 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Shopping Cart</li>
            </ol>
        </nav>
        <h1>Shopping Cart</h1>
    </div>
</section>

<!-- Cart Section -->
<section class="py-5">
    <div class="container">
        <?php if ($result->num_rows > 0): ?>
            <div class="row">
                <div class="col-lg-8">
                    <?php while ($item = $result->fetch_assoc()): ?>
                        <?php
                        $itemTotal = $item['price'] * $item['quantity'];
                        $total += $itemTotal;
                        ?>
                        <div class="cart-item">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <?php if ($item['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                    <?php else: ?>
                                        <img src="assets/images/placeholder.jpg" class="img-fluid rounded" alt="Placeholder">
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-5">
                                    <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                                    <p class="text-muted mb-0">Seller: <?php echo htmlspecialchars($item['username']); ?></p>
                                    <p class="text-muted">Price: R<?php echo number_format($item['price'], 2); ?></p>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="number" class="form-control" 
                                               value="<?php echo $item['quantity']; ?>" 
                                               min="1" 
                                               max="<?php echo $item['available_quantity']; ?>"
                                               onchange="updateCartQuantity(<?php echo $item['product_id']; ?>, this.value)">
                                    </div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <h5>R<?php echo number_format($itemTotal, 2); ?></h5>
                                    <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(<?php echo $item['product_id']; ?>)">
                                        <i class="bi bi-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>R<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping</span>
                                <span>R50.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total</strong>
                                <strong>R<?php echo number_format($total + 50, 2); ?></strong>
                            </div>
                            <a href="checkout.php" class="btn btn-primary w-100">Proceed to Checkout</a>
                            <a href="products.php" class="btn btn-outline-secondary w-100 mt-2">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-cart-x display-1"></i>
                <h4 class="mt-3">Your cart is empty</h4>
                <p>Start shopping to add items to your cart</p>
                <a href="products.php" class="btn btn-primary">Browse Products</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
