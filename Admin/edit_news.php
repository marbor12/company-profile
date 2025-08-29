<?php
session_start();
// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';
$pdo = getDBConnection();

$article = null;
$error_message = '';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header('Location: news.php?error=missing_id');
    exit();
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = $_POST['content']; // Tidak perlu trim untuk HTML content
    $current_image_path = trim($_POST['current_image']);
    $id_to_update = trim($_POST['id']);

    if (empty($title) || empty($content)) {
        $error_message = 'Judul dan Konten tidak boleh kosong.';
    } else {
        $dbImagePath = $current_image_path;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = "../uploads/";
            $fileName = time() . "_" . basename($_FILES["image"]["name"]);
            $targetFile = $uploadDir . $fileName;

            $dbImagePath = "../uploads/" . $fileName;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                // Jika berhasil, hapus gambar lama (juga butuh ../)
                if (!empty($current_image_path) && file_exists("../" . $current_image_path)) {
                    unlink("../" . $current_image_path);
                }
            } else {
                 $dbImagePath = $current_image_path;
            }
        }

        try {
            $stmt = $pdo->prepare("UPDATE news SET title = ?, article = ?, picture = ? WHERE id = ?");
            $stmt->execute([$title, $content, $dbImagePath, $id_to_update]);
            header("Location: news.php?success=updated");
            exit();
        } catch (PDOException $e) {
            $error_message = "Gagal memperbarui berita. Silakan coba lagi.";
        }
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        header('Location: news.php?error=not_found');
        exit();
    }
} catch (PDOException $e) {
    header('Location: news.php?error=db_error');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit News - Admin idSpora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../styles.css" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">
    <!-- CKEditor Rich Text Editor with Fallback -->
    <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
    <script>
        // Fallback jika CDN gagal
        window.addEventListener('error', function(e) {
            if (e.target.src && e.target.src.includes('ckeditor')) {
                console.log('CKEditor CDN failed, loading from local fallback...');
                // Bisa tambahkan fallback ke local file jika ada
            }
        }, true);
    </script>
    <style>
        .admin-container {
            background: #ffffff;
            border-radius: 20px;
            margin: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            min-height: calc(100vh - 40px);
        }
        
        .admin-header{
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
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-blue);
            margin-bottom: 10px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #f6c728;
            box-shadow: 0 0 0 0.2rem rgba(246, 199, 40, 0.25);
        }

        .btn-submit {
            background: #f6c728;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: #e6b800;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .char-counter {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 5px;
        }

        .char-counter.warning {
            color: #f39c12;
        }
        
        .char-counter.danger {
            color: #e74c3c;
        }

        .admin-nav .nav-link.active::after {
            content: "";
            display: block;
            height: 2px;
            background: #f6c728; 
            margin-top: 5px;
        }

        .preview-container {
            position: relative;
            display: inline-block;
            max-width: 200px;
            max-height: 200px;
            overflow: hidden;
        }

        .img-preview {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid rgb(44, 62, 80);
            padding: 5px;
            background: #f8f9fa;
            display: block;
            transition: transform 0.3s ease; 
        }

        .preview-container:hover .img-preview {
            transform: scale(1.05);
        }

        .change-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.8);
            background: rgba(0,0,0,0.7);
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .preview-container:hover .change-btn {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }

        /* Rich Text Editor Styling */
        .ck-editor__editable {
            border-radius: 10px !important;
            border: 2px solid #e9ecef !important;
            min-height: 300px !important;
        }

        .ck.ck-editor__main {
            background: white !important;
        }

        .ck.ck-toolbar {
            background: #f8f9fa !important;
            border: 2px solid #e9ecef !important;
            border-bottom: none !important;
            border-radius: 10px 10px 0 0 !important;
        }

        .ck.ck-toolbar__items {
            background: transparent !important;
        }

        .ck.ck-button {
            background: transparent !important;
            border: none !important;
            color: #495057 !important;
        }

        .ck.ck-button:hover {
            background: #e9ecef !important;
            color: #212529 !important;
        }

        .ck.ck-button.ck-on {
            background: #f6c728 !important;
            color: white !important;
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
                        <h1><i class="fas fa-edit"></i> Edit News Article</h1>
                        <p class="mb-0">Edit artikel berita yang sudah ada di database idSpora</p>
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
                        <a class="nav-link active" href="news.php">
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
                <?php if ($error_message): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <div class="form-container">
                    <form action="edit_news.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($article['id']); ?>">
                        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($article['picture']); ?>">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Article Title *</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($article['title']); ?>" 
                                   maxlength="255" required>
                            <div class="char-counter">
                                <span id="title-count">0</span>/255 characters
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Article Content *</label>
                            <textarea class="form-control" id="content" name="content" rows="15" required><?php echo htmlspecialchars($article['article']); ?></textarea>
                            <div class="char-counter">
                                <span id="content-count">0</span> characters
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Gunakan editor di atas untuk formatting teks, gambar, dan link. Konten akan tersimpan sebagai HTML di database.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="imageInput" class="form-label">Upload Picture</label>
                            <input class="form-control" type="file" id="imageInput" name="image" accept="image/*" onchange="previewImage(event)">
                        </div>

                        <div class="preview-container text-center">
                            <img id="imagePreview" class="img-preview" 
                                 src="<?php echo !empty($article['picture']) ? '../uploads/' . htmlspecialchars($article['picture']) : 'https://via.placeholder.com/200x150.png?text=Preview'; ?>"
                                 alt="Image Preview">
                            <button type="button" class="change-btn" onclick="document.getElementById('imageInput').click()">Ganti Gambar</button>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-save"></i> Update Article
                            </button>
                            <a href="news.php" class="btn btn-back">
                                <i class="fas fa-arrow-left"></i> Back to News
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
    
    <!-- CKEditor Configuration with Enhanced Error Handling -->
    <script>
        let editor;
        let editorInitialized = false;
        
        // Function to initialize CKEditor
        function initCKEditor() {
            if (typeof ClassicEditor === 'undefined') {
                console.error('CKEditor not loaded. Falling back to textarea.');
                document.getElementById('content').style.display = 'block';
                return;
            }
            
            ClassicEditor
                .create(document.querySelector('#content'), {
                    toolbar: {
                        items: [
                            'undo', 'redo',
                            '|', 'heading',
                            '|', 'bold', 'italic', 'underline', 'strikethrough',
                            '|', 'fontColor', 'fontBackgroundColor',
                            '|', 'link', 'blockQuote', 'insertTable', 'mediaEmbed',
                            '|', 'bulletedList', 'numberedList',
                            '|', 'outdent', 'indent',
                            '|', 'alignment',
                            '|', 'removeFormat'
                        ]
                    },
                    heading: {
                        options: [
                            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                        ]
                    },
                    language: 'id',
                    removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload'],
                    table: {
                        contentToolbar: [
                            'tableColumn',
                            'tableRow',
                            'mergeTableCells'
                        ]
                    },
                    // Enhanced configuration for hosting
                    removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload'],
                    // Disable features that might cause issues in some hosting environments
                    simpleUpload: {
                        uploadUrl: null
                    }
                })
                .then(newEditor => {
                    editor = newEditor;
                    editorInitialized = true;
                    
                    // Hide original textarea
                    document.getElementById('content').style.display = 'none';
                    
                    // Auto update character count
                    editor.model.document.on('change:data', () => {
                        updateCharCount();
                    });
                    
                    // Auto save content to textarea
                    editor.model.document.on('change:data', () => {
                        const data = editor.getData();
                        document.getElementById('content').value = data;
                    });
                    
                    console.log('CKEditor initialized successfully');
                })
                .catch(error => {
                    console.error('CKEditor initialization failed:', error);
                    // Fallback to textarea
                    document.getElementById('content').style.display = 'block';
                    editorInitialized = false;
                });
        }
        
        // Initialize editor when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Wait a bit for CKEditor to load
            setTimeout(initCKEditor, 100);
        });

        function updateCharCount() {
            if (editor && editorInitialized) {
                try {
                    const data = editor.getData();
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data;
                    const plainText = tempDiv.textContent || tempDiv.innerText || '';
                    const charCount = document.getElementById('content-count');
                    if (charCount) {
                        charCount.textContent = plainText.length;
                    }
                } catch (error) {
                    console.error('Error updating character count:', error);
                }
            } else {
                // Fallback for textarea
                const textarea = document.getElementById('content');
                if (textarea) {
                    const charCount = document.getElementById('content-count');
                    if (charCount) {
                        charCount.textContent = textarea.value.length;
                    }
                }
            }
        }

        function previewImage(event) {
            const preview = document.getElementById('imagePreview');
            preview.src = URL.createObjectURL(event.target.files[0]);
        }

        // Character counter for title
        const titleInput = document.getElementById('title');
        const titleCount = document.getElementById('title-count');
        const titleCounter = titleCount.parentElement;
        
        titleInput.addEventListener('input', function() {
            const count = this.value.length;
            titleCount.textContent = count;
            
            if (count > 200) {
                titleCounter.className = 'char-counter danger';
            } else if (count > 150) {
                titleCounter.className = 'char-counter warning';
            } else {
                titleCounter.className = 'char-counter';
            }
        });
        
        // Initialize character count
        titleInput.dispatchEvent(new Event('input'));
    </script>
</body>
</html>