<?php
require_once __DIR__ . '/../config/database.php';

// Fallback defaults if variables are not defined by include
if (!isset($host)) { $host = 'localhost'; }
if (!isset($dbname)) { $dbname = 'company_profil_idspora'; }
if (!isset($username)) { $username = 'root'; }
if (!isset($password)) { $password = ''; }
if (!isset($options) || !is_array($options)) {
	$options = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
	];
}

$mentors = [];

try {
	// Build PDO directly to avoid global die() on connection failure
	$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
	$pdo = new PDO($dsn, $username, $password, $options);

	$sql_get_mentor = "SELECT * FROM mentor";
	$stmt = $pdo->query($sql_get_mentor);
	$mentors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
	$mentors = [];
}

function bersihkanNama($name) {
    // daftar gelar umum, bisa ditambah sesuai kebutuhan
    $gelar = [
        'dr.', 'drs.', 'drh.', 'prof.', 'ir.', 
        's.kom.', 'm.kom.', 'm.t.', 'm.si.', 'ph.d', 
        's.t.', 's.e.', 's.h.', 's.pd.', 'm.pd.', 'm.ba.',
        's.psi', 'm.m'
    ];

    // hapus gelar-gelar dari nama
    $namaBersih = str_ireplace($gelar, '', $name);

    // hapus tanda baca apapun (titik, koma, dsb)
    $namaBersih = preg_replace('/[^\p{L}\p{N}\s]/u', '', $namaBersih);

    // hapus spasi → jadi huruf kecil rapat
    $slug = strtolower(str_replace(' ', '', trim($namaBersih)));

    return $slug;
}


