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

// Get all events for dropdown
$stmt = $pdo->query("SELECT id, title FROM event ORDER BY date DESC");
$events = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $job_title = $_POST['job_title'] ?? '';
    $rate = $_POST['rate'] ?? '';
    $review = $_POST['review'] ?? '';
    $id_event = $_POST['id_event'] ?? '';
    
    if (empty($name) || empty($job_title) || empty($rate) || empty($review)) {
        $error = 'Nama, jabatan, rating, dan review wajib diisi!';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO review (name, job_title, rate, review, id_event) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $job_title, $rate, $review, $id_event ?: null]);
            $success = 'Review berhasil ditambahkan!';
            
            // Clear form data
            $name = $job_title = $rate = $review = $id_event = '';
        } catch(PDOException $e) {
            $error = 'Gagal menambahkan review: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Review - Admin idSpora</title>
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
        
        .rating-input {
            display: none;
        }
        
        .rating-stars {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .rating-stars:hover,
        .rating-stars:hover ~ .rating-stars {
            color: #f39c12;
        }
        
        .rating-input:checked ~ .rating-stars {
            color: #f39c12;
        }
        
        .rating-container {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }
        
        .rating-container label {
            margin: 0 2px;
        }
        
        .char-counter {
            font-size: 0.8rem;
            color: #666;
            text-align: right;
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
                        <h1><i class="fas fa-plus-circle"></i> Add New Review</h1>
                        <p class="mb-0">Tambah review baru ke database idSpora</p>
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
                        <a class="nav-link active" href="reviews.php">
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
                                    <label for="name" class="form-label">Reviewer Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="job_title" class="form-label">Job Title *</label>
                                    <input type="text" class="form-control" id="job_title" name="job_title" 
                                           value="<?php echo htmlspecialchars($job_title ?? ''); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Rating *</label>
                            <div class="rating-container">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="rate" value="<?php echo $i; ?>" 
                                           id="star<?php echo $i; ?>" class="rating-input" 
                                           <?php echo ($rate ?? '') == $i ? 'checked' : ''; ?> required>
                                    <label for="star<?php echo $i; ?>" class="rating-stars">
                                        <i class="fas fa-star"></i>
                                    </label>
                                <?php endfor; ?>
                            </div>
                            <small class="text-muted">Klik bintang untuk memberikan rating</small>
                        </div>

                        <div class="mb-3">
                            <label for="id_event" class="form-label">Event (Optional)</label>
                            <select class="form-select" id="id_event" name="id_event">
                                <option value="">Select Event (Optional)</option>
                                <?php foreach ($events as $event): ?>
                                    <option value="<?php echo $event['id']; ?>" 
                                            <?php echo ($id_event ?? '') == $event['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($event['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="review" class="form-label">Review Content *</label>
                            <textarea class="form-control" id="review" name="review" rows="6" required><?php echo htmlspecialchars($review ?? ''); ?></textarea>
                            <div class="char-counter">
                                <span id="review-count">0</span> characters
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-submit">
                                <i class="fas fa-save"></i> Save Review
                            </button>
                            <a href="reviews.php" class="btn btn-back">
                                <i class="fas fa-arrow-left"></i> Back to Reviews
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Character counter for review
        const reviewInput = document.getElementById('review');
        const reviewCount = document.getElementById('review-count');
        
        reviewInput.addEventListener('input', function() {
            const count = this.value.length;
            reviewCount.textContent = count;
        });
        
        // Initialize counter
        reviewInput.dispatchEvent(new Event('input'));
        
        // Rating stars interaction
        const ratingInputs = document.querySelectorAll('.rating-input');
        const ratingStars = document.querySelectorAll('.rating-stars');
        
        ratingInputs.forEach((input, index) => {
            input.addEventListener('change', function() {
                // Reset all stars
                ratingStars.forEach(star => {
                    star.style.color = '#ddd';
                });
                
                // Color stars up to selected rating
                const rating = parseInt(this.value);
                for (let i = 0; i < rating; i++) {
                    ratingStars[ratingStars.length - 1 - i].style.color = '#f39c12';
                }
            });
        });
        
        // Initialize rating display
        const checkedRating = document.querySelector('.rating-input:checked');
        if (checkedRating) {
            checkedRating.dispatchEvent(new Event('change'));
        }
    </script>
</body>
</html> 