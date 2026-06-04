<?php
$pageTitle = 'Order Management';
require_once 'header.php';

// Handle order actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $orderId = intval($_GET['id']);
    
    if ($action == 'update_status' && isset($_GET['status'])) {
        $status = $_GET['status'];
        $updateQuery = "UPDATE orders SET status = ? WHERE order_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("si", $status, $orderId);
        $updateStmt->execute();
        header('Location: orders.php?updated=1');
        exit;
    }
}

// Fetch orders with status filter
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$query = "SELECT o.*, b.username as buyer_name, s.username as seller_name 
          FROM orders o 
          JOIN users b ON o.buyer_id = b.user_id 
          JOIN users s ON o.seller_id = s.user_id";
if ($statusFilter) {
    $query .= " WHERE o.status = ?";
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
        <form action="orders.php" method="GET" class="row g-3">
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="pending" <?php echo $statusFilter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo $statusFilter == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="shipped" <?php echo $statusFilter == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                    <option value="delivered" <?php echo $statusFilter == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="cancelled" <?php echo $statusFilter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Order status updated successfully!</div>
<?php endif; ?>

<!-- Orders Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Orders</h5>
    </div>
    <div class="card-body">
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Buyer</th>
                            <th>Seller</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['seller_name']); ?></td>
                                <td>R<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <span class="badge <?php 
                                        echo $order['status'] == 'delivered' ? 'bg-success' : 
                                            ($order['status'] == 'cancelled' ? 'bg-danger' : 'bg-warning'); 
                                    ?>">
                                        <?php echo htmlspecialchars($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Update Status
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="orders.php?action=update_status&id=<?php echo $order['order_id']; ?>&status=pending">Pending</a></li>
                                            <li><a class="dropdown-item" href="orders.php?action=update_status&id=<?php echo $order['order_id']; ?>&status=confirmed">Confirmed</a></li>
                                            <li><a class="dropdown-item" href="orders.php?action=update_status&id=<?php echo $order['order_id']; ?>&status=shipped">Shipped</a></li>
                                            <li><a class="dropdown-item" href="orders.php?action=update_status&id=<?php echo $order['order_id']; ?>&status=delivered">Delivered</a></li>
                                            <li><a class="dropdown-item" href="orders.php?action=update_status&id=<?php echo $order['order_id']; ?>&status=cancelled">Cancelled</a></li>
                                        </ul>
                                    </div>
                                    <a href="order_detail.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">No orders found.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>
