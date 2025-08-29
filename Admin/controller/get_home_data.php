<?php
require_once __DIR__ . '/../config/database.php';

$event_gallery = [];
$total_events = 0;
$total_audience = 0;
$total_partners = 0;
$error = '';

try {
	// Build PDO directly to avoid global die() on connection failure
	$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
	$pdo = new PDO($dsn, $username, $password, $options);

	// 1) Event gallery: 6 latest past events with documentation
	$stmt = $pdo->query("\n\t\tSELECT \n\t\t\te.id,\n\t\t\te.title,\n\t\t\te.date,\n\t\t\te.audience,\n\t\t\te.venue,\n\t\t\te.category,\n\t\t\te.type,\n\t\t\td.picture,\n\t\t\td.id_dokumentasi\n\t\tFROM event e\n\t\tLEFT JOIN documentation d ON e.id = d.id_event\n\t\tWHERE e.date < CURDATE()\n\t\tORDER BY e.date DESC\n\t\tLIMIT 6\n\t");
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$grouped = [];
	foreach ($rows as $row) {
		$eventId = (int)$row['id'];
		if (!isset($grouped[$eventId])) {
			$grouped[$eventId] = [
				'id' => $row['id'],
				'title' => $row['title'],
				'date' => $row['date'],
				'audience' => $row['audience'],
				'venue' => $row['venue'],
				'category' => $row['category'],
				'type' => $row['type'],
				'images' => []
			];
		}
		if (!empty($row['picture'])) {
			$grouped[$eventId]['images'][] = $row['picture'];
		}
	}
	$event_gallery = array_values($grouped);

	// 2) Stats section
	$stmt = $pdo->query("SELECT COUNT(*) as total FROM event");
	$total_events = (int)($stmt->fetch()['total'] ?? 0);

	$stmt = $pdo->query("SELECT SUM(audience) as total FROM event WHERE audience IS NOT NULL");
	$total_audience = (int)($stmt->fetch()['total'] ?? 0);

	$stmt = $pdo->query("SELECT COUNT(DISTINCT mitra) as total FROM event WHERE mitra IS NOT NULL AND mitra != ''");
	$total_partners = (int)($stmt->fetch()['total'] ?? 0);

} catch (Throwable $e) {
	$error = 'Tidak dapat memuat data beranda saat ini.';
}

return [
	'event_gallery' => $event_gallery,
	'total_events' => $total_events,
	'total_audience' => $total_audience,
	'total_partners' => $total_partners,
	'error' => $error,
];
