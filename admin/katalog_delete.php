<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$base_url = '../';
require_role('admin', $base_url);

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $delete = $pdo->prepare('DELETE FROM items WHERE id = :id');
    $delete->execute(['id' => $id]);
    $_SESSION['flash_success'] = 'Item katalog berhasil dihapus.';
}

header('Location: katalog_index.php');
exit;
