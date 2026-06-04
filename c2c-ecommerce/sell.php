<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch categories
$categoryQuery = "SELECT * FROM categories ORDER BY category_name";
$categories = $conn->query($categoryQuery);

// Process product listing
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $categoryId = intval($_POST['category_id']);
    $condition = $_POST['condition'];
    
    // Handle image upload
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
    
    // Insert product
    $query = "INSERT INTO products (seller_id, category_id, title, description, price, quantity, image_url, condition_status, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'available')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisdisss", $userId, $categoryId, $title, $description, $price, $quantity, $image_url, $condition);
    
    if ($stmt->execute()) {
        header('Location: my_products.php?success=1');
        exit;
    } else {
        $error = "Failed to list product. Please try again.";
    }
}
?>

<!-- Page Header -->
<section class="py-4 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Sell Item</li>
            </ol>
        </nav>
        <h1>List an Item for Sale</h1>
    </div>
</section>

<!-- Sell Item Section -->
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
                        
                        <form action="sell.php" method="POST" enctype="multipart/form-data" data-validate>
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
                            <a href="my_products.php" class="btn btn-outline-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
