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

// Get event ID from URL
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($event_id <= 0) {
    header('Location: events.php?error=invalid_id');
    exit();
}

    // Get event details with mentor information
    try {
        $stmt = $pdo->prepare("
            SELECT e.*, m.name as mentor_name, m.description as mentor_description, m.profile_pict as mentor_photo
            FROM event e 
            LEFT JOIN mentor m ON e.id_trainer = m.id 
            WHERE e.id = ?
        ");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();

    if (!$event) {
        header('Location: events.php?error=event_not_found');
        exit();
    }

    // Get reviews for this event
    $stmt = $pdo->prepare("
        SELECT * FROM review 
        WHERE id_event = ? 
        ORDER BY id DESC
    ");
    $stmt->execute([$event_id]);
    $event_reviews = $stmt->fetchAll();

    // Get documentation for this event
    $stmt = $pdo->prepare("
        SELECT * FROM documentation 
        WHERE id_event = ? 
        ORDER BY id_dokumentasi DESC
    ");
    $stmt->execute([$event_id]);
    $event_documentation = $stmt->fetchAll();

} catch(PDOException $e) {
    header('Location: events.php?error=database_error');
    exit();
}

// Calculate event status
$today = date('Y-m-d');
$event_date = $event['date'];
$is_past = $event_date < $today;
$is_today = $event_date == $today;
$is_upcoming = $event_date > $today;

// Calculate days difference
$date_diff = (strtotime($event_date) - strtotime($today)) / (60 * 60 * 24);
$days_text = '';
if ($is_past) {
    $days_text = abs($date_diff) . ' hari yang lalu';
} elseif ($is_today) {
    $days_text = 'Hari ini';
} else {
    $days_text = $date_diff . ' hari lagi';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Event - Admin idSpora</title>
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
        
        .event-detail-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .event-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .event-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--dark-blue);
            margin-bottom: 15px;
        }
        
        .event-status {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .status-past {
            background: #e74c3c;
            color: white;
        }
        
        .status-today {
            background: #f39c12;
            color: white;
        }
        
        .status-upcoming {
            background: #27ae60;
            color: white;
        }
        
        .event-date-info {
            font-size: 1.2rem;
            color: var(--primary-orange);
            font-weight: 500;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid var(--primary-orange);
        }
        
        .info-label {
            font-weight: bold;
            color: var(--dark-blue);
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #666;
        }
        
        .mentor-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--dark-blue);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--primary-orange);
            padding-bottom: 10px;
        }
        
        .mentor-card {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border-left: 4px solid var(--primary-orange);
        }
        
        .mentor-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-orange);
            margin-right: 20px;
        }
        
        .mentor-info h4 {
            margin: 0 0 5px 0;
            color: var(--dark-blue);
        }
        
        .mentor-info p {
            margin: 0;
            color: #666;
        }
        
        .reviews-section, .documentation-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .review-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid var(--primary-orange);
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .reviewer-name {
            font-weight: bold;
            color: var(--dark-blue);
        }
        
        .review-rating {
            color: #f39c12;
        }
        
        .review-text {
            color: #666;
            font-style: italic;
        }
        
        .documentation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .doc-item {
            background: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #ddd;
        }
        
        .doc-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .doc-info {
            padding: 15px;
        }
        
        .btn-back {
            background: var(--dark-blue);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .btn-back:hover {
            background: var(--primary-orange);
            color: white;
            transform: translateY(-2px);
        }
        
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-item {
            background: var(--primary-orange);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
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
                        <h1><i class="fas fa-calendar-alt"></i> Detail Event</h1>
                        <p class="mb-0">Informasi lengkap event idSpora</p>
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
                        <a class="nav-link active" href="events.php">
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
                <a href="events.php" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Event
                </a>

                <div class="event-detail-card">
                    <div class="event-header">
                        <h2 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h2>
                        <div class="event-status <?php echo $is_past ? 'status-past' : ($is_today ? 'status-today' : 'status-upcoming'); ?>">
                            <?php echo $is_past ? 'EVENT SELESAI' : ($is_today ? 'EVENT HARI INI' : 'EVENT AKAN DATANG'); ?>
                        </div>
                        <div class="event-date-info">
                            <i class="fas fa-calendar me-2"></i>
                            <?php echo date('d F Y', strtotime($event['date'])); ?>
                            <span class="ms-3">(<?php echo $days_text; ?>)</span>
                        </div>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo number_format($event['audience']); ?></div>
                            <div class="stat-label">Target Audience</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo count($event_reviews); ?></div>
                            <div class="stat-label">Reviews</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo count($event_documentation); ?></div>
                            <div class="stat-label">Documentation</div>
                        </div>
                    </div>

                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Judul Event</div>
                            <div class="info-value"><?php echo htmlspecialchars($event['title']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tanggal Event</div>
                            <div class="info-value"><?php echo date('d F Y', strtotime($event['date'])); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Target Audience</div>
                            <div class="info-value"><?php echo number_format($event['audience']); ?> orang</div>
                        </div>
                        <?php if ($event['mitra']): ?>
                        <div class="info-item">
                            <div class="info-label">Mitra</div>
                            <div class="info-value"><?php echo htmlspecialchars($event['mitra']); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($event['description']): ?>
                        <div class="info-item">
                            <div class="info-label">Deskripsi Event</div>
                            <div class="info-value"><?php echo nl2br(htmlspecialchars($event['description'])); ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($event['mentor_name']): ?>
                <div class="mentor-section">
                    <h3 class="section-title">
                        <i class="fas fa-user-tie me-2"></i>Mentor Event
                    </h3>
                    <div class="mentor-card">
                        <img 
                            src="../uploads/profile/<?php echo htmlspecialchars($event['mentor_photo']); ?>" 
                            alt="<?php echo htmlspecialchars($event['mentor_name']); ?>" 
                            class="mentor-photo"
                            onerror="this.src='../property/profile.png'"
                        >
                        <div class="mentor-info">
                            <h4><?php echo htmlspecialchars($event['mentor_name']); ?></h4>
                            <p><?php echo htmlspecialchars(substr($event['mentor_description'], 0, 100)) . '...'; ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="reviews-section">
                    <h3 class="section-title">
                        <i class="fas fa-star me-2"></i>Reviews Event (<?php echo count($event_reviews); ?>)
                    </h3>
                    
                    <?php if (!empty($event_reviews)): ?>
                        <?php foreach ($event_reviews as $review): ?>
                            <div class="review-card">
                                <div class="review-header">
                                    <div class="reviewer-name"><?php echo htmlspecialchars($review['name']); ?></div>
                                    <div class="review-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?php echo $i <= $review['rate'] ? '' : '-o'; ?>"></i>
                                        <?php endfor; ?>
                                        <span class="ms-2">(<?php echo $review['rate']; ?>/5)</span>
                                    </div>
                                </div>
                                <div class="review-text">"<?php echo htmlspecialchars($review['review']); ?>"</div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-data">
                            <i class="fas fa-star-o" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                            <p>Belum ada review untuk event ini.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="documentation-section">
                    <h3 class="section-title">
                        <i class="fas fa-images me-2"></i>Documentation Event (<?php echo count($event_documentation); ?>)
                    </h3>
                    
                    <?php if (!empty($event_documentation)): ?>
                        <div class="documentation-grid">
                            <?php foreach ($event_documentation as $doc): ?>
                                <div class="doc-item">
                                    <img 
                                        src="../uploads/documentation/<?php echo htmlspecialchars($doc['picture']); ?>" 
                                        alt="Documentation" 
                                        class="doc-image"
                                        onerror="this.src='../property/profile.png'"
                                    >
                                    <div class="doc-info">
                                        <small class="text-muted">Documentation #<?php echo $doc['id_dokumentasi']; ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-data">
                            <i class="fas fa-images" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                            <p>Belum ada documentation untuk event ini.</p>
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
</body>
</html>
