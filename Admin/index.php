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

// Get statistics
$stats = getDatabaseStats();
$totalEvents = $stats['total_events'];
$totalMentors = $stats['total_mentors'];
$totalNews = $stats['total_news'];
$totalReviews = $stats['total_reviews'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - idSpora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../styles.css" rel="stylesheet">
    <style>
        .admin-container {
            background: var(--light-cream);
            border-radius: 20px;
            margin: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            min-height: calc(100vh - 40px);
        }
        
        .admin-header {
            background: linear-gradient(135deg, var(--primary-orange) 0%, #FF8C42 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .admin-nav {
            background: var(--dark-blue);
            padding: 15px 0;
        }
        
        .admin-nav .nav-link {
            color: white !important;
            margin: 0 15px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .admin-nav .nav-link:hover {
            color: var(--primary-orange) !important;
        }
        
        .admin-nav .nav-link.active {
            color: var(--primary-orange) !important;
            font-weight: 700;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin: 15px 0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-icon {
            font-size: 3rem;
            color: var(--primary-orange);
            margin-bottom: 15px;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--dark-blue);
        }
        
        .stats-label {
            color: #666;
            font-size: 1.1rem;
        }
        
        .admin-content {
            padding: 30px;
        }
        
        .quick-actions {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin: 20px 0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .action-btn {
            background: var(--primary-orange);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            background: var(--dark-blue);
            color: white;
            transform: translateY(-2px);
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
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Admin Header -->
        <div class="admin-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1><i class="fas fa-cogs"></i> Admin Dashboard</h1>
                        <p class="mb-0">Selamat datang di panel administrasi idSpora</p>
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
                        <a class="nav-link active" href="index.php">
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
                </ul>
            </div>
        </nav>

        <!-- Admin Content -->
        <div class="admin-content">
            <div class="container">
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stats-card text-center">
                            <div class="stats-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="stats-number"><?php echo $totalEvents; ?></div>
                            <div class="stats-label">Total Events</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card text-center">
                            <div class="stats-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stats-number"><?php echo $totalMentors; ?></div>
                            <div class="stats-label">Total Mentors</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card text-center">
                            <div class="stats-icon">
                                <i class="fas fa-newspaper"></i>
                            </div>
                            <div class="stats-number"><?php echo $totalNews; ?></div>
                            <div class="stats-label">Total News</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card text-center">
                            <div class="stats-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stats-number"><?php echo $totalReviews; ?></div>
                            <div class="stats-label">Total Reviews</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <a href="add_event.php" class="action-btn">
                                <i class="fas fa-plus"></i> Add New Event
                            </a>
                            <a href="add_mentor.php" class="action-btn">
                                <i class="fas fa-user-plus"></i> Add New Mentor
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="add_news.php" class="action-btn">
                                <i class="fas fa-plus"></i> Add News Article
                            </a>
                            <a href="documentation.php" class="action-btn">
                                <i class="fas fa-images"></i> Manage Documentation
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="quick-actions">
                            <h3><i class="fas fa-clock"></i> Recent Events</h3>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM event ORDER BY date DESC LIMIT 5");
                            $recentEvents = $stmt->fetchAll();
                            if ($recentEvents): ?>
                                <div class="list-group">
                                    <?php foreach ($recentEvents as $event): ?>
                                        <div class="list-group-item">
                                            <h6><?php echo htmlspecialchars($event['title']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo date('d M Y', strtotime($event['date'])); ?> - 
                                                <?php echo htmlspecialchars($event['venue']); ?>
                                            </small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No events found</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="quick-actions">
                            <h3><i class="fas fa-users"></i> Recent Mentors</h3>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM mentor ORDER BY id DESC LIMIT 5");
                            $recentMentors = $stmt->fetchAll();
                            if ($recentMentors): ?>
                                <div class="list-group">
                                    <?php foreach ($recentMentors as $mentor): ?>
                                        <div class="list-group-item">
                                            <h6><?php echo htmlspecialchars($mentor['name']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($mentor['description']); ?>
                                            </small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No mentors found</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 