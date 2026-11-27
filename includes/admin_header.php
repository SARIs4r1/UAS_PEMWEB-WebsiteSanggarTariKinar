<?php
$base_url     = $base_url ?? '../';
$admin_active = $admin_active ?? '';
$user         = current_user();
$menu = [
    'dashboard'  => ['dashboard.php',  'Ringkasan'],
    'kegiatan'   => ['kegiatan.php',   'Kegiatan'],
    'galeri'     => ['galeri.php',     'Galeri foto'],
    'tari'       => ['tari.php',       'Tari'],
    'katalog'    => ['katalog.php',    'Katalog sewa'],
    'sewa'       => ['sewa.php',       'Permintaan sewa'],
    'pengaturan' => ['pengaturan.php', 'Pengaturan situs'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($page_title) ? e($page_title) . ' | ' : '' ?>Panel Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Young+Serif&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e($base_url) ?>assets/css/kinar.css?v=<?= (int)@filemtime(__DIR__ . '/../assets/css/kinar.css') ?>">
</head>
<body class="admin-body">
<div class="admin">
    <aside class="admin-side">
        <p class="admin-brand"><?= e(setting('nama_sanggar', 'Sanggar Tari Kinar')) ?></p>
        <p class="admin-who">Masuk sebagai <?= e($user['name']) ?></p>
        <nav aria-label="Menu admin">
            <?php foreach ($menu as $key => [$file, $label]): ?>
                <a class="admin-link<?= $admin_active === $key ? ' is-active' : '' ?>" href="<?= e($file) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="admin-side-foot">
            <a class="admin-link" href="../index.php" target="_blank" rel="noopener">Lihat situs</a>
            <form method="post" action="../logout.php">
                <?= csrf_field() ?>
                <button class="admin-link admin-link--btn" type="submit">Keluar</button>
            </form>
        </div>
    </aside>
    <main class="admin-main" id="konten">
        <div class="toasts" role="status" aria-live="polite"><?= flash_render() ?></div>
