<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!$isLoggedIn) {
    header('Location: auth.php?action=login');
    exit;
}

$userId = $_SESSION['user_id'];
$action = isset($_GET['action']) ? $_GET['action'] : 'profile';

// Profile functionality
if ($action == 'profile') {
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $fullName = trim($_POST['full_name']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        
        $updateQuery = "UPDATE users SET full_name = ?, phone = ?, address = ? WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("sssi", $fullName, $phone, $address, $userId);
        
        if ($updateStmt->execute()) {
            header('Location: account.php?action=profile&updated=1');
            exit;
        } else {
            $error = "Failed to update profile. Please try again.";
        }
    }
}

// Messages functionality
if ($action == 'messages') {
    $query = "SELECT m.*, u.username as sender_name, p.title as product_title
              FROM messages m
              JOIN users u ON m.sender_id = u.user_id
              LEFT JOIN products p ON m.product_id = p.product_id
              WHERE m.receiver_id = ?
              ORDER BY m.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $receiverId = intval($_POST['receiver_id']);
        $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
        $subject = trim($_POST['subject']);
        $message = trim($_POST['message']);
        
        $insertQuery = "INSERT INTO messages (sender_id, receiver_id, product_id, subject, message) 
                        VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("iiiss", $userId, $receiverId, $productId, $subject, $message);
        
        if ($insertStmt->execute()) {
            header('Location: account.php?action=messages&sent=1');
            exit;
        } else {
            $error = "Failed to send message. Please try again.";
        }
    }
}

// Orders functionality
if ($action == 'orders') {
    $query = "SELECT o.*, u.username as seller_name 
              FROM orders o 
              JOIN users u ON o.seller_id = u.user_id 
              WHERE o.buyer_id = ? 
              ORDER BY o.created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!-- Page Header -->
<section class="py-4 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <?php if ($action == 'profile'): ?>
                    <li class="breadcrumb-item active">My Profile</li>
                <?php elseif ($action == 'messages'): ?>
                    <li class="breadcrumb-item active">Messages</li>
                <?php elseif ($action == 'orders'): ?>
                    <li class="breadcrumb-item active">My Orders</li>
                <?php endif; ?>
            </ol>
        </nav>
        <?php if ($action == 'profile'): ?>
            <h1>My Profile</h1>
        <?php elseif ($action == 'messages'): ?>
            <h1>Messages</h1>
        <?php elseif ($action == 'orders'): ?>
            <h1>My Orders</h1>
        <?php endif; ?>
    </div>
</section>

<!-- Account Section -->
<section class="py-5">
    <div class="container">
        <?php if ($action == 'profile'): ?>
            <?php if (isset($_GET['updated'])): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> Profile updated successfully!
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-person-circle display-1 text-primary mb-3"></i>
                            <h4><?php echo htmlspecialchars($user['full_name']); ?></h4>
                            <p class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
                            <span class="badge bg-primary"><?php echo htmlspecialchars($user['role']); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Profile Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="account.php?action=profile" method="POST" data-validate>
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="full_name" required 
                                           value="<?php echo htmlspecialchars($user['full_name']); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" name="phone" 
                                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Account Status</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['status']); ?>" disabled>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($action == 'messages'): ?>
            <?php if (isset($_GET['sent'])): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> Message sent successfully!
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <div class="mb-4">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#composeModal">
                    <i class="bi bi-pencil-square"></i> Compose Message
                </button>
            </div>
            
            <?php if ($result->num_rows > 0): ?>
                <div class="row">
                    <?php while ($message = $result->fetch_assoc()): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card <?php echo $message['is_read'] ? '' : 'border-primary'; ?>">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <strong><?php echo htmlspecialchars($message['sender_name']); ?></strong>
                                    <small class="text-muted"><?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?></small>
                                </div>
                                <div class="card-body">
                                    <?php if ($message['subject']): ?>
                                        <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($message['subject']); ?></h6>
                                    <?php endif; ?>
                                    <?php if ($message['product_title']): ?>
                                        <p class="card-text">
                                            <small class="text-muted">Re: <?php echo htmlspecialchars($message['product_title']); ?></small>
                                        </p>
                                    <?php endif; ?>
                                    <p class="card-text"><?php echo nl2br(htmlspecialchars(substr($message['message'], 0, 150))); ?>...</p>
                                    <a href="view_message.php?id=<?php echo $message['message_id']; ?>" class="btn btn-sm btn-outline-primary">Read More</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-envelope display-1"></i>
                    <h4 class="mt-3">No messages yet</h4>
                    <p>Start communicating with other users</p>
                </div>
            <?php endif; ?>
            
            <div class="modal fade" id="composeModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Compose Message</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="account.php?action=messages" method="POST" data-validate>
                                <div class="mb-3">
                                    <label class="form-label">Recipient Username</label>
                                    <input type="text" class="form-control" name="receiver_username" id="receiverUsername" required>
                                    <input type="hidden" name="receiver_id" id="receiverId">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Subject</label>
                                    <input type="text" class="form-control" name="subject" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Message</label>
                                    <textarea class="form-control" name="message" rows="5" required></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Send Message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($action == 'orders'): ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> Order placed successfully!
                </div>
            <?php endif; ?>
            
            <?php if ($result->num_rows > 0): ?>
                <div class="row">
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <strong>Order #<?php echo $order['order_id']; ?></strong>
                                    <span class="badge <?php 
                                        echo $order['status'] == 'delivered' ? 'bg-success' : 
                                            ($order['status'] == 'cancelled' ? 'bg-danger' : 'bg-warning'); 
                                    ?>">
                                        <?php echo htmlspecialchars($order['status']); ?>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <p><strong>Seller:</strong> <?php echo htmlspecialchars($order['seller_name']); ?></p>
                                    <p><strong>Total:</strong> R<?php echo number_format($order['total_amount'], 2); ?></p>
                                    <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                                    <p><strong>Shipping Address:</strong> <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                                    <a href="order_detail.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-receipt display-1"></i>
                    <h4 class="mt-3">No orders yet</h4>
                    <p>Start shopping to place your first order</p>
                    <a href="products.php" class="btn btn-primary">Browse Products</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
