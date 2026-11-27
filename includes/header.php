<?php
$base_url     = $base_url ?? '';
$active       = $active ?? '';
$header_solid = $header_solid ?? false;
$user         = current_user();
$nama_sanggar = setting('nama_sanggar', 'Sanggar Tari Kinar');
$nav = [
    'beranda'  => ['index.php',    'Beranda'],
    'profil'   => ['profil.php',   'Profil'],
    'tari'     => ['tari.php',     'Tari'],
    'kegiatan' => ['kegiatan.php', 'Kegiatan'],
    'galeri'   => ['galeri.php',   'Galeri'],
    'sewa'     => ['katalog.php',  'Sewa Kostum'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($page_title) ? e($page_title) . ' | ' : '' ?><?= e($nama_sanggar) ?></title>
<meta name="description" content="<?= e(setting('tagline')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Young+Serif&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e($base_url) ?>assets/css/kinar.css?v=<?= (int)@filemtime(__DIR__ . '/../assets/css/kinar.css') ?>">
</head>
<body>
<a class="skip-link" href="#konten">Lewati ke konten</a>
<header class="site-header<?= $header_solid ? ' is-solid' : '' ?>" id="siteHeader">
    <div class="container bar">
        <a class="brand" href="<?= e($base_url) ?>index.php">
            <span class="brand-name"><?= e($nama_sanggar) ?></span>
            <span class="brand-sub">Tekarang, Sambas</span>
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="mainNav" aria-label="Buka menu">
            <span></span><span></span><span></span>
        </button>
        <nav class="nav" id="mainNav" aria-label="Menu utama">
            <?php foreach ($nav as $key => [$file, $label]): ?>
                <a class="nav-link<?= $active === $key ? ' is-active' : '' ?>" href="<?= e($base_url . $file) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
            <span class="nav-sep" aria-hidden="true"></span>
            <?php if ($user): ?>
                <?php if ($user['role'] === 'admin'): ?>
                    <a class="nav-link" href="<?= e($base_url) ?>admin/dashboard.php">Panel admin</a>
                <?php else: ?>
                    <a class="nav-link" href="<?= e($base_url) ?>sewa_saya.php">Sewa saya</a>
                <?php endif; ?>
                <form method="post" action="<?= e($base_url) ?>logout.php" class="inline-form">
                    <?= csrf_field() ?>
                    <button class="btn btn--ghost btn--sm" type="submit">Keluar</button>
                </form>
            <?php else: ?>
                <a class="nav-link" href="<?= e($base_url) ?>login.php">Masuk</a>
                <a class="btn btn--gold btn--sm" href="<?= e($base_url) ?>register.php">Daftar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<div class="toasts" role="status" aria-live="polite"><?= flash_render() ?></div>
<main id="konten">
