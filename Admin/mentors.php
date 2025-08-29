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
        $stmt = $pdo->prepare("DELETE FROM mentor WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: mentors.php?success=deleted');
        exit();
    } catch(PDOException $e) {
        header('Location: mentors.php?error=delete_failed');
        exit();
    }
}

// Get all mentors
$stmt = $pdo->query("SELECT * FROM mentor ORDER BY name");
$mentors = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Mentors - Admin idSpora</title>
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
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            margin: 0 3px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 35px;
            height: 35px;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .btn-edit {
            background: #3498db;
            color: white;
        }
        
        .btn-edit:hover {
            background: #2980b9;
            color: white;
        }
        
        .btn-delete {
            background: #e74c3c;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c0392b;
            color: white;
        }
        
        .btn-view {
            background: var(--primary-orange);
            color: white;
        }
        
        .btn-view:hover {
            background: #e67e22;
            color: white;
        }
        
        .btn-cv {
            background: #27ae60;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-cv:hover {
            background: #229954;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
            align-items: center;
            justify-content: center;
        }
        
        .table td {
            vertical-align: middle;
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
        
        .mentor-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-orange);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            overflow: hidden;
        }
        
        .mentor-profile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .email-link {
            color: var(--primary-orange);
            text-decoration: none;
        }
        
        .email-link:hover {
            color: var(--dark-blue);
        }
        
        .linkedin-link {
            color: #0077b5;
            text-decoration: none;
        }
        
        .linkedin-link:hover {
            color: #005885;
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
                        <h1><i class="fas fa-users"></i> Manage Mentors</h1>
                        <p class="mb-0">Kelola semua mentor idSpora</p>
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
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> 
                        <?php echo $_GET['success'] === 'deleted' ? 'Mentor berhasil dihapus!' : 'Operasi berhasil!'; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Terjadi kesalahan saat menghapus mentor.
                    </div>
                <?php endif; ?>

                <div class="content-header">
                    <h2>Daftar Mentors</h2>
                    <a href="add_mentor.php" class="btn-add">
                        <i class="fas fa-plus"></i> Add New Mentor
                    </a>
                </div>

                <div class="table-container">
                    <?php if ($mentors): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mentor</th>
                                        <th>Description</th>
                                        <th>Email</th>
                                        <th>LinkedIn</th>
                                        <th>CV</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($mentors as $mentor): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mentor-avatar me-3">
                                                        <?php if (!empty($mentor['profile_pict'])): ?>
                                                            <img src="../uploads/profile/<?php echo htmlspecialchars($mentor['profile_pict']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($mentor['name']); ?>" 
                                                                 class="mentor-profile-img">
                                                        <?php else: ?>
                                                            <?php echo strtoupper(substr($mentor['name'], 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($mentor['name']); ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($mentor['description']); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <a href="mailto:<?php echo htmlspecialchars($mentor['email']); ?>" class="email-link">
                                                    <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($mentor['email']); ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if ($mentor['linkedin']): ?>
                                                    <a href="<?php echo htmlspecialchars($mentor['linkedin']); ?>" 
                                                       target="_blank" class="linkedin-link">
                                                        <i class="fab fa-linkedin"></i> LinkedIn
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($mentor['cv']): ?>
                                                    <a href="../uploads/cv/<?php echo htmlspecialchars($mentor['cv']); ?>" 
                                                       target="_blank" class="btn-cv">
                                                        <i class="fas fa-file-pdf"></i> View CV
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="edit_mentor.php?id=<?php echo $mentor['id']; ?>" class="btn-action btn-edit" title="Edit Mentor">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="view_mentor.php?id=<?php echo $mentor['id']; ?>" class="btn-action btn-view" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="mentors.php?delete=<?php echo $mentor['id']; ?>" 
                                                       class="btn-action btn-delete"
                                                       onclick="return confirm('Are you sure you want to delete this mentor?')"
                                                       title="Delete Mentor">
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
                            <i class="fas fa-users-slash" style="font-size: 4rem; color: #ccc;"></i>
                            <h4 class="mt-3">No Mentors Found</h4>
                            <p class="text-muted">Start by adding your first mentor.</p>
                            <a href="add_mentor.php" class="btn-add">
                                <i class="fas fa-plus"></i> Add First Mentor
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