<?php
require_once __DIR__ . '/includes/bootstrap.php';

$base_url   = '';
$page_title = 'Sewa Kostum';
$active     = 'sewa';

$kategori         = $_GET['kategori'] ?? '';
$allowed_kategori = ['Kostum Tari', 'Properti Tari'];

if (in_array($kategori, $allowed_kategori, true)) {
    $stmt = $pdo->prepare('SELECT * FROM items WHERE kategori = :kategori ORDER BY nama ASC');
    $stmt->execute(['kategori' => $kategori]);
} else {
    $kategori = '';
    $stmt = $pdo->query('SELECT * FROM items ORDER BY nama ASC');
}
$items = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
page_hero('Sewa kostum dan properti tari', 'Layanan pelengkap sanggar untuk sekolah, pelajar, dan panitia acara.');
?>
<section class="section">
    <div class="container">
        <div class="filters" role="group" aria-label="Filter kategori">
            <a class="chip<?= $kategori === '' ? ' is-active' : '' ?>" href="katalog.php">Semua</a>
            <a class="chip<?= $kategori === 'Kostum Tari' ? ' is-active' : '' ?>" href="katalog.php?kategori=Kostum+Tari">Kostum</a>
            <a class="chip<?= $kategori === 'Properti Tari' ? ' is-active' : '' ?>" href="katalog.php?kategori=Properti+Tari">Properti</a>
        </div>

        <?php if (!$items): ?>
            <p class="empty">Belum ada item untuk kategori ini.</p>
        <?php else: ?>
            <div class="grid grid--4">
                <?php foreach ($items as $it): ?>
                    <a class="tile" href="katalog_detail.php?id=<?= (int)$it['id'] ?>">
                        <div class="tile-media"><?= photo($it['foto'], $it['nama'], 'ratio-43') ?></div>
                        <div class="tile-body">
                            <h3><?= e($it['nama']) ?></h3>
                            <p class="meta"><?= e($it['kategori']) ?>, stok <?= (int)$it['stok'] ?></p>
                            <p class="price"><?= e(rupiah($it['harga_sewa_per_hari'])) ?> <small>/ hari</small></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
