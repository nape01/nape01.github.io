<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch cart items
$query = "SELECT c.*, p.title, p.price, p.seller_id, u.username
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
$items = [];
while ($item = $result->fetch_assoc()) {
    $itemTotal = $item['price'] * $item['quantity'];
    $total += $itemTotal;
    $items[] = $item;
}

if (empty($items)) {
    header('Location: cart.php');
    exit;
}

// Process order if form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shippingAddress = $_POST['shipping_address'];
    $totalAmount = $total + 50; // Including shipping
    
    // Create order for each seller
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
    
    // Insert orders
    foreach ($sellerOrders as $sellerId => $orderData) {
        $orderQuery = "INSERT INTO orders (buyer_id, seller_id, total_amount, status, shipping_address) 
                       VALUES (?, ?, ?, 'pending', ?)";
        $orderStmt = $conn->prepare($orderQuery);
        $orderStmt->bind_param("iids", $userId, $sellerId, $orderData['total'], $shippingAddress);
        $orderStmt->execute();
        $orderId = $conn->insert_id;
        
        // Insert order items
        foreach ($orderData['items'] as $item) {
            $orderItemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                              VALUES (?, ?, ?, ?)";
            $orderItemStmt = $conn->prepare($orderItemQuery);
            $orderItemStmt->bind_param("iiid", $orderId, $item['product_id'], $item['quantity'], $item['price']);
            $orderItemStmt->execute();
        }
    }
    
    // Clear cart
    $clearCartQuery = "DELETE FROM cart WHERE user_id = ?";
    $clearCartStmt = $conn->prepare($clearCartQuery);
    $clearCartStmt->bind_param("i", $userId);
    $clearCartStmt->execute();
    
    header('Location: orders.php?success=1');
    exit;
}

// Fetch user address
$userQuery = "SELECT address FROM users WHERE user_id = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();
$user = $userResult->fetch_assoc();
?>

<!-- Page Header -->
<section class="py-4 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="cart.php">Cart</a></li>
                <li class="breadcrumb-item active">Checkout</li>
            </ol>
        </nav>
        <h1>Checkout</h1>
    </div>
</section>

<!-- Checkout Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="checkout.php" method="POST">
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
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
