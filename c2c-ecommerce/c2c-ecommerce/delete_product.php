<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verify product belongs to user
$query = "SELECT * FROM products WHERE product_id = ? AND seller_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $productId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: my_products.php');
    exit;
}

// Delete product
$deleteQuery = "DELETE FROM products WHERE product_id = ? AND seller_id = ?";
$deleteStmt = $conn->prepare($deleteQuery);
$deleteStmt->bind_param("ii", $productId, $userId);

if ($deleteStmt->execute()) {
    header('Location: my_products.php?deleted=1');
    exit;
} else {
    header('Location: my_products.php?error=delete_failed');
    exit;
}
?>
