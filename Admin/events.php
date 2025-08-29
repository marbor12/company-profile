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
        $stmt = $pdo->prepare("DELETE FROM event WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: events.php?success=deleted');
        exit();
    } catch(PDOException $e) {
        header('Location: events.php?error=delete_failed');
        exit();
    }
}

// Get all events with mentor information
$stmt = $pdo->query("
    SELECT e.*, m.name as mentor_name 
    FROM event e 
    LEFT JOIN mentor m ON e.id_trainer = m.id 
    ORDER BY e.date DESC
");
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - Admin idSpora</title>
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
            background: var(--primary-yellow);
            color: #111;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-add:hover {
            background: #e0b020;
            color: #111;
            transform: translateY(-2px);
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.06);
            overflow-x: auto;
        }
        
        .table {
            margin-bottom: 0;
            min-width: 1200px;
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
        .table th:nth-child(1), .table td:nth-child(1) { width: 25%; } /* Title */
        .table th:nth-child(2), .table td:nth-child(2) { width: 12%; } /* Category */
        .table th:nth-child(3), .table td:nth-child(3) { width: 10%; } /* Type */
        .table th:nth-child(4), .table td:nth-child(4) { width: 12%; } /* Date */
        .table th:nth-child(5), .table td:nth-child(5) { width: 15%; } /* Venue */
        .table th:nth-child(6), .table td:nth-child(6) { width: 12%; } /* Trainer */
        .table th:nth-child(7), .table td:nth-child(7) { width: 8%; }  /* Audience */
        .table th:nth-child(8), .table td:nth-child(8) { width: 6%; }  /* Actions */
        
        /* Title Column */
        .table td:nth-child(1) {
            word-wrap: break-word;
            max-width: 0;
        }
        
        .table td:nth-child(1) strong {
            display: block;
            margin-bottom: 5px;
            color: var(--dark-blue);
        }
        
        .table td:nth-child(1) small {
            display: block;
            line-height: 1.4;
            color: #6c757d;
        }
        
        /* Category Column */
        .table td:nth-child(2) {
            text-align: center;
        }
        
        .category-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #e3f2fd;
            color: #1976d2;
            text-align: center;
            white-space: nowrap;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Date Column */
        .table td:nth-child(4) {
            text-align: center;
        }
        
        .date-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            white-space: nowrap;
        }
        
        .status-upcoming {
            background: #e8f5e8;
            color: #27ae60;
        }
        
        .status-past {
            background: #ffeaea;
            color: #e74c3c;
        }
        
        .date-text {
            font-size: 0.8rem;
            color: #495057;
            font-weight: 500;
        }
        
        /* Actions Column */
        .table td:nth-child(8) {
            text-align: center;
        }
        
        .actions-container {
            display: flex;
            flex-direction: column;
            gap: 5px;
            align-items: center;
        }
        
        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            border: none;
        }
        
        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .btn-edit {
            background: #3498db;
            color: white;
        }
        
        .btn-edit:hover {
            background: #2980b9;
            color: white;
        }
        
        .btn-view {
            background: var(--primary-yellow);
            color: #111;
        }
        
        .btn-view:hover {
            background: #e0b020;
            color: #111;
        }
        
        .btn-delete {
            background: #e74c3c;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c0392b;
            color: white;
        }
        
        /* Responsive adjustments */
        @media (max-width: 1400px) {
            .table {
                min-width: 1000px;
            }
        }
        
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
            border-radius: 10px;
            border: none;
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
                        <h1><i class="fas fa-calendar-alt"></i> Manage Events</h1>
                        <p class="mb-0">Kelola event yang akan datang dan yang sudah lewat</p>
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
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> 
                        <?php echo $_GET['success'] === 'deleted' ? 'Event berhasil dihapus!' : 'Operasi berhasil!'; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Terjadi kesalahan saat menghapus event.
                    </div>
                <?php endif; ?>

                <div class="content-header">
                    <h2>Daftar Events</h2>
                    <div class="d-flex gap-2">
                        <select id="statusFilter" class="form-select" style="width: auto;">
                            <option value="all">All Events</option>
                            <option value="upcoming">Upcoming Events</option>
                            <option value="past">Past Events</option>
                        </select>
                        <a href="add_event.php" class="btn-add">
                            <i class="fas fa-plus"></i> Add New Event
                        </a>
                    </div>
                </div>

                <div class="table-container">
                    <?php if ($events): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Venue</th>
                                        <th>Trainer</th>
                                        <th>Audience</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($events as $event): ?>
                                        <?php 
                                        $eventDate = new DateTime($event['date']);
                                        $today = new DateTime();
                                        $statusClass = $eventDate >= $today ? 'status-upcoming' : 'status-past';
                                        $statusText = $eventDate >= $today ? 'upcoming' : 'past';
                                        ?>
                                        <tr data-status="<?php echo $statusText; ?>">
                                            <td>
                                                <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="category-badge">
                                                    <?php echo htmlspecialchars($event['category']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($event['type']); ?></td>
                                            <td>
                                                <div class="date-info">
                                                    <span class="status-badge <?php echo $statusClass; ?>">
                                                        <?php echo ucfirst($statusText); ?>
                                                    </span>
                                                    <span class="date-text">
                                                        <?php echo date('d M Y', strtotime($event['date'])); ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($event['venue']); ?></td>
                                            <td><?php echo htmlspecialchars($event['mentor_name'] ?? 'N/A'); ?></td>
                                            <td class="text-end"><?php echo number_format($event['audience']); ?></td>
                                            <td>
                                                <div class="actions-container">
                                                    <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn-action btn-edit" title="Edit Event">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="view_event.php?id=<?php echo $event['id']; ?>" class="btn-action btn-view" title="View Event">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="events.php?delete=<?php echo $event['id']; ?>" 
                                                       class="btn-action btn-delete" 
                                                       title="Delete Event"
                                                       onclick="return confirm('Are you sure you want to delete this event?')">
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
                            <i class="fas fa-calendar-times" style="font-size: 4rem; color: #ccc;"></i>
                            <h4 class="mt-3">No Events Found</h4>
                            <p class="text-muted">Start by adding your first event.</p>
                            <a href="add_event.php" class="btn-add">
                                <i class="fas fa-plus"></i> Add First Event
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
    <script>
        // Event status filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('statusFilter');
            const eventRows = document.querySelectorAll('tbody tr[data-status]');
            
            statusFilter.addEventListener('change', function() {
                const selectedStatus = this.value;
                
                eventRows.forEach(row => {
                    const eventStatus = row.getAttribute('data-status');
                    
                    if (selectedStatus === 'all' || eventStatus === selectedStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html> 