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

// Get documentation ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: documentation.php?error=invalid_id');
    exit();
}

// Fetch documentation details
try {
    $stmt = $pdo->prepare("SELECT * FROM documentation WHERE id_dokumentasi = ?");
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

// Get only past events for dropdown
$today = date('Y-m-d');
$stmt = $pdo->prepare("
    SELECT id, title, date 
    FROM event 
    WHERE date < ? 
    ORDER BY date DESC
");
$stmt->execute([$today]);
$past_events = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_event = $_POST['id_event'] ?? '';
    
    // Validate required fields
    if (empty($id_event) || $id_event == '0') {
        $error = 'Event wajib dipilih!';
    } else {
        // Check if event is actually past
        $stmt = $pdo->prepare("SELECT date FROM event WHERE id = ?");
        $stmt->execute([$id_event]);
        $event = $stmt->fetch();
        
        if (!$event) {
            $error = 'Event tidak ditemukan!';
        } elseif ($event['date'] >= $today) {
            $error = 'Hanya event yang sudah lewat yang bisa ditambahkan dokumentasinya!';
        } else {
            $filename = $doc['picture']; // keep old picture by default
            $upload_success = true;
            
            // Check if a new file is uploaded
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['picture'];
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                $max_size = 5 * 1024 * 1024; // 5MB
                
                if (!in_array($file['type'], $allowed_types)) {
                    $error = 'Tipe file tidak didukung. Gunakan JPG, PNG, atau GIF.';
                    $upload_success = false;
                } elseif ($file['size'] > $max_size) {
                    $error = 'Ukuran file terlalu besar. Maksimal 5MB.';
                    $upload_success = false;
                } else {
                    $upload_dir = "../uploads/documentation/";
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    // Generate unique filename
                    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $new_filename = 'doc_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $filepath = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($file['tmp_name'], $filepath)) {
                        // Delete old picture file if it exists and is different
                        if ($doc['picture']) {
                            $old_filepath = $upload_dir . $doc['picture'];
                            if (file_exists($old_filepath)) {
                                unlink($old_filepath);
                            }
                        }
                        $filename = $new_filename;
                    } else {
                        $error = 'Gagal mengupload file.';
                        $upload_success = false;
                    }
                }
            }
            
            if ($upload_success && empty($error)) {
                try {
                    $stmt = $pdo->prepare("
                        UPDATE documentation 
                        SET picture = ?, id_event = ? 
                        WHERE id_dokumentasi = ?
                    ");
                    $stmt->execute([$filename, $id_event, $id]);
                    header('Location: documentation.php?success=updated');
                    exit();
                } catch(PDOException $e) {
                    $error = 'Gagal memperbarui documentation: ' . $e->getMessage();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Documentation - Admin idSpora</title>
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
            border-color: var(--primary-yellow);
            box-shadow: 0 0 0 0.2rem rgba(244, 196, 48, 0.25);
        }
        
        .btn-submit {
            background: var(--primary-yellow);
            color: #111;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            background: #e0b020;
            transform: translateY(-2px);
            color: #111;
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
        
        .file-upload-area {
            border: 2px dashed #e9ecef;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .file-upload-area:hover {
            border-color: var(--primary-orange);
            background: #fff5f2;
        }
        
        .file-upload-area.dragover {
            border-color: var(--primary-orange);
            background: #fff5f2;
        }
        
        .preview-image {
            max-width: 300px;
            max-height: 220px;
            border-radius: 8px;
            margin-top: 15px;
            border: 2px solid #e9ecef;
            object-fit: cover;
        }
        
        .current-image-info {
            font-weight: 500;
            color: #666;
            margin-top: 5px;
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
                        <h1><i class="fas fa-edit"></i> Edit Documentation</h1>
                        <p class="mb-0">Ubah dokumentasi untuk event yang sudah lewat</p>
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
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="id_event" class="form-label">Select Past Event *</label>
                            <select class="form-select" id="id_event" name="id_event" required>
                                <option value="">Select Past Event</option>
                                <?php if (!empty($past_events)): ?>
                                    <?php foreach ($past_events as $event): ?>
                                        <option value="<?php echo $event['id']; ?>" 
                                                <?php echo $doc['id_event'] == $event['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($event['title']); ?> 
                                            (<?php echo date('d M Y', strtotime($event['date'])); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No past events available</option>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Hanya event yang sudah lewat yang bisa dipilih untuk dokumentasi</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Documentation Image (Leave empty to keep current image)</label>
                            <div class="file-upload-area" onclick="document.getElementById('picture').click();">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                                <h5>Upload New Image</h5>
                                <p class="text-muted mb-0">Click to select or drag and drop</p>
                                <p class="text-muted small">Supported: JPG, PNG, GIF (Max: 5MB)</p>
                                <input type="file" id="picture" name="picture" accept="image/*" style="display: none;">
                            </div>
                            
                            <div class="mt-3">
                                <h6>Current Image Preview:</h6>
                                <div id="preview-container">
                                    <img id="preview-image" class="preview-image" 
                                         src="../uploads/documentation/<?php echo htmlspecialchars($doc['picture']); ?>" 
                                         alt="Current Image">
                                    <div class="current-image-info text-muted">Filename: <?php echo htmlspecialchars($doc['picture']); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-save"></i> Update Documentation
                            </button>
                            <a href="documentation.php" class="btn btn-back">
                                <i class="fas fa-arrow-left"></i> Back to Documentation
                            </a>
                        </div>
                    </form>
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
        // File upload preview
        document.getElementById('picture').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const previewImage = document.getElementById('preview-image');
            const infoText = document.querySelector('.current-image-info');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    if (infoText) {
                        infoText.textContent = 'New selected file: ' + file.name;
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        // Drag and drop functionality
        const uploadArea = document.querySelector('.file-upload-area');
        
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('picture').files = files;
                document.getElementById('picture').dispatchEvent(new Event('change'));
            }
        });

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const submitBtn = document.querySelector('.btn-submit');
            
            form.addEventListener('submit', function(e) {
                const id_event = document.getElementById('id_event').value;
                
                if (!id_event || id_event === '0') {
                    e.preventDefault();
                    alert('Event wajib dipilih!');
                    return false;
                }
                
                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                submitBtn.disabled = true;
            });
        });
    </script>
</body>
</html>
