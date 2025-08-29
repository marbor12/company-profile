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
        // Get the picture filename before deleting
        $stmt = $pdo->prepare("SELECT picture FROM documentation WHERE id_dokumentasi = ?");
        $stmt->execute([$id]);
        $doc = $stmt->fetch();
        
        if ($doc && $doc['picture']) {
            $picture_path = "../uploads/documentation/" . $doc['picture'];
            if (file_exists($picture_path)) {
                unlink($picture_path);
            }
        }
        
        $stmt = $pdo->prepare("DELETE FROM documentation WHERE id_dokumentasi = ?");
        $stmt->execute([$id]);
        header('Location: documentation.php?success=deleted');
        exit();
    } catch(PDOException $e) {
        header('Location: documentation.php?error=delete_failed');
        exit();
    }
}

// Get all documentation with event information
$stmt = $pdo->query("
    SELECT d.*, e.title as event_title, e.date as event_date 
    FROM documentation d 
    LEFT JOIN event e ON d.id_event = e.id 
    ORDER BY d.id_dokumentasi DESC
");
$documentations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Documentation - Admin idSpora</title>
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
            justify-content: between;
            align-items: center;
            margin-bottom: 30px;
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
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            border-top: none;
            background: #f8f9fa;
            color: var(--dark-blue);
            font-weight: 600;
        }
        
        .btn-action {
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            margin: 0 2px;
            font-size: 0.9rem;
        }
        
        .btn-edit {
            background: #3498db;
            color: white;
        }
        
        .btn-delete {
            background: #e74c3c;
            color: white;
        }
        
        .btn-view {
            background: var(--primary-orange);
            color: white;
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
        
        .doc-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        
        .event-date {
            color: #666;
            font-size: 0.9rem;
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
                        <h1><i class="fas fa-images"></i> Manage Documentation</h1>
                        <p class="mb-0">Kelola dokumentasi event yang sudah lewat</p>
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
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> 
                        <?php echo $_GET['success'] === 'deleted' ? 'Documentation berhasil dihapus!' : 'Operasi berhasil!'; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Terjadi kesalahan saat menghapus documentation.
                    </div>
                <?php endif; ?>

                <div class="content-header">
                    <h2>Daftar Documentation</h2>
                    <a href="add_documentation.php" class="btn-add">
                        <i class="fas fa-plus"></i> Add New Documentation
                    </a>
                </div>

                <div class="table-container">
                    <?php if ($documentations): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Event</th>
                                        <th>Event Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($documentations as $doc): ?>
                                        <tr>
                                            <td>
                                                <?php if ($doc['picture']): ?>
                                                    <img src="../uploads/documentation/<?php echo htmlspecialchars($doc['picture']); ?>" 
                                                         alt="Documentation" class="doc-image">
                                                <?php else: ?>
                                                    <div class="doc-image bg-light d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($doc['event_title'] ?? 'N/A'); ?></strong>
                                            </td>
                                            <td>
                                                <span class="event-date">
                                                    <?php echo $doc['event_date'] ? date('d M Y', strtotime($doc['event_date'])) : 'N/A'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="edit_documentation.php?id=<?php echo $doc['id_dokumentasi']; ?>" class="btn-action btn-edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="view_documentation.php?id=<?php echo $doc['id_dokumentasi']; ?>" class="btn-action btn-view">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="documentation.php?delete=<?php echo $doc['id_dokumentasi']; ?>" 
                                                   class="btn-action btn-delete"
                                                   onclick="return confirm('Are you sure you want to delete this documentation?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-images" style="font-size: 4rem; color: #ccc;"></i>
                            <h4 class="mt-3">No Documentation Found</h4>
                            <p class="text-muted">Start by adding documentation for past events.</p>
                            <a href="add_documentation.php" class="btn-add">
                                <i class="fas fa-plus"></i> Add First Documentation
                            </a>
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
