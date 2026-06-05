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

// Get documentation ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: documentation.php?error=invalid_id');
    exit();
}

// Fetch documentation details with event information
try {
    $stmt = $pdo->prepare("
        SELECT d.*, e.title as event_title, e.date as event_date, e.audience as event_audience, e.mitra as event_mitra
        FROM documentation d 
        LEFT JOIN event e ON d.id_event = e.id 
        WHERE d.id_dokumentasi = ?
    ");
    $stmt->execute([$id]);
    $doc = $stmt->fetch();

    if (!$doc) {
        header('Location: documentation.php?error=not_found');
        exit();
    }
} catch(PDOException $e) {
    header('Location: documentation.php?error=db_error');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Documentation - Admin idSpora</title>
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
        
        .doc-detail-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .doc-image-container {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .doc-image {
            max-width: 100%;
            max-height: 450px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            border: 4px solid #fff;
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
        
        .btn-action-edit {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .btn-action-edit:hover {
            background: #2980b9;
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-action-delete {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .btn-action-delete:hover {
            background: #c0392b;
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
                        <h1><i class="fas fa-images"></i> Detail Documentation</h1>
                        <p class="mb-0">Informasi lengkap dokumentasi event</p>
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
                        <a class="nav-link active" href="documentation.php">
                            <i class="fas fa-images"></i> Documentation
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Admin Content -->
        <div class="admin-content">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="documentation.php" class="btn-back">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                    </a>
                    <div class="d-flex gap-2">
                        <a href="edit_documentation.php?id=<?php echo $doc['id_dokumentasi']; ?>" class="btn-action-edit">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                        <a href="documentation.php?delete=<?php echo $doc['id_dokumentasi']; ?>" 
                           class="btn-action-delete"
                           onclick="return confirm('Are you sure you want to delete this documentation?')">
                            <i class="fas fa-trash me-2"></i>Hapus
                        </a>
                    </div>
                </div>

                <div class="doc-detail-card">
                    <div class="doc-image-container">
                        <?php if ($doc['picture']): ?>
                            <img src="../uploads/documentation/<?php echo htmlspecialchars($doc['picture']); ?>" 
                                 alt="Documentation Image" class="doc-image">
                        <?php else: ?>
                            <div class="bg-light p-5 text-center text-muted rounded">
                                <i class="fas fa-image fa-4x mb-3"></i>
                                <p>Gambar tidak tersedia</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <h3 class="mb-4 text-dark-blue">Informasi Event</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Judul Event</div>
                            <div class="info-value">
                                <strong><?php echo htmlspecialchars($doc['event_title'] ?? 'N/A'); ?></strong>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tanggal Event</div>
                            <div class="info-value">
                                <?php echo $doc['event_date'] ? date('d F Y', strtotime($doc['event_date'])) : 'N/A'; ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Jumlah Audience</div>
                            <div class="info-value">
                                <?php echo isset($doc['event_audience']) ? number_format($doc['event_audience']) . ' orang' : 'N/A'; ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Mitra Kerjasama</div>
                            <div class="info-value">
                                <?php echo htmlspecialchars($doc['event_mitra'] ?? 'Tidak ada'); ?>
                            </div>
                        </div>
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
