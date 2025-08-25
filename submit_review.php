<?php
// Include database connection
require_once 'Admin/config/database.php';

// Get database connection
$pdo = getDBConnection();

// Initialize variables
$success = false;
$error_message = '';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $job_title = trim($_POST['job_title'] ?? '');
    $rate = intval($_POST['rate'] ?? 0);
    $review = trim($_POST['review'] ?? '');
    $id_event = intval($_POST['id_event'] ?? 0);

    // Validate inputs
    if (empty($name)) {
        $error_message = 'Nama reviewer wajib diisi!';
    } elseif (empty($job_title)) {
        $error_message = 'Pekerjaan wajib diisi!';
    } elseif ($rate < 1 || $rate > 5) {
        $error_message = 'Rating harus antara 1-5!';
    } elseif (empty($review)) {
        $error_message = 'Ulasan wajib diisi!';
    } elseif ($id_event <= 0) {
        $error_message = 'Event wajib dipilih!';
    } else {
        // Validate that the event exists and is a past event
        try {
            $today = date('Y-m-d');
            $stmt = $pdo->prepare("
                SELECT id, title, date 
                FROM event 
                WHERE id = ? AND date < ?
            ");
            $stmt->execute([$id_event, $today]);
            $event = $stmt->fetch();

            if (!$event) {
                $error_message = 'Event yang dipilih tidak valid atau belum selesai!';
            } else {
                // Insert review into database
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO review (name, job_title, rate, review, id_event) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$name, $job_title, $rate, $review, $id_event]);
                    $success = true;
                } catch(PDOException $e) {
                    $error_message = 'Gagal menyimpan review: ' . $e->getMessage();
                }
            }
        } catch(PDOException $e) {
            $error_message = 'Gagal memvalidasi event: ' . $e->getMessage();
        }
    }
}

// Redirect back to reviews page with appropriate message
if ($success) {
    header('Location: reviews.php?success=submitted');
} else {
    header('Location: reviews.php?error=' . urlencode($error_message));
}
exit();
?>
