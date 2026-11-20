<?php
// Variabel yang harus di-set oleh halaman pemanggil SEBELUM include file ini:
//   $base_url   -> '' kalau file ada di root, '../' kalau di dalam folder admin/
//   $page_title -> judul halaman (opsional)
$base_url = $base_url ?? '';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - ' : '' ?>Sanggar Tari Kinar</title>
<link rel="stylesheet" href="<?= htmlspecialchars($base_url) ?>assets/css/style.css">
</head>
<body>
<header class="navbar">
    <div class="navbar-inner">
        <a class="navbar-brand" href="<?= htmlspecialchars($base_url) ?>index.php">Sanggar Tari Kinar</a>
        <nav class="navbar-links">
            <a href="<?= htmlspecialchars($base_url) ?>index.php">Beranda</a>
            <a href="<?= htmlspecialchars($base_url) ?>profil.php">Profil</a>
            <a href="<?= htmlspecialchars($base_url) ?>katalog.php">Katalog</a>
            <?php if ($user): ?>
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="<?= htmlspecialchars($base_url) ?>admin/dashboard.php">Dashboard Admin</a>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($base_url) ?>sewa_saya.php">Penyewaan Saya</a>
                <?php endif; ?>
                <span class="navbar-user">Halo, <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['role']) ?>)</span>
                <a href="<?= htmlspecialchars($base_url) ?>logout.php">Keluar</a>
            <?php else: ?>
                <a href="<?= htmlspecialchars($base_url) ?>login.php">Masuk</a>
                <a href="<?= htmlspecialchars($base_url) ?>register.php">Daftar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
