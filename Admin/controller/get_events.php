<?php
require_once __DIR__ . '/../config/database.php';

try {
	$pdo = getDBConnection();

	$stmt = $pdo->query("
		SELECT 
			e.id,
			e.title,
			e.description,
			e.date,
			e.audience,
			e.venue,
			e.category,
			e.type,
			d.picture,
			d.id_dokumentasi
		FROM event e
		LEFT JOIN documentation d ON e.id = d.id_event
		WHERE e.date < CURDATE()
		ORDER BY e.date DESC
		LIMIT 6
	");
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

	$grouped = [];
	foreach ($rows as $row) {
		$eventId = (int)$row['id'];
		if (!isset($grouped[$eventId])) {
			$grouped[$eventId] = [
				'id' => $row['id'],
				'title' => $row['title'],
				'description' => $row['description'],
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

	$event_carousel = array_values($grouped);

	if (empty($event_carousel)) {
		$event_carousel = [[
			'id' => 0,
			'title' => 'Belum ada event',
			'description' => 'Event yang telah dilaksanakan akan tampil di sini.',
			'date' => date('Y-m-d'),
			'audience' => null,
			'venue' => 'TBD',
			'category' => 'Informasi',
			'type' => null,
			'images' => []
		]];
	}
} catch (Throwable $e) {
	$event_carousel = [[
		'id' => 0,
		'title' => 'Terjadi kesalahan',
		'description' => 'Tidak dapat memuat data event saat ini.',
		'date' => date('Y-m-d'),
		'audience' => null,
		'venue' => 'TBD',
		'category' => 'Error',
		'type' => null,
		'images' => []
	]];
}

return $event_carousel;
