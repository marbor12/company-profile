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

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM review WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: reviews.php?success=deleted');
        exit();
    } catch(PDOException $e) {
        header('Location: reviews.php?error=delete_failed');
        exit();
    }
}

// Get all approved reviews with event information
$stmt = $pdo->query("
    SELECT r.*, e.title as event_title 
    FROM review r 
    LEFT JOIN event e ON r.id_event = e.id 
    WHERE r.status = 'active'
    ORDER BY r.approved_at DESC
");
$reviews = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - Admin idSpora</title>
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
            margin-bottom: 35px;
            padding: 25px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 15px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .content-header h2 {
            color: var(--dark-blue);
            font-weight: 700;
            margin: 0;
            font-size: 1.8rem;
            text-shadow: 0 1px 2px rgba(44, 62, 80, 0.1);
        }
        
        .btn-add {
            background: var(--primary-orange);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-add:hover {
            background: var(--dark-blue);
            color: white;
            transform: translateY(-2px);
        }
        
        .table-container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
            border: 1px solid #f0f0f0;
        }
        
        .table {
            margin-bottom: 0;
            min-width: 1000px;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table th {
            border: none;
            background: linear-gradient(135deg, var(--dark-blue) 0%, #2c3e50 100%);
            color: white;
            font-weight: 600;
            white-space: nowrap;
            padding: 20px 15px;
            vertical-align: middle;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }
        
        .table th:first-child {
            border-top-left-radius: 12px;
        }
        
        .table th:last-child {
            border-top-right-radius: 12px;
        }
        
        .table td {
            padding: 20px 15px;
            vertical-align: middle;
            border: none;
            border-bottom: 1px solid #f0f0f0;
            background: white;
            transition: all 0.3s ease;
        }
        
        .table tbody tr {
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .table tbody tr:hover td {
            background: #f8f9fa;
        }
        
        /* Column Widths */
        .table th:nth-child(1), .table td:nth-child(1) { width: 15%; } /* Reviewer */
        .table th:nth-child(2), .table td:nth-child(2) { width: 12%; } /* Job Title */
        .table th:nth-child(3), .table td:nth-child(3) { width: 10%; } /* Rating */
        .table th:nth-child(4), .table td:nth-child(4) { width: 35%; } /* Review */
        .table th:nth-child(5), .table td:nth-child(5) { width: 15%; } /* Event */
        .table th:nth-child(6), .table td:nth-child(6) { width: 13%; } /* Actions */
        
        /* Reviewer Column */
        .table td:nth-child(1) {
            text-align: center;
        }
        
        .reviewer-name {
            font-weight: 700;
            color: var(--dark-blue);
            font-size: 1.05rem;
            text-shadow: 0 1px 2px rgba(44, 62, 80, 0.1);
            letter-spacing: 0.3px;
        }
        
        /* Job Title Column */
        .table td:nth-child(2) {
            text-align: center;
        }
        
        .job-title {
            color: #495057;
            font-size: 0.85rem;
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 6px 12px;
            border-radius: 15px;
            display: inline-block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            border: 1px solid #90caf9;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(33, 150, 243, 0.1);
        }
        
        /* Rating Column */
        .table td:nth-child(3) {
            text-align: center;
        }
        
        .rating-stars {
            color: #f39c12;
            font-size: 1.1rem;
            margin-bottom: 8px;
            text-shadow: 0 1px 2px rgba(243, 156, 18, 0.3);
        }
        
        .rating-stars .fa-star-o {
            color: #ddd;
            text-shadow: none;
        }
        
        .rating-number {
            font-size: 0.85rem;
            color: #666;
            font-weight: 700;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 4px 8px;
            border-radius: 12px;
            border: 1px solid #dee2e6;
        }
        
        /* Review Content Column */
        .table td:nth-child(4) {
            max-width: 0;
        }
        
        .review-content {
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            line-height: 1.4;
            color: #495057;
            font-size: 0.9rem;
            position: relative;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 16px;
            border-radius: 12px;
            border-left: 4px solid var(--primary-orange);
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .review-content:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }
        
        .review-content::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 50px;
            height: 25px;
            background: linear-gradient(to right, transparent, #ffffff);
            pointer-events: none;
            border-radius: 0 12px 0 0;
        }
        
        .review-preview {
            position: relative;
        }
        
        .review-full {
            display: none;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            border-left: 3px solid var(--primary-orange);
        }
        
        .review-full.show {
            display: block;
        }
        
        .toggle-review {
            background: linear-gradient(135deg, var(--primary-orange) 0%, #FF8C42 100%);
            border: none;
            color: white;
            font-size: 0.8rem;
            cursor: pointer;
            padding: 8px 16px;
            margin-top: 10px;
            border-radius: 20px;
            transition: all 0.3s ease;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .toggle-review::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .toggle-review:hover::before {
            left: 100%;
        }
        
        .toggle-review:hover {
            background: linear-gradient(135deg, var(--dark-blue) 0%, #2c3e50 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(44, 62, 80, 0.4);
        }
        
        .toggle-review.hide-btn {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3);
        }
        
        .toggle-review.hide-btn:hover {
            background: linear-gradient(135deg, #5a6268 0%, #495057 100%);
            box-shadow: 0 4px 15px rgba(90, 98, 104, 0.4);
        }
        
        /* Event Column */
        .table td:nth-child(5) {
            text-align: center;
        }
        
        .event-title {
            background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
            color: #2e7d32;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            border: 1px solid #81c784;
            box-shadow: 0 2px 4px rgba(76, 175, 80, 0.1);
        }
        
        /* Actions Column */
        .table td:nth-child(6) {
            text-align: center;
        }
        
        .actions-container {
            display: flex;
            flex-direction: column;
            gap: 5px;
            align-items: center;
        }
        
        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .btn-action::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-action:hover::before {
            left: 100%;
        }
        
        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        .btn-view {
            background: linear-gradient(135deg, var(--primary-orange) 0%, #FF8C42 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(255, 140, 66, 0.3);
        }
        
        .btn-view:hover {
            background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(230, 126, 34, 0.4);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.3);
        }
        
        .btn-delete:hover {
            background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(192, 57, 43, 0.4);
        }
        
        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .table {
                min-width: 800px;
            }
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
        
        .alert {
            border-radius: 15px;
            border: none;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            font-weight: 500;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-left: 5px solid #28a745;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-left: 5px solid #dc3545;
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
                        <h1><i class="fas fa-star"></i> Manage Reviews</h1>
                        <p class="mb-0">Kelola semua review idSpora</p>
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
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> 
                        <?php 
                        if ($_GET['success'] === 'deleted') {
                            echo 'Review berhasil dihapus!';
                        } elseif ($_GET['success'] === 'pending_approval') {
                            echo 'Review berhasil dikirim dan sedang menunggu persetujuan admin!';
                        } else {
                            echo 'Operasi berhasil!';
                        }
                        ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Terjadi kesalahan saat menghapus review.
                    </div>
                <?php endif; ?>

                <div class="content-header">
                    <h2>Daftar Reviews</h2>
                                         <div class="d-flex gap-2">
                         <a href="pending_reviews.php" class="btn-add" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);">
                             <i class="fas fa-clock"></i> Pending Reviews
                         </a>
                     </div>
                </div>

                <div class="table-container">
                    <?php if ($reviews): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Reviewer</th>
                                        <th>Job Title</th>
                                        <th>Rating</th>
                                        <th>Review</th>
                                        <th>Event</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reviews as $review): ?>
                                        <tr>
                                            <td>
                                                <span class="reviewer-name"><?php echo htmlspecialchars($review['name']); ?></span>
                                            </td>
                                            <td>
                                                <span class="job-title"><?php echo htmlspecialchars($review['job_title']); ?></span>
                                            </td>
                                            <td>
                                                <div class="rating-stars">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star<?php echo $i <= $review['rate'] ? '' : '-o'; ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                                <div class="rating-number">(<?php echo $review['rate']; ?>/5)</div>
                                            </td>
                                            <td>
                                                <div class="review-preview" id="review-preview-<?php echo $review['id']; ?>">
                                                    <div class="review-content">
                                                        <?php echo htmlspecialchars($review['review']); ?>
                                                    </div>
                                                    <button type="button" class="toggle-review" onclick="toggleReview(<?php echo $review['id']; ?>)">
                                                        Lihat Selengkapnya
                                                    </button>
                                                </div>
                                                <div class="review-full" id="review-full-<?php echo $review['id']; ?>">
                                                    <?php echo nl2br(htmlspecialchars($review['review'])); ?>
                                                    <button type="button" class="toggle-review hide-btn" onclick="toggleReview(<?php echo $review['id']; ?>)">
                                                        Sembunyikan
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="event-title">
                                                    <?php echo htmlspecialchars($review['event_title'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="actions-container">
                                                    <a href="view_review.php?id=<?php echo $review['id']; ?>" class="btn-action btn-view" title="View Review">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="reviews.php?delete=<?php echo $review['id']; ?>" 
                                                       class="btn-action btn-delete" 
                                                       title="Delete Review"
                                                       onclick="return confirm('Are you sure you want to delete this review?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-star-o" style="font-size: 4rem; color: #ccc;"></i>
                            <h4 class="mt-3">No Reviews Found</h4>
                            <p class="text-muted">Belum ada review yang tersedia.</p>
                        </div>
                    <?php endif; ?>
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
    <script>
        function toggleReview(reviewId) {
            const preview = document.querySelector(`#review-preview-${reviewId}`);
            const full = document.querySelector(`#review-full-${reviewId}`);
            
            if (full.classList.contains('show')) {
                full.classList.remove('show');
                preview.style.display = 'block';
            } else {
                full.classList.add('show');
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html> 