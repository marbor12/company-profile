<?php
include __DIR__ . '/../config/database.php';
$pdo = getDBConnection();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID mentor tidak valid.");
}

$id = (int) $_GET['id'];

$sql = "SELECT * FROM mentor WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$mentor = $stmt->fetch();

if (!$mentor) {
    die("Mentor tidak ditemukan.");
}

$sql_detail = "SELECT * FROM expert_mentor WHERE id_mentor = ?";
$stmt_detail = $pdo->prepare($sql_detail);
$stmt_detail->execute([$id]);
$details = $stmt_detail->fetchAll(PDO::FETCH_ASSOC);
?>