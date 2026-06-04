<?php
$pageTitle = 'Reports & Analytics';
require_once 'header.php';

// Fetch report data
$totalUsersQuery = "SELECT COUNT(*) as count FROM users";
$totalUsers = $conn->query($totalUsersQuery)->fetch_assoc()['count'];

$totalProductsQuery = "SELECT COUNT(*) as count FROM products";
$totalProducts = $conn->query($totalProductsQuery)->fetch_assoc()['count'];

$totalOrdersQuery = "SELECT COUNT(*) as count FROM orders";
$totalOrders = $conn->query($totalOrdersQuery)->fetch_assoc()['count'];

$totalRevenueQuery = "SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'";
$totalRevenue = $conn->query($totalRevenueQuery)->fetch_assoc()['total'] ?? 0;

// Top sellers
$topSellersQuery = "SELECT u.username, COUNT(o.order_id) as order_count, SUM(o.total_amount) as total_sales 
                    FROM users u 
                    JOIN orders o ON u.user_id = o.seller_id 
                    WHERE o.status != 'cancelled'
                    GROUP BY u.user_id 
                    ORDER BY total_sales DESC 
                    LIMIT 5";
$topSellers = $conn->query($topSellersQuery);

// Top categories
$topCategoriesQuery = "SELECT c.category_name, COUNT(p.product_id) as product_count 
                       FROM categories c 
                       LEFT JOIN products p ON c.category_id = p.category_id 
                       GROUP BY c.category_id 
                       ORDER BY product_count DESC 
                       LIMIT 5";
$topCategories = $conn->query($topCategoriesQuery);

// Monthly sales
$monthlySalesQuery = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_amount) as total 
                     FROM orders 
                     WHERE status != 'cancelled' 
                     GROUP BY month 
                     ORDER BY month DESC 
                     LIMIT 6";
$monthlySales = $conn->query($monthlySalesQuery);
?>

<!-- Overview Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><?php echo $totalUsers; ?></h3>
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
                    <h3><?php echo $totalProducts; ?></h3>
                    <p>Total Products</p>
                </div>
                <i class="bi bi-box-seam stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-warning text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><?php echo $totalOrders; ?></h3>
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

<!-- Reports -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top Sellers</h5>
            </div>
            <div class="card-body">
                <?php if ($topSellers->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Seller</th>
                                    <th>Orders</th>
                                    <th>Total Sales</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($seller = $topSellers->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($seller['username']); ?></td>
                                        <td><?php echo $seller['order_count']; ?></td>
                                        <td>R<?php echo number_format($seller['total_sales'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No sales data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top Categories</h5>
            </div>
            <div class="card-body">
                <?php if ($topCategories->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Products</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($category = $topCategories->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                        <td><?php echo $category['product_count']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No category data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Monthly Sales</h5>
            </div>
            <div class="card-body">
                <?php if ($monthlySales->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($month = $monthlySales->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($month['month']); ?></td>
                                        <td>R<?php echo number_format($month['total'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No monthly sales data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
