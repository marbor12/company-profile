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

$success = '';
$error = '';

// Get all mentors for dropdown
$stmt = $pdo->query("SELECT id, name FROM mentor ORDER BY name");
$mentors = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $audience = $_POST['audience'] ?? '';
    $mitra = $_POST['mitra'] ?? '';
    $id_trainer = $_POST['id_trainer'] ?? '';
    $category = $_POST['category'] ?? '';
    $type = $_POST['type'] ?? '';
    $date = $_POST['date'] ?? '';
    $venue = $_POST['venue'] ?? '';
    
    // Validate required fields
    if (empty(trim($title)) || empty(trim($description)) || empty(trim($audience)) || 
        empty($id_trainer) || $id_trainer == '0' || 
        empty($category) || empty($type) || empty($date) || empty(trim($venue))) {
        
        $error = 'Semua field wajib diisi!';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO event (title, description, audience, mitra, id_trainer, category, type, date, venue) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$title, $description, $audience, $mitra, $id_trainer, $category, $type, $date, $venue]);
            $success = 'Event berhasil ditambahkan!';
            
            // Clear form data
            $title = $description = $audience = $mitra = $id_trainer = $category = $type = $date = $venue = '';
        } catch(PDOException $e) {
            $error = 'Gagal menambahkan event: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event - Admin idSpora</title>
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
        
        .admin-content {
            padding: 30px;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
        }
        
        .btn-submit {
            background: var(--primary-orange);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            background: var(--dark-blue);
            transform: translateY(-2px);
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: #5a6268;
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
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-blue);
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
                        <h1><i class="fas fa-plus-circle"></i> Add New Event</h1>
                        <p class="mb-0">Tambah event baru ke database idSpora</p>
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
                </ul>
            </div>
        </nav>

        <!-- Admin Content -->
        <div class="admin-content">
            <div class="container">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <div class="form-container">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Event Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo htmlspecialchars($title ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category *</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="webinar & seminar" <?php echo ($category ?? '') === 'webinar & seminar' ? 'selected' : ''; ?>>Webinar & Seminar</option>
                                        <option value="training & mini workshop" <?php echo ($category ?? '') === 'training & mini workshop' ? 'selected' : ''; ?>>Training & Mini Workshop</option>
                                        <option value="e-learning" <?php echo ($category ?? '') === 'e-learning' ? 'selected' : ''; ?>>E-Learning</option>
                                        <option value="video production" <?php echo ($category ?? '') === 'video production' ? 'selected' : ''; ?>>Video Production</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Event Type *</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="offline" <?php echo ($type ?? '') === 'offline' ? 'selected' : ''; ?>>Offline</option>
                                        <option value="online" <?php echo ($type ?? '') === 'online' ? 'selected' : ''; ?>>Online</option>
                                        <option value="hybrid" <?php echo ($type ?? '') === 'hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="date" class="form-label">Event Date *</label>
                                    <input type="date" class="form-control" id="date" name="date" 
                                           value="<?php echo $date ?? ''; ?>" min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="audience" class="form-label">Expected Audience *</label>
                                    <input type="number" class="form-control" id="audience" name="audience" 
                                           value="<?php echo htmlspecialchars($audience ?? ''); ?>" min="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="venue" class="form-label">Venue *</label>
                                    <input type="text" class="form-control" id="venue" name="venue" 
                                           value="<?php echo htmlspecialchars($venue ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mitra" class="form-label">Partner/Mitra</label>
                                    <input type="text" class="form-control" id="mitra" name="mitra" 
                                           value="<?php echo htmlspecialchars($mitra ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="id_trainer" class="form-label">Trainer/Mentor *</label>
                            <select class="form-select" id="id_trainer" name="id_trainer" required>
                                <option value="">Select Trainer</option>
                                <?php if (!empty($mentors)): ?>
                                    <?php foreach ($mentors as $mentor): ?>
                                        <option value="<?php echo $mentor['id']; ?>" 
                                                <?php echo ($id_trainer ?? '') == $mentor['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($mentor['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No mentors available</option>
                                <?php endif; ?>
                            </select>
                            <?php if (empty($mentors)): ?>
                                <small class="text-danger">Please add mentors first before creating events</small>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-save"></i> Save Event
                            </button>
                            <a href="events.php" class="btn btn-back">
                                <i class="fas fa-arrow-left"></i> Back to Events
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const submitBtn = document.querySelector('.btn-submit');
            
            form.addEventListener('submit', function(e) {
                const title = document.getElementById('title').value.trim();
                const description = document.getElementById('description').value.trim();
                const audience = document.getElementById('audience').value.trim();
                const id_trainer = document.getElementById('id_trainer').value;
                const category = document.getElementById('category').value;
                const type = document.getElementById('type').value;
                const date = document.getElementById('date').value;
                const venue = document.getElementById('venue').value.trim();
                
                if (!title || !description || !audience || !id_trainer || id_trainer === '0' || !category || !type || !date || !venue) {
                    e.preventDefault();
                    alert('Semua field wajib diisi!');
                    return false;
                }
                
                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                submitBtn.disabled = true;
            });
        });
    </script>
</body>
</html> 