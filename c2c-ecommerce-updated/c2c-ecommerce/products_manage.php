<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!$isLoggedIn) {
    header('Location: auth.php?action=login');
    exit;
}

$userId = $_SESSION['user_id'];
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// Fetch categories (needed for sell and edit)
$categoryQuery = "SELECT * FROM categories ORDER BY category_name";
$categories = $conn->query($categoryQuery);

// List products
if ($action == 'list') {
    $query = "SELECT p.*, c.category_name, 
              (SELECT COUNT(*) FROM order_items oi JOIN orders o ON oi.order_id = o.order_id WHERE oi.product_id = p.product_id) as sales_count
              FROM products p 
              JOIN categories c ON p.category_id = c.category_id 
              WHERE p.seller_id = ? 
              ORDER BY p.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Sell new product
if ($action == 'sell' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $categoryId = intval($_POST['category_id']);
    $condition = $_POST['condition'];
    
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = 'assets/images/products/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $image_url = $targetFilePath;
        }
    }
    
    $query = "INSERT INTO products (seller_id, category_id, title, description, price, quantity, image_url, condition_status, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'available')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisdisss", $userId, $categoryId, $title, $description, $price, $quantity, $image_url, $condition);
    
    if ($stmt->execute()) {
        header('Location: products_manage.php?action=list&success=1');
        exit;
    } else {
        $error = "Failed to list product. Please try again.";
    }
}

// Edit product
if ($action == 'edit') {
    $productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    $query = "SELECT p.*, c.category_name 
              FROM products p 
              JOIN categories c ON p.category_id = c.category_id 
              WHERE p.product_id = ? AND p.seller_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $productId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        header('Location: products_manage.php?action=list');
        exit;
    }
    
    $product = $result->fetch_assoc();
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
        $quantity = intval($_POST['quantity']);
        $categoryId = intval($_POST['category_id']);
        $condition = $_POST['condition'];
        $status = $_POST['status'];
        
        $image_url = $product['image_url'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = 'assets/images/products/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetFilePath = $targetDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $image_url = $targetFilePath;
            }
        }
        
        $updateQuery = "UPDATE products SET category_id = ?, title = ?, description = ?, price = ?, quantity = ?, image_url = ?, condition_status = ?, status = ? WHERE product_id = ? AND seller_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("isdissssii", $categoryId, $title, $description, $price, $quantity, $image_url, $condition, $status, $productId, $userId);
        
        if ($updateStmt->execute()) {
            header('Location: products_manage.php?action=list&updated=1');
            exit;
        } else {
            $error = "Failed to update product. Please try again.";
        }
    }
}

// Delete product
if ($action == 'delete') {
    $productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    $query = "SELECT * FROM products WHERE product_id = ? AND seller_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $productId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        header('Location: products_manage.php?action=list');
        exit;
    }
    
    $deleteQuery = "DELETE FROM products WHERE product_id = ? AND seller_id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("ii", $productId, $userId);
    
    if ($deleteStmt->execute()) {
        header('Location: products_manage.php?action=list&deleted=1');
        exit;
    } else {
        header('Location: products_manage.php?action=list&error=delete_failed');
        exit;
    }
}
?>

<!-- Page Header -->
<section class="py-4 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <?php if ($action == 'list'): ?>
                    <li class="breadcrumb-item active">My Products</li>
                <?php elseif ($action == 'sell'): ?>
                    <li class="breadcrumb-item"><a href="products_manage.php?action=list">My Products</a></li>
                    <li class="breadcrumb-item active">Sell Item</li>
                <?php elseif ($action == 'edit'): ?>
                    <li class="breadcrumb-item"><a href="products_manage.php?action=list">My Products</a></li>
                    <li class="breadcrumb-item active">Edit Product</li>
                <?php endif; ?>
            </ol>
        </nav>
        <?php if ($action == 'list'): ?>
            <div class="d-flex justify-content-between align-items-center">
                <h1>My Products</h1>
                <a href="products_manage.php?action=sell" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> List New Item
                </a>
            </div>
        <?php elseif ($action == 'sell'): ?>
            <h1>List an Item for Sale</h1>
        <?php elseif ($action == 'edit'): ?>
            <h1>Edit Product</h1>
        <?php endif; ?>
    </div>
</section>

<!-- Products Management Section -->
<section class="py-5">
    <div class="container">
        <?php if ($action == 'list'): ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> Product listed successfully!
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['updated'])): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> Product updated successfully!
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> Product deleted successfully!
                </div>
            <?php endif; ?>
            
            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Sales</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($product = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php if ($product['image_url']): ?>
                                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="img-thumbnail" style="max-width: 80px;" alt="">
                                        <?php else: ?>
                                            <img src="assets/images/placeholder.jpg" class="img-thumbnail" style="max-width: 80px;" alt="">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars(substr($product['title'], 0, 40)); ?>...</td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td>R<?php echo number_format($product['price'], 2); ?></td>
                                    <td><?php echo $product['quantity']; ?></td>
                                    <td>
                                        <span class="badge <?php echo $product['status'] == 'available' ? 'bg-success' : ($product['status'] == 'sold' ? 'bg-danger' : 'bg-warning'); ?>">
                                            <?php echo htmlspecialchars($product['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $product['sales_count']; ?></td>
                                    <td>
                                        <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="products_manage.php?action=edit&id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete('products_manage.php?action=delete&id=<?php echo $product['product_id']; ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-inbox display-1"></i>
                    <h4 class="mt-3">No products listed yet</h4>
                    <p>Start selling by listing your first item</p>
                    <a href="products_manage.php?action=sell" class="btn btn-primary">List Your First Item</a>
                </div>
            <?php endif; ?>
        <?php elseif ($action == 'sell'): ?>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger">
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>
                            
                            <form action="products_manage.php?action=sell" method="POST" enctype="multipart/form-data" data-validate>
                                <div class="mb-3">
                                    <label class="form-label">Product Title</label>
                                    <input type="text" class="form-control" name="title" required 
                                           value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <select class="form-select" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php while ($category = $categories->fetch_assoc()): ?>
                                            <option value="<?php echo $category['category_id']; ?>"
                                                    <?php echo isset($_POST['category_id']) && $_POST['category_id'] == $category['category_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['category_name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="5" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Price (R)</label>
                                        <input type="number" class="form-control" name="price" step="0.01" min="0" required 
                                               value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" class="form-control" name="quantity" min="1" required 
                                               value="<?php echo isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : '1'; ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Condition</label>
                                    <select class="form-select" name="condition" required>
                                        <option value="new" <?php echo isset($_POST['condition']) && $_POST['condition'] == 'new' ? 'selected' : ''; ?>>New</option>
                                        <option value="used" <?php echo isset($_POST['condition']) && $_POST['condition'] == 'used' ? 'selected' : ''; ?>>Used</option>
                                        <option value="refurbished" <?php echo isset($_POST['condition']) && $_POST['condition'] == 'refurbished' ? 'selected' : ''; ?>>Refurbished</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Product Image</label>
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                    <small class="text-muted">Upload a clear image of your product</small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">List Item</button>
                                <a href="products_manage.php?action=list" class="btn btn-outline-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($action == 'edit'): ?>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger">
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>
                            
                            <form action="products_manage.php?action=edit&id=<?php echo $productId; ?>" method="POST" enctype="multipart/form-data" data-validate>
                                <div class="mb-3">
                                    <label class="form-label">Product Title</label>
                                    <input type="text" class="form-control" name="title" required 
                                           value="<?php echo htmlspecialchars($product['title']); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <select class="form-select" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php 
                                        $categories->data_seek(0);
                                        while ($category = $categories->fetch_assoc()): 
                                        ?>
                                            <option value="<?php echo $category['category_id']; ?>"
                                                    <?php echo $product['category_id'] == $category['category_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category['category_name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Price (R)</label>
                                        <input type="number" class="form-control" name="price" step="0.01" min="0" required 
                                               value="<?php echo $product['price']; ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Quantity</label>
                                        <input type="number" class="form-control" name="quantity" min="1" required 
                                               value="<?php echo $product['quantity']; ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Condition</label>
                                        <select class="form-select" name="condition" required>
                                            <option value="new" <?php echo $product['condition_status'] == 'new' ? 'selected' : ''; ?>>New</option>
                                            <option value="used" <?php echo $product['condition_status'] == 'used' ? 'selected' : ''; ?>>Used</option>
                                            <option value="refurbished" <?php echo $product['condition_status'] == 'refurbished' ? 'selected' : ''; ?>>Refurbished</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status" required>
                                            <option value="available" <?php echo $product['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                                            <option value="sold" <?php echo $product['status'] == 'sold' ? 'selected' : ''; ?>>Sold</option>
                                            <option value="reserved" <?php echo $product['status'] == 'reserved' ? 'selected' : ''; ?>>Reserved</option>
                                            <option value="inactive" <?php echo $product['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Product Image</label>
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                    <?php if ($product['image_url']): ?>
                                        <div class="mt-2">
                                            <small class="text-muted">Current image:</small><br>
                                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="img-thumbnail" style="max-width: 200px;" alt="Current image">
                                        </div>
                                    <?php endif; ?>
                                    <small class="text-muted">Leave empty to keep current image</small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Update Product</button>
                                <a href="products_manage.php?action=list" class="btn btn-outline-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
