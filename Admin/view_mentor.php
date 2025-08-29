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

// Get mentor ID from URL
$mentor_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($mentor_id <= 0) {
    header('Location: mentors.php?error=invalid_id');
    exit();
}

// Get mentor details
try {
    $stmt = $pdo->prepare("SELECT * FROM mentor WHERE id = ?");
    $stmt->execute([$mentor_id]);
    $mentor = $stmt->fetch();

    if (!$mentor) {
        header('Location: mentors.php?error=mentor_not_found');
        exit();
    }

    // Get events by this mentor
    $stmt = $pdo->prepare("
        SELECT * FROM event 
        WHERE id_trainer = ? 
        ORDER BY date DESC
    ");
    $stmt->execute([$mentor_id]);
    $mentor_events = $stmt->fetchAll();

} catch(PDOException $e) {
    header('Location: mentors.php?error=database_error');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Mentor - Admin idSpora</title>
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
        
        .mentor-detail-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .mentor-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid var(--primary-orange);
            margin: 0 auto 20px;
            display: block;
        }
        
        .mentor-info {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .mentor-name {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--dark-blue);
            margin-bottom: 10px;
        }
        
        .mentor-title {
            font-size: 1.2rem;
            color: var(--primary-orange);
            margin-bottom: 20px;
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
        
        .events-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--dark-blue);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--primary-orange);
            padding-bottom: 10px;
        }
        
        .event-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid var(--primary-orange);
            transition: all 0.3s ease;
        }
        
        .event-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .event-title {
            font-weight: bold;
            color: var(--dark-blue);
            margin-bottom: 10px;
        }
        
        .event-date {
            color: var(--primary-orange);
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .event-audience {
            color: #666;
            font-size: 0.9rem;
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
        
        .no-events {
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
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Admin Header -->
        <div class="admin-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1><i class="fas fa-user-tie"></i> Detail Mentor</h1>
                        <p class="mb-0">Informasi lengkap mentor idSpora</p>
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
                        <a class="nav-link active" href="mentors.php">
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
                <a href="mentors.php" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Mentor
                </a>

                <div class="mentor-detail-card">
                    <div class="mentor-info">
                        <img 
                            src="../uploads/profile/<?php echo htmlspecialchars($mentor['profile_pict']); ?>" 
                            alt="<?php echo htmlspecialchars($mentor['name']); ?>" 
                            class="mentor-photo"
                            onerror="this.src='../property/profile.png'"
                        >
                        <h2 class="mentor-name"><?php echo htmlspecialchars($mentor['name']); ?></h2>
                        <p class="mentor-title"><?php echo htmlspecialchars(substr($mentor['description'], 0, 100)) . '...'; ?></p>
                    </div>

                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value"><?php echo htmlspecialchars($mentor['name']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Deskripsi</div>
                            <div class="info-value"><?php echo htmlspecialchars($mentor['description']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?php echo htmlspecialchars($mentor['email']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Telepon</div>
                            <div class="info-value"><?php echo htmlspecialchars($mentor['phone']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">LinkedIn</div>
                            <div class="info-value">
                                <?php if ($mentor['linkedin']): ?>
                                    <a href="<?php echo htmlspecialchars($mentor['linkedin']); ?>" target="_blank">
                                        <?php echo htmlspecialchars($mentor['linkedin']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Tidak tersedia</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Instagram</div>
                            <div class="info-value">
                                <?php if ($mentor['instagram']): ?>
                                    <a href="<?php echo htmlspecialchars($mentor['instagram']); ?>" target="_blank">
                                        <?php echo htmlspecialchars($mentor['instagram']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Tidak tersedia</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>


                </div>

                <div class="events-section">
                    <h3 class="section-title">
                        <i class="fas fa-calendar-alt me-2"></i>Event yang Diampu
                    </h3>
                    
                    <?php if (!empty($mentor_events)): ?>
                        <?php foreach ($mentor_events as $event): ?>
                            <div class="event-card">
                                <div class="event-title"><?php echo htmlspecialchars($event['title']); ?></div>
                                <div class="event-date">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?php echo date('d F Y', strtotime($event['date'])); ?>
                                </div>
                                <div class="event-audience">
                                    <i class="fas fa-users me-1"></i>
                                    Target Audience: <?php echo number_format($event['audience']); ?> orang
                                </div>
                                <?php if ($event['mitra']): ?>
                                    <div class="event-audience">
                                        <i class="fas fa-handshake me-1"></i>
                                        Mitra: <?php echo htmlspecialchars($event['mitra']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-events">
                            <i class="fas fa-calendar-times" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                            <p>Mentor ini belum mengampu event apapun.</p>
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
