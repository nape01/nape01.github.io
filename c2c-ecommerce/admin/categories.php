<?php
$pageTitle = 'Category Management';
require_once 'header.php';

// Handle category actions
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $categoryName = trim($_POST['category_name']);
        $description = trim($_POST['description']);
        
        $insertQuery = "INSERT INTO categories (category_name, description) VALUES (?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ss", $categoryName, $description);
        
        if ($insertStmt->execute()) {
            header('Location: categories.php?added=1');
            exit;
        }
    } elseif ($_POST['action'] == 'delete') {
        $categoryId = intval($_POST['category_id']);
        $deleteQuery = "DELETE FROM categories WHERE category_id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $categoryId);
        $deleteStmt->execute();
        header('Location: categories.php?deleted=1');
        exit;
    }
}

// Fetch categories
$query = "SELECT * FROM categories ORDER BY category_name";
$result = $conn->query($query);
?>

<?php if (isset($_GET['added'])): ?>
    <div class="alert alert-success">Category added successfully!</div>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">Category deleted successfully!</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Add Category</h5>
            </div>
            <div class="card-body">
                <form action="categories.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="category_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Categories</h5>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category Name</th>
                                    <th>Description</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($category = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $category['category_id']; ?></td>
                                        <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($category['description'] ?? '', 0, 50)); ?>...</td>
                                        <td><?php echo date('M j, Y', strtotime($category['created_at'])); ?></td>
                                        <td>
                                            <form action="categories.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No categories found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
