<?php
require_once __DIR__ . '/../config/database.php';

$errorNotice = '';

try {
	// Build a safe PDO connection here to avoid fatal die() inside getDBConnection()
	$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
	$pdo = new PDO($dsn, $username, $password, $options);

	// Fetch past events for dropdown (only past events)
	$today = date('Y-m-d');
	$stmt = $pdo->prepare("SELECT id, title, date FROM event WHERE date < ? ORDER BY date DESC");
	$stmt->execute([$today]);
	$past_events = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Fetch latest 6 approved reviews with event title/date
	$stmt = $pdo->prepare("SELECT r.*, e.title as event_title, e.date as event_date FROM review r LEFT JOIN event e ON r.id_event = e.id WHERE r.status = 'active' ORDER BY r.approved_at DESC LIMIT 6");
	$stmt->execute();
	$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
	$past_events = [];
	$reviews = [];
	$errorNotice = 'Tidak dapat terhubung ke database saat ini. Menampilkan halaman tanpa data.';
}

return [
	'past_events' => $past_events,
	'reviews' => $reviews,
	'error' => $errorNotice,
];
