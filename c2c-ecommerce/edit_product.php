<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details
$query = "SELECT p.*, c.category_name 
          FROM products p 
          JOIN categories c ON p.category_id = c.category_id 
          WHERE p.product_id = ? AND p.seller_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $productId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: my_products.php');
    exit;
}

$product = $result->fetch_assoc();

// Fetch categories
$categoryQuery = "SELECT * FROM categories ORDER BY category_name";
$categories = $conn->query($categoryQuery);

// Process product update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $categoryId = intval($_POST['category_id']);
    $condition = $_POST['condition'];
    $status = $_POST['status'];
    
    // Handle image upload
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
    
    // Update product
    $updateQuery = "UPDATE products SET category_id = ?, title = ?, description = ?, price = ?, quantity = ?, image_url = ?, condition_status = ?, status = ? WHERE product_id = ? AND seller_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("isdissssii", $categoryId, $title, $description, $price, $quantity, $image_url, $condition, $status, $productId, $userId);
    
    if ($updateStmt->execute()) {
        header('Location: my_products.php?updated=1');
        exit;
    } else {
        $error = "Failed to update product. Please try again.";
    }
}
?>

<!-- Page Header -->
<section class="py-4 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="my_products.php">My Products</a></li>
                <li class="breadcrumb-item active">Edit Product</li>
            </ol>
        </nav>
        <h1>Edit Product</h1>
    </div>
</section>

<!-- Edit Product Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="edit_product.php?id=<?php echo $productId; ?>" method="POST" enctype="multipart/form-data" data-validate>
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
                            <a href="my_products.php" class="btn btn-outline-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
