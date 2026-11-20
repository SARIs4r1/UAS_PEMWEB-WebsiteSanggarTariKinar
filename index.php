<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$base_url   = '';
$page_title = 'Beranda';

$stmt  = $pdo->query('SELECT * FROM items ORDER BY created_at DESC LIMIT 4');
$items = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <h1>Sanggar Tari Kinar</h1>
    <p>Penyewaan kostum dan properti tari untuk pentas, festival, dan acara budaya di Sambas, Kalimantan Barat.</p>
    <a class="btn" href="katalog.php">Lihat Katalog</a>
</section>

<section>
    <h2>Katalog Terbaru</h2>
    <?php if (empty($items)): ?>
        <p class="muted">Belum ada data katalog.</p>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($items as $item): ?>
                <div class="card">
                    <span class="badge"><?= htmlspecialchars($item['kategori']) ?></span>
                    <h3><?= htmlspecialchars($item['nama']) ?></h3>
                    <p>Rp <?= number_format((float)$item['harga_sewa_per_hari'], 0, ',', '.') ?> / hari</p>
                    <a href="katalog_detail.php?id=<?= (int)$item['id'] ?>">Lihat Detail &rarr;</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
