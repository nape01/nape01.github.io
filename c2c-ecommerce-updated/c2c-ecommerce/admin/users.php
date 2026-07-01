<?php
$pageTitle = 'User Management';
require_once 'header.php';

// Handle user actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $userId = intval($_GET['id']);
    
    if ($action == 'delete') {
        $deleteQuery = "DELETE FROM users WHERE user_id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $userId);
        $deleteStmt->execute();
        header('Location: users.php?deleted=1');
        exit;
    } elseif ($action == 'activate' || $action == 'suspend') {
        $status = $action == 'activate' ? 'active' : 'suspended';
        $updateQuery = "UPDATE users SET status = ? WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("si", $status, $userId);
        $updateStmt->execute();
        header('Location: users.php?updated=1');
        exit;
    }
}

// Fetch users with role filter
$roleFilter = isset($_GET['role']) ? $_GET['role'] : '';
$query = "SELECT * FROM users";
if ($roleFilter) {
    $query .= " WHERE role = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $roleFilter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}
?>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form action="users.php" method="GET" class="row g-3">
            <div class="col-md-3">
                <select class="form-select" name="role">
                    <option value="">All Roles</option>
                    <option value="customer" <?php echo $roleFilter == 'customer' ? 'selected' : ''; ?>>Customers</option>
                    <option value="seller" <?php echo $roleFilter == 'seller' ? 'selected' : ''; ?>>Sellers</option>
                    <option value="admin" <?php echo $roleFilter == 'admin' ? 'selected' : ''; ?>>Admins</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">User deleted successfully!</div>
<?php endif; ?>

<?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">User status updated successfully!</div>
<?php endif; ?>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Users</h5>
    </div>
    <div class="card-body">
        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Full Name</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['user_id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td>
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($user['role']); ?></span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $user['status'] == 'active' ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo htmlspecialchars($user['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if ($user['status'] == 'active'): ?>
                                        <a href="users.php?action=suspend&id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-warning">Suspend</a>
                                    <?php else: ?>
                                        <a href="users.php?action=activate&id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-success">Activate</a>
                                    <?php endif; ?>
                                    <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-info">Edit</a>
                                    <a href="users.php?action=delete&id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">No users found.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>
