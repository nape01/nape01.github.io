<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit;
}

$userId = $_SESSION['user_id'];
$productId = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);

if ($quantity < 1) {
    $quantity = 1;
}

// Check if product exists and is available
$productQuery = "SELECT * FROM products WHERE product_id = ? AND status = 'available'";
$productStmt = $conn->prepare($productQuery);
$productStmt->bind_param("i", $productId);
$productStmt->execute();
$productResult = $productStmt->get_result();

if ($productResult->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Product not available']);
    exit;
}

$product = $productResult->fetch_assoc();

// Check if quantity is available
if ($product['quantity'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
    exit;
}

// Check if item already in cart
$cartQuery = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
$cartStmt = $conn->prepare($cartQuery);
$cartStmt->bind_param("ii", $userId, $productId);
$cartStmt->execute();
$cartResult = $cartStmt->get_result();

if ($cartResult->num_rows > 0) {
    // Update quantity
    $cartItem = $cartResult->fetch_assoc();
    $newQuantity = $cartItem['quantity'] + $quantity;
    
    if ($newQuantity > $product['quantity']) {
        echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
        exit;
    }
    
    $updateQuery = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ii", $newQuantity, $cartItem['cart_id']);
    $updateStmt->execute();
} else {
    // Add to cart
    $insertQuery = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("iii", $userId, $productId, $quantity);
    $insertStmt->execute();
}

echo json_encode(['success' => true, 'message' => 'Product added to cart']);
?>
