<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!$isLoggedIn) {
    header('Location: auth.php?action=login');
    exit;
}

$userId = $_SESSION['user_id'];
$action = isset($_GET['action']) ? $_GET['action'] : 'cart';

// Cart functionality
if ($action == 'cart') {
    $query = "SELECT c.*, p.title, p.price, p.image_url, p.quantity as available_quantity, u.username
              FROM cart c
              JOIN products p ON c.product_id = p.product_id
              JOIN users u ON p.seller_id = u.user_id
              WHERE c.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = 0;
}

// Checkout functionality
if ($action == 'checkout') {
    $query = "SELECT c.*, p.title, p.price, p.seller_id, u.username
              FROM cart c
              JOIN products p ON c.product_id = p.product_id
              JOIN users u ON p.seller_id = u.user_id
              WHERE c.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $total = 0;
    $items = [];
    while ($item = $result->fetch_assoc()) {
        $itemTotal = $item['price'] * $item['quantity'];
        $total += $itemTotal;
        $items[] = $item;
    }
    
    if (empty($items)) {
        header('Location: shopping.php?action=cart');
        exit;
    }
    
    // Process order if form submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $shippingAddress = $_POST['shipping_address'];
        $totalAmount = $total + 50;
        
        $sellerOrders = [];
        foreach ($items as $item) {
            if (!isset($sellerOrders[$item['seller_id']])) {
                $sellerOrders[$item['seller_id']] = [
                    'items' => [],
                    'total' => 0
                ];
            }
            $sellerOrders[$item['seller_id']]['items'][] = $item;
            $sellerOrders[$item['seller_id']]['total'] += $item['price'] * $item['quantity'];
        }
        
        foreach ($sellerOrders as $sellerId => $orderData) {
            $orderQuery = "INSERT INTO orders (buyer_id, seller_id, total_amount, status, shipping_address) 
                           VALUES (?, ?, ?, 'pending', ?)";
            $orderStmt = $conn->prepare($orderQuery);
            $orderStmt->bind_param("iids", $userId, $sellerId, $orderData['total'], $shippingAddress);
            $orderStmt->execute();
            $orderId = $conn->insert_id;
            
            foreach ($orderData['items'] as $item) {
                $orderItemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                                  VALUES (?, ?, ?, ?)";
                $orderItemStmt = $conn->prepare($orderItemQuery);
                $orderItemStmt->bind_param("iiid", $orderId, $item['product_id'], $item['quantity'], $item['price']);
                $orderItemStmt->execute();
            }
        }
        
        $clearCartQuery = "DELETE FROM cart WHERE user_id = ?";
        $clearCartStmt = $conn->prepare($clearCartQuery);
        $clearCartStmt->bind_param("i", $userId);
        $clearCartStmt->execute();
        
        header('Location: account.php?action=orders&success=1');
        exit;
    }
    
    $userQuery = "SELECT address FROM users WHERE user_id = ?";
    $userStmt = $conn->prepare($userQuery);
    $userStmt->bind_param("i", $userId);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    $user = $userResult->fetch_assoc();
}
?>

<!-- Page Header -->
<section class="py-4 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <?php if ($action == 'cart'): ?>
                    <li class="breadcrumb-item active">Shopping Cart</li>
                <?php elseif ($action == 'checkout'): ?>
                    <li class="breadcrumb-item"><a href="shopping.php?action=cart">Cart</a></li>
                    <li class="breadcrumb-item active">Checkout</li>
                <?php endif; ?>
            </ol>
        </nav>
        <h1><?php echo $action == 'cart' ? 'Shopping Cart' : 'Checkout'; ?></h1>
    </div>
</section>

<!-- Shopping Section -->
<section class="py-5">
    <div class="container">
        <?php if ($action == 'cart'): ?>
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
                                <a href="shopping.php?action=checkout" class="btn btn-primary w-100">Proceed to Checkout</a>
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
        <?php elseif ($action == 'checkout'): ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Shipping Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="shopping.php?action=checkout" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Shipping Address</label>
                                    <textarea class="form-control" name="shipping_address" rows="4" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Place Order</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Items</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($items as $item): ?>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($item['title']); ?></h6>
                                        <small class="text-muted">Seller: <?php echo htmlspecialchars($item['username']); ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-secondary">x<?php echo $item['quantity']; ?></span>
                                        <span class="ms-2">R<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
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
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Orders will be processed per seller.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
