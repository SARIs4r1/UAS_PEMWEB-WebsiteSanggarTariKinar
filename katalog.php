<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$base_url   = '';
$page_title = 'Katalog';

$kategori = $_GET['kategori'] ?? '';
$allowed_kategori = ['Kostum Tari', 'Properti Tari'];

if (in_array($kategori, $allowed_kategori, true)) {
    $stmt = $pdo->prepare('SELECT * FROM items WHERE kategori = :kategori ORDER BY nama ASC');
    $stmt->execute(['kategori' => $kategori]);
} else {
    $stmt = $pdo->query('SELECT * FROM items ORDER BY nama ASC');
}
$items = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<h1>Katalog Kostum &amp; Properti Tari</h1>

<p class="action-links">
    <a href="katalog.php" class="<?= $kategori === '' ? 'btn btn-small' : '' ?>">Semua</a>
    <a href="katalog.php?kategori=Kostum+Tari" class="<?= $kategori === 'Kostum Tari' ? 'btn btn-small' : '' ?>">Kostum Tari</a>
    <a href="katalog.php?kategori=Properti+Tari" class="<?= $kategori === 'Properti Tari' ? 'btn btn-small' : '' ?>">Properti Tari</a>
</p>

<?php if (empty($items)): ?>
    <p class="muted">Tidak ada item untuk kategori ini.</p>
<?php else: ?>
    <div class="grid">
        <?php foreach ($items as $item): ?>
            <div class="card">
                <span class="badge"><?= htmlspecialchars($item['kategori']) ?></span>
                <h3><?= htmlspecialchars($item['nama']) ?></h3>
                <p class="muted">Stok: <?= (int)$item['stok'] ?></p>
                <p>Rp <?= number_format((float)$item['harga_sewa_per_hari'], 0, ',', '.') ?> / hari</p>
                <a href="katalog_detail.php?id=<?= (int)$item['id'] ?>">Lihat Detail &rarr;</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
