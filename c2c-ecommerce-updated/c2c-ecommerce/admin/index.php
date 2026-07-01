<?php
$pageTitle = 'Dashboard';
require_once 'header.php';

// Fetch statistics
$userCountQuery = "SELECT COUNT(*) as count FROM users";
$userCountResult = $conn->query($userCountQuery);
$userCount = $userCountResult->fetch_assoc()['count'];

$productCountQuery = "SELECT COUNT(*) as count FROM products WHERE status = 'available'";
$productCountResult = $conn->query($productCountQuery);
$productCount = $productCountResult->fetch_assoc()['count'];

$orderCountQuery = "SELECT COUNT(*) as count FROM orders WHERE status != 'cancelled'";
$orderCountResult = $conn->query($orderCountQuery);
$orderCount = $orderCountResult->fetch_assoc()['count'];

$totalRevenueQuery = "SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'";
$totalRevenueResult = $conn->query($totalRevenueQuery);
$totalRevenue = $totalRevenueResult->fetch_assoc()['total'] ?? 0;

// Recent orders
$recentOrdersQuery = "SELECT o.*, b.username as buyer_name, s.username as seller_name 
                      FROM orders o 
                      JOIN users b ON o.buyer_id = b.user_id 
                      JOIN users s ON o.seller_id = s.user_id 
                      ORDER BY o.created_at DESC LIMIT 5";
$recentOrders = $conn->query($recentOrdersQuery);

// Recent users
$recentUsersQuery = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
$recentUsers = $conn->query($recentUsersQuery);
?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><?php echo $userCount; ?></h3>
                    <p>Total Users</p>
                </div>
                <i class="bi bi-people stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><?php echo $productCount; ?></h3>
                    <p>Active Products</p>
                </div>
                <i class="bi bi-box-seam stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-warning text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><?php echo $orderCount; ?></h3>
                    <p>Total Orders</p>
                </div>
                <i class="bi bi-receipt stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3>R<?php echo number_format($totalRevenue, 2); ?></h3>
                    <p>Total Revenue</p>
                </div>
                <i class="bi bi-currency-dollar stat-icon"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Orders</h5>
            </div>
            <div class="card-body">
                <?php if ($recentOrders->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Buyer</th>
                                    <th>Seller</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = $recentOrders->fetch_assoc()): ?>
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
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No recent orders</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Users</h5>
            </div>
            <div class="card-body">
                <?php if ($recentUsers->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = $recentUsers->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                                        <td>
                                            <span class="badge <?php 
                                                echo $user['status'] == 'active' ? 'bg-success' : 'bg-danger'; 
                                            ?>">
                                                <?php echo htmlspecialchars($user['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No recent users</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
