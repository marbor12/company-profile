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

// Sample mentors data
$sample_mentors = [
    [
        'name' => 'Buretno',
        'description' => 'Senior Trainer & Consultant',
        'email' => 'buretno@idspora.com',
        'linkedin' => 'https://linkedin.com/in/buretno',
        'cv' => ''
    ],
    [
        'name' => 'Sri Widyaningsih',
        'description' => 'Expert Mentor & Business Consultant',
        'email' => 'sri@idspora.com',
        'linkedin' => 'https://linkedin.com/in/sriwidyaningsih',
        'cv' => ''
    ],
    [
        'name' => 'Heru Nugroho',
        'description' => 'Digital Marketing Specialist',
        'email' => 'heru@idspora.com',
        'linkedin' => 'https://linkedin.com/in/herunugroho',
        'cv' => ''
    ],
    [
        'name' => 'Kristina',
        'description' => 'Leadership & Management Trainer',
        'email' => 'kristina@idspora.com',
        'linkedin' => 'https://linkedin.com/in/kristina',
        'cv' => ''
    ],
    [
        'name' => 'Robbi Hendriyanto',
        'description' => 'Technology & Innovation Expert',
        'email' => 'robbi@idspora.com',
        'linkedin' => 'https://linkedin.com/in/robbihendriyanto',
        'cv' => ''
    ]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Check if mentors already exist
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM mentor");
        $existing_count = $stmt->fetch()['count'];
        
        if ($existing_count > 0) {
            $error = 'Mentors already exist in database. Please add mentors manually.';
        } else {
            // Insert sample mentors
            $stmt = $pdo->prepare("
                INSERT INTO mentor (name, description, email, linkedin, cv) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            foreach ($sample_mentors as $mentor) {
                $stmt->execute([
                    $mentor['name'],
                    $mentor['description'],
                    $mentor['email'],
                    $mentor['linkedin'],
                    $mentor['cv']
                ]);
            }
            
            $success = 'Sample mentors berhasil ditambahkan! Sekarang Anda bisa menambahkan event.';
        }
    } catch(PDOException $e) {
        $error = 'Gagal menambahkan sample mentors: ' . $e->getMessage();
    }
}

// Get current mentors count
$stmt = $pdo->query("SELECT COUNT(*) as count FROM mentor");
$mentors_count = $stmt->fetch()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Sample Mentors - Admin idSpora</title>
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
        
        .admin-content {
            padding: 30px;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .mentor-list {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .mentor-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid var(--primary-orange);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Admin Header -->
        <div class="admin-header">
            <div class="container">
                <h1><i class="fas fa-users"></i> Add Sample Mentors</h1>
                <p class="mb-0">Tambahkan mentor sample untuk testing</p>
            </div>
        </div>

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
                    <div class="row">
                        <div class="col-md-8">
                            <h3>Current Status</h3>
                            <p>Total mentors in database: <strong><?php echo $mentors_count; ?></strong></p>
                            
                            <?php if ($mentors_count == 0): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>No mentors found!</strong> You need to add mentors before creating events.
                                </div>
                                
                                <h4>Sample Mentors to be Added:</h4>
                                <div class="mentor-list">
                                    <?php foreach ($sample_mentors as $mentor): ?>
                                        <div class="mentor-item">
                                            <h5><?php echo htmlspecialchars($mentor['name']); ?></h5>
                                            <p class="text-muted mb-1"><?php echo htmlspecialchars($mentor['description']); ?></p>
                                            <small><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($mentor['email']); ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <form method="POST">
                                    <button type="submit" class="btn btn-submit">
                                        <i class="fas fa-plus"></i> Add Sample Mentors
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> 
                                    <strong>Mentors available!</strong> You can now create events.
                                </div>
                                
                                <div class="d-flex gap-3">
                                    <a href="add_event.php" class="btn btn-submit">
                                        <i class="fas fa-plus-circle"></i> Add Event
                                    </a>
                                    <a href="mentors.php" class="btn btn-back">
                                        <i class="fas fa-users"></i> Manage Mentors
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-4">
                            <h4>Quick Actions</h4>
                            <div class="d-grid gap-2">
                                <a href="add_mentor.php" class="btn btn-outline-primary">
                                    <i class="fas fa-user-plus"></i> Add Custom Mentor
                                </a>
                                <a href="mentors.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-users"></i> View All Mentors
                                </a>
                                <a href="add_event.php" class="btn btn-outline-success">
                                    <i class="fas fa-calendar-plus"></i> Add Event
                                </a>
                                <a href="index.php" class="btn btn-outline-info">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
