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
$mentor = null;

// Get mentor ID from URL
$mentor_id = $_GET['id'] ?? null;

if (!$mentor_id) {
    header('Location: mentors.php');
    exit();
}

// Get mentor data
try {
    $stmt = $pdo->prepare("SELECT * FROM mentor WHERE id = ?");
    $stmt->execute([$mentor_id]);
    $mentor = $stmt->fetch();
    
    if (!$mentor) {
        header('Location: mentors.php?error=mentor_not_found');
        exit();
    }
} catch(PDOException $e) {
    header('Location: mentors.php?error=database_error');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $linkedin = $_POST['linkedin'] ?? '';
    $email = $_POST['email'] ?? '';
    $cv_filename = $mentor['cv']; // Keep existing CV if no new file uploaded
    $profile_pict = $mentor['profile_pict']; // Keep existing profile picture if no new file uploaded
    
    if (empty($name) || empty($description) || empty($email)) {
        $error = 'Nama, deskripsi, dan email wajib diisi!';
    } else {
        // Create upload directories if they don't exist
        $profile_upload_dir = '../uploads/profile/';
        $cv_upload_dir = '../uploads/cv/';
        
        if (!is_dir($profile_upload_dir)) {
            mkdir($profile_upload_dir, 0755, true);
        }
        if (!is_dir($cv_upload_dir)) {
            mkdir($cv_upload_dir, 0755, true);
        }
        
        // Handle profile picture upload
        if (isset($_FILES['profile_pict']) && $_FILES['profile_pict']['error'] === UPLOAD_ERR_OK) {
            $file_info = pathinfo($_FILES['profile_pict']['name']);
            $file_extension = strtolower($file_info['extension']);
            
            // Check file type for images
            $allowed_image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($file_extension, $allowed_image_extensions)) {
                $error = 'Foto profil harus berformat JPG, JPEG, PNG, GIF, atau WEBP!';
            } else {
                // Generate unique filename for profile picture
                $profile_pict = 'profile_' . strtolower(str_replace(' ', '_', $name)) . '_' . time() . '.' . $file_extension;
                $upload_path = $profile_upload_dir . $profile_pict;
                
                // Upload profile picture
                if (move_uploaded_file($_FILES['profile_pict']['tmp_name'], $upload_path)) {
                    // Delete old profile picture if exists
                    if (!empty($mentor['profile_pict']) && file_exists($profile_upload_dir . $mentor['profile_pict'])) {
                        unlink($profile_upload_dir . $mentor['profile_pict']);
                    }
                } else {
                    $error = 'Gagal mengupload foto profil!';
                }
            }
        }
        
        // Handle CV file upload (if no error from profile picture)
        if (empty($error) && isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
            $file_info = pathinfo($_FILES['cv_file']['name']);
            $file_extension = strtolower($file_info['extension']);
            
            // Check file type for CV
            $allowed_cv_extensions = ['pdf', 'doc', 'docx'];
            if (!in_array($file_extension, $allowed_cv_extensions)) {
                $error = 'File CV harus berformat PDF, DOC, atau DOCX!';
            } else {
                // Generate unique filename for CV
                $cv_filename = 'cv_' . strtolower(str_replace(' ', '_', $name)) . '_' . time() . '.' . $file_extension;
                $upload_path = $cv_upload_dir . $cv_filename;
                
                // Upload CV file
                if (move_uploaded_file($_FILES['cv_file']['tmp_name'], $upload_path)) {
                    // Delete old CV file if exists
                    if (!empty($mentor['cv']) && file_exists($cv_upload_dir . $mentor['cv'])) {
                        unlink($cv_upload_dir . $mentor['cv']);
                    }
                } else {
                    $error = 'Gagal mengupload file CV!';
                }
            }
        }
        
        // If no error, update database
        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("
                    UPDATE mentor SET name = ?, description = ?, linkedin = ?, email = ?, cv = ?, profile_pict = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$name, $description, $linkedin, $email, $cv_filename, $profile_pict, $mentor_id]);
                $success = 'Mentor berhasil diupdate!';
                
                // Update mentor data for display
                $mentor['name'] = $name;
                $mentor['description'] = $description;
                $mentor['linkedin'] = $linkedin;
                $mentor['email'] = $email;
                $mentor['cv'] = $cv_filename;
                $mentor['profile_pict'] = $profile_pict;
            } catch(PDOException $e) {
                $error = 'Gagal mengupdate mentor: ' . $e->getMessage();
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
    <title>Edit Mentor - Admin idSpora</title>
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
        
        .file-upload-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
            font-size: 0.9rem;
            color: #666;
        }
        
        .file-upload-info i {
            color: var(--primary-orange);
            margin-right: 5px;
        }
        
        .file-preview {
            margin-top: 10px;
            padding: 10px;
            background: #e8f5e8;
            border-radius: 5px;
            display: none;
        }
        
        .file-preview.show {
            display: block;
        }
        
        .current-cv {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
        }
        
        .current-cv i {
            color: #f39c12;
            margin-right: 5px;
        }
        
        .current-profile-pict {
            background: #e8f5e8;
            border: 1px solid #a8e6cf;
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
            text-align: center;
        }
        
        .current-profile-pict img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }
        
        .image-preview {
            margin-top: 10px;
            text-align: center;
            display: none;
        }
        
        .image-preview.show {
            display: block;
        }
        
        .image-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .required-field {
            color: #e74c3c;
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
                        <h1><i class="fas fa-edit"></i> Edit Mentor</h1>
                        <p class="mb-0">Edit data mentor: <?php echo htmlspecialchars($mentor['name']); ?></p>
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Mentor Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($mentor['name']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($mentor['email']); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description/Title *</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($mentor['description']); ?></textarea>
                            <small class="text-muted">Contoh: "Professor in Computer Science", "Entrepreneur & Startup Consultant", dll.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="linkedin" class="form-label">LinkedIn Profile</label>
                                    <input type="url" class="form-control" id="linkedin" name="linkedin" 
                                           value="<?php echo htmlspecialchars($mentor['linkedin']); ?>"
                                           placeholder="https://linkedin.com/in/username">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="profile_pict" class="form-label">Profile Picture</label>
                                    <input type="file" class="form-control" id="profile_pict" name="profile_pict" 
                                           accept="image/*">
                                    <div class="file-upload-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Format yang diizinkan:</strong> JPG, JPEG, PNG, GIF, WEBP<br>
                                        <strong>Ukuran maksimal:</strong> 5MB<br>
                                        <strong>Rasio aspek:</strong> Disarankan 1:1 (square)<br>
                                        <strong>File akan disimpan di:</strong> folder property/
                                    </div>
                                    
                                    <?php if (!empty($mentor['profile_pict'])): ?>
                                        <div class="current-profile-pict">
                                            <i class="fas fa-image"></i>
                                            <strong>Foto Profil Saat Ini:</strong><br>
                                            <img src="../uploads/profile/<?php echo htmlspecialchars($mentor['profile_pict']); ?>" 
                                                 alt="Profile Picture" class="mt-2">
                                            <br>
                                            <small class="text-muted">Upload file baru untuk mengganti foto profil yang ada</small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="image-preview" id="image-preview">
                                        <img id="preview-img" src="#" alt="Preview">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="cv_file" class="form-label">CV File (Optional)</label>
                                    <input type="file" class="form-control" id="cv_file" name="cv_file" 
                                           accept=".pdf,.doc,.docx">
                                    <div class="file-upload-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Format yang diizinkan:</strong> PDF, DOC, DOCX<br>
                                        <strong>Ukuran maksimal:</strong> 5MB<br>
                                        <strong>File akan disimpan di:</strong> folder property/
                                    </div>
                                    
                                    <?php if (!empty($mentor['cv'])): ?>
                                        <div class="current-cv">
                                            <i class="fas fa-file-pdf"></i>
                                            <strong>CV Saat Ini:</strong> 
                                            <a href="../uploads/cv/<?php echo htmlspecialchars($mentor['cv']); ?>" 
                                               target="_blank" class="text-decoration-none">
                                                <?php echo htmlspecialchars($mentor['cv']); ?>
                                            </a>
                                            <br>
                                            <small class="text-muted">Upload file baru untuk mengganti CV yang ada</small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="file-preview" id="file-preview">
                                        <i class="fas fa-file-pdf"></i>
                                        <span id="file-name"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-save"></i> Update Mentor
                            </button>
                            <a href="mentors.php" class="btn btn-back">
                                <i class="fas fa-arrow-left"></i> Back to Mentors
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Profile picture preview
        const profileInput = document.getElementById('profile_pict');
        const imagePreview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        
        profileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const file = this.files[0];
                
                // Check file size (5MB limit)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File foto profil terlalu besar! Maksimal 5MB.');
                    this.value = '';
                    imagePreview.classList.remove('show');
                    return;
                }
                
                // Check file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak diizinkan! Gunakan JPG, JPEG, PNG, GIF, atau WEBP.');
                    this.value = '';
                    imagePreview.classList.remove('show');
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.classList.add('show');
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.classList.remove('show');
            }
        });
        
        // CV file preview
        const fileInput = document.getElementById('cv_file');
        const filePreview = document.getElementById('file-preview');
        const fileName = document.getElementById('file-name');
        
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const file = this.files[0];
                fileName.textContent = file.name;
                filePreview.classList.add('show');
                
                // Check file size (5MB limit)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File CV terlalu besar! Maksimal 5MB.');
                    this.value = '';
                    filePreview.classList.remove('show');
                }
                
                // Check file type
                const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak diizinkan! Gunakan PDF, DOC, atau DOCX.');
                    this.value = '';
                    filePreview.classList.remove('show');
                }
            } else {
                filePreview.classList.remove('show');
            }
        });
    </script>
</body>
</html> 