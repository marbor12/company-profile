<?php
session_start();
// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Include database connection
require_once 'config/database.php';

// Get database connection
$pdo = getDBConnection();

// Get review ID from URL
$review_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$review_id) {
    header('Location: reviews.php?error=invalid_id');
    exit();
}

// Get review details with event information
$stmt = $pdo->prepare("
    SELECT r.*, e.title as event_title, e.date as event_date, e.venue as event_venue
    FROM review r 
    LEFT JOIN event e ON r.id_event = e.id 
    WHERE r.id = ?
");
$stmt->execute([$review_id]);
$review = $stmt->fetch();

if (!$review) {
    header('Location: reviews.php?error=review_not_found');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Review - Admin idSpora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../styles.css" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">
    <style>
        .admin-container {
            background: #ffffff;
            border-radius: 20px;
            margin: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            min-height: calc(100vh - 40px);
        }
        
        .admin-header {
            background: linear-gradient(135deg, #ffffff 0%, #fff9e6 100%);
            color: var(--dark-blue);
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .admin-nav {
            background: #ffffff;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .admin-nav .nav-link {
            color: var(--dark-blue) !important;
            margin: 0 15px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .admin-nav .nav-link:hover {
            color: var(--primary-yellow) !important;
        }
        
        .admin-nav .nav-link.active {
            color: var(--primary-yellow) !important;
            font-weight: 700;
        }
        
        .admin-content {
            padding: 30px;
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .btn-back {
            background: var(--dark-blue);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: #2c3e50;
            color: white;
            transform: translateY(-2px);
        }
        
        .review-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .review-header {
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        
        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .reviewer-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-orange) 0%, #FF8C42 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .reviewer-details h3 {
            color: var(--dark-blue);
            margin-bottom: 5px;
        }
        
        .job-title {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        
        .rating-display {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .rating-stars {
            color: #f39c12;
            font-size: 1.5rem;
        }
        
        .rating-number {
            background: #e3f2fd;
            color: #1976d2;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .review-content {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            border-left: 4px solid var(--primary-orange);
            margin: 20px 0;
        }
        
        .review-text {
            font-size: 1.1rem;
            line-height: 1.7;
            color: #495057;
            margin: 0;
        }
        
        .event-info {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
        }
        
        .event-title {
            color: #1976d2;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .event-details {
            color: #666;
            font-size: 1rem;
        }
        
        .review-meta {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
        }
        
        .meta-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .meta-item:last-child {
            border-bottom: none;
        }
        
        .meta-label {
            font-weight: 600;
            color: var(--dark-blue);
        }
        
        .meta-value {
            color: #666;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .status-active {
            background: #e8f5e8;
            color: #27ae60;
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: #c0392b;
            color: white;
        }
        
        .actions-section {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .btn-edit {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-edit:hover {
            background: #2980b9;
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-delete {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-delete:hover {
            background: #c0392b;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Admin Header -->
        <div class="admin-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1><i class="fas fa-eye"></i> View Review</h1>
                        <p class="mb-0">Detail lengkap review dari <?php echo htmlspecialchars($review['name']); ?></p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="logout.php" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Navigation -->
        <nav class="admin-nav">
            <div class="container">
                <ul class="navbar-nav flex-row">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="events.php">
                            <i class="fas fa-calendar-alt"></i> Events
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mentors.php">
                            <i class="fas fa-users"></i> Mentors
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="news.php">
                            <i class="fas fa-newspaper"></i> News
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="reviews.php">
                            <i class="fas fa-star"></i> Reviews
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pending_reviews.php">
                            <i class="fas fa-clock"></i> Pending Reviews
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="documentation.php">
                            <i class="fas fa-images"></i> Documentation
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Admin Content -->
        <div class="admin-content">
            <div class="container">
                <div class="content-header">
                    <h2>Review Details</h2>
                    <a href="reviews.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Back to Reviews
                    </a>
                </div>

                <div class="review-card">
                    <!-- Review Header -->
                    <div class="review-header">
                        <div class="reviewer-info">
                            <div class="reviewer-avatar">
                                <?php echo strtoupper(substr($review['name'], 0, 1)); ?>
                            </div>
                            <div class="reviewer-details">
                                <h3><?php echo htmlspecialchars($review['name']); ?></h3>
                                <div class="job-title"><?php echo htmlspecialchars($review['job_title']); ?></div>
                                <div class="rating-display">
                                    <div class="rating-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?php echo $i <= $review['rate'] ? '' : '-o'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="rating-number"><?php echo $review['rate']; ?>/5</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Review Content -->
                    <div class="review-content">
                        <p class="review-text"><?php echo nl2br(htmlspecialchars($review['review'])); ?></p>
                    </div>

                    <!-- Event Information -->
                    <?php if ($review['event_title']): ?>
                    <div class="event-info">
                        <div class="event-title">
                            <i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($review['event_title']); ?>
                        </div>
                        <div class="event-details">
                            <strong>Date:</strong> <?php echo date('d F Y', strtotime($review['event_date'])); ?><br>
                            <strong>Venue:</strong> <?php echo htmlspecialchars($review['event_venue'] ?? 'N/A'); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Review Metadata -->
                    <div class="review-meta">
                        <div class="meta-item">
                            <span class="meta-label">Review ID:</span>
                            <span class="meta-value">#<?php echo $review['id']; ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Status:</span>
                            <span class="status-badge status-active"><?php echo ucfirst($review['status'] ?? 'active'); ?></span>
                        </div>
                        <?php if ($review['approved_at']): ?>
                        <div class="meta-item">
                            <span class="meta-label">Approved At:</span>
                            <span class="meta-value"><?php echo date('d F Y H:i', strtotime($review['approved_at'])); ?></span>
                        </div>
                        <?php endif; ?>

                    </div>

                    <!-- Actions -->
                    <div class="actions-section">
                        <a href="edit_review.php?id=<?php echo $review['id']; ?>" class="btn-edit">
                            <i class="fas fa-edit"></i> Edit Review
                        </a>
                        <a href="reviews.php?delete=<?php echo $review['id']; ?>" 
                           class="btn-delete"
                           onclick="return confirm('Are you sure you want to delete this review?')">
                            <i class="fas fa-trash"></i> Delete Review
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <footer class="admin-footer">
        <a class="brand" href="index.php">
            <img src="../property/logo idspora_nobg_outlined.png" alt="idSpora" />
            <span>idSpora Admin</span>
        </a>
        <div class="small">© <?php echo date('Y'); ?> idSpora</div>
    </footer>
</body>
</html>
