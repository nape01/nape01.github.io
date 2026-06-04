<?php
require_once 'includes/header.php';

// Check if user is logged in
if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch messages
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

// Process new message
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
        header('Location: messages.php?sent=1');
        exit;
    } else {
        $error = "Failed to send message. Please try again.";
    }
}
?>

<!-- Page Header -->
<section class="py-4 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Messages</li>
            </ol>
        </nav>
        <h1>Messages</h1>
    </div>
</section>

<!-- Messages Section -->
<section class="py-5">
    <div class="container">
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
        
        <!-- Compose Message Button -->
        <div class="mb-4">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#composeModal">
                <i class="bi bi-pencil-square"></i> Compose Message
            </button>
        </div>
        
        <!-- Messages List -->
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
    </div>
</section>

<!-- Compose Message Modal -->
<div class="modal fade" id="composeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Compose Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="messages.php" method="POST" data-validate>
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

<?php require_once 'includes/footer.php'; ?>
