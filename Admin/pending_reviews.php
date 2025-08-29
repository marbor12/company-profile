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

// Handle approval/rejection
if (isset($_POST['action']) && isset($_POST['review_id'])) {
    $review_id = $_POST['review_id'];
    $action = $_POST['action'];
    $admin_notes = trim($_POST['admin_notes'] ?? '');
    
    try {
        if ($action === 'approve') {
            // Get the pending review
            $stmt = $pdo->prepare("SELECT * FROM review_approval WHERE id = ? AND status = 'pending'");
            $stmt->execute([$review_id]);
            $pending_review = $stmt->fetch();
            
            if ($pending_review) {
                // Insert into main review table
                $stmt = $pdo->prepare("
                    INSERT INTO review (name, job_title, rate, review, id_event, status, approved_at, approved_by) 
                    VALUES (?, ?, ?, ?, ?, 'active', NOW(), ?)
                ");
                $stmt->execute([
                    $pending_review['name'],
                    $pending_review['job_title'],
                    $pending_review['rate'],
                    $pending_review['review'],
                    $pending_review['id_event'],
                    $_SESSION['admin_id'] ?? 1
                ]);
                
                // Update status to approved
                $stmt = $pdo->prepare("
                    UPDATE review_approval 
                    SET status = 'approved', admin_notes = ?, reviewed_by = ?, reviewed_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$admin_notes, $_SESSION['admin_id'] ?? 1, $review_id]);
                
                header('Location: pending_reviews.php?success=approved');
                exit();
            }
        } elseif ($action === 'reject') {
            // Update status to rejected
            $stmt = $pdo->prepare("
                UPDATE review_approval 
                SET status = 'rejected', admin_notes = ?, reviewed_by = ?, reviewed_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$admin_notes, $_SESSION['admin_id'] ?? 1, $review_id]);
            
            header('Location: pending_reviews.php?success=rejected');
            exit();
        }
    } catch(PDOException $e) {
        // Log error untuk debugging
        error_log("Review approval error: " . $e->getMessage());
        header('Location: pending_reviews.php?error=action_failed&details=' . urlencode($e->getMessage()));
        exit();
    }
}

// Get all pending reviews
$stmt = $pdo->query("
    SELECT ra.*, e.title as event_title 
    FROM review_approval ra 
    LEFT JOIN event e ON ra.id_event = e.id 
    WHERE ra.status = 'pending'
    ORDER BY ra.created_at DESC
");
$pending_reviews = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Reviews - Admin idSpora</title>
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
        
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }
        
        .table {
            margin-bottom: 0;
            min-width: 1000px;
        }
        
        .table th {
            border-top: none;
            background: #fff8d6;
            color: var(--dark-blue);
            font-weight: 600;
            white-space: nowrap;
            padding: 15px 12px;
            vertical-align: middle;
        }
        
        .table td {
            padding: 15px 12px;
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
        }
        
        /* Column Widths */
        .table th:nth-child(1), .table td:nth-child(1) { width: 15%; } /* Reviewer */
        .table th:nth-child(2), .table td:nth-child(2) { width: 12%; } /* Job Title */
        .table th:nth-child(3), .table td:nth-child(3) { width: 10%; } /* Rating */
        .table th:nth-child(4), .table td:nth-child(4) { width: 30%; } /* Review */
        .table th:nth-child(5), .table td:nth-child(5) { width: 15%; } /* Event */
        .table th:nth-child(6), .table td:nth-child(6) { width: 18%; } /* Actions */
        
        .reviewer-name {
            font-weight: 600;
            color: var(--dark-blue);
        }
        
        .job-title {
            color: #666;
            font-size: 0.85rem;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 12px;
            display: inline-block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .rating-stars {
            color: #f39c12;
            font-size: 1rem;
            margin-bottom: 5px;
        }
        
        .rating-stars .fa-star-o {
            color: #ddd;
        }
        
        .rating-number {
            font-size: 0.8rem;
            color: #666;
            font-weight: 600;
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
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            border-left: 3px solid var(--primary-orange);
            margin-bottom: 8px;
            position: relative;
        }
        
        .review-content::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 40px;
            height: 20px;
            background: linear-gradient(to right, transparent, #f8f9fa);
            pointer-events: none;
        }
        
        .event-title {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .actions-container {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
        
        .btn-approve {
            background: #27ae60;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }
        
        .btn-approve:hover {
            background: #229954;
            color: white;
            transform: translateY(-1px);
        }
        
        .btn-reject {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }
        
        .btn-reject:hover {
            background: #c0392b;
            color: white;
            transform: translateY(-1px);
        }
        
        .btn-view {
            background: var(--primary-yellow);
            color: #111;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.8rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .btn-view:hover {
            background: #e0b020;
            color: #111;
            transform: translateY(-1px);
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
            border-radius: 10px;
            border: none;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        .modal-header {
            background: var(--dark-blue);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .modal-footer {
            border-top: none;
            padding: 20px;
        }
        
        .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .form-control:focus {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 0.2rem rgba(255, 140, 66, 0.25);
        }
        
        .badge-pending {
            background: #f39c12;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .created-date {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
        }
        
        @media (max-width: 1200px) {
            .table {
                min-width: 800px;
            }
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
                        <h1><i class="fas fa-clock"></i> Pending Reviews</h1>
                        <p class="mb-0">Kelola review yang menunggu persetujuan admin</p>
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
                        <a class="nav-link" href="reviews.php">
                            <i class="fas fa-star"></i> Reviews
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="pending_reviews.php">
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
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> 
                        <?php 
                        if ($_GET['success'] === 'approved') {
                            echo 'Review berhasil disetujui dan ditampilkan di frontend!';
                        } elseif ($_GET['success'] === 'rejected') {
                            echo 'Review berhasil ditolak!';
                        }
                        ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Terjadi kesalahan saat memproses review.
                        <?php if (isset($_GET['details'])): ?>
                            <br><strong>Detail Error:</strong> <?php echo htmlspecialchars($_GET['details']); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="content-header">
                    <h2>Review Menunggu Persetujuan</h2>
                    <a href="reviews.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Back to Reviews
                    </a>
                </div>

                <div class="table-container">
                    <?php if ($pending_reviews): ?>
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
                                    <?php foreach ($pending_reviews as $review): ?>
                                        <tr>
                                            <td>
                                                <span class="reviewer-name"><?php echo htmlspecialchars($review['name']); ?></span>
                                                <div class="created-date">
                                                    <i class="fas fa-clock"></i> 
                                                    <?php echo date('d M Y H:i', strtotime($review['created_at'])); ?>
                                                </div>
                                                <span class="badge-pending">Pending</span>
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
                                                <div class="review-content">
                                                    <?php echo htmlspecialchars($review['review']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="event-title">
                                                    <?php echo htmlspecialchars($review['event_title'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="actions-container">
                                                    <button type="button" class="btn-view" 
                                                            onclick="viewReview(<?php echo $review['id']; ?>, '<?php echo htmlspecialchars($review['name']); ?>', '<?php echo htmlspecialchars($review['review']); ?>')">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                    <button type="button" class="btn-approve" 
                                                            onclick="approveReview(<?php echo $review['id']; ?>, '<?php echo htmlspecialchars($review['name']); ?>')">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn-reject" 
                                                            onclick="rejectReview(<?php echo $review['id']; ?>, '<?php echo htmlspecialchars($review['name']); ?>')">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle" style="font-size: 4rem; color: #27ae60;"></i>
                            <h4 class="mt-3">No Pending Reviews</h4>
                            <p class="text-muted">Semua review sudah diproses.</p>
                            <a href="reviews.php" class="btn-back">
                                <i class="fas fa-arrow-left"></i> Back to Reviews
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- View Review Modal -->
    <div class="modal fade" id="viewReviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Review Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="reviewDetails"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Review Modal -->
    <div class="modal fade" id="approveReviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Review</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="review_id" id="approveReviewId">
                        <input type="hidden" name="action" value="approve">
                        <p>Apakah Anda yakin ingin menyetujui review dari <strong id="approveReviewerName"></strong>?</p>
                        <div class="mb-3">
                            <label for="approveNotes" class="form-label">Catatan Admin (Opsional):</label>
                            <textarea class="form-control" id="approveNotes" name="admin_notes" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Review Modal -->
    <div class="modal fade" id="rejectReviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Review</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="review_id" id="rejectReviewId">
                        <input type="hidden" name="action" value="reject">
                        <p>Apakah Anda yakin ingin menolak review dari <strong id="rejectReviewerName"></strong>?</p>
                        <div class="mb-3">
                            <label for="rejectNotes" class="form-label">Alasan Penolakan:</label>
                            <textarea class="form-control" id="rejectNotes" name="admin_notes" rows="3" placeholder="Berikan alasan penolakan..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Review</button>
                    </div>
                </form>
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
        function viewReview(reviewId, reviewerName, reviewContent) {
            document.getElementById('reviewDetails').innerHTML = `
                <div class="mb-3">
                    <strong>Reviewer:</strong> ${reviewerName}
                </div>
                <div class="mb-3">
                    <strong>Review:</strong><br>
                    <div class="p-3 bg-light rounded">${reviewContent}</div>
                </div>
            `;
            new bootstrap.Modal(document.getElementById('viewReviewModal')).show();
        }
        
        function approveReview(reviewId, reviewerName) {
            document.getElementById('approveReviewId').value = reviewId;
            document.getElementById('approveReviewerName').textContent = reviewerName;
            new bootstrap.Modal(document.getElementById('approveReviewModal')).show();
        }
        
        function rejectReview(reviewId, reviewerName) {
            document.getElementById('rejectReviewId').value = reviewId;
            document.getElementById('rejectReviewerName').textContent = reviewerName;
            new bootstrap.Modal(document.getElementById('rejectReviewModal')).show();
        }
    </script>
</body>
</html>
