<?php
require_once __DIR__ . '/includes/bootstrap.php';

$base_url = '';
$active   = 'sewa';
$id       = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM items WHERE id = :id');
$stmt->execute(['id' => $id]);
$item = $stmt->fetch();

if (!$item) {
    http_response_code(404);
    $page_title = 'Item tidak ditemukan';
    require __DIR__ . '/includes/header.php';
    page_hero('Item tidak ditemukan');
    echo '<section class="section"><div class="container"><p>Item yang kamu cari tidak ada. <a href="katalog.php">Kembali ke katalog</a>.</p></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$page_title = $item['nama'];
$user       = current_user();

require __DIR__ . '/includes/header.php';
page_hero($item['nama'], $item['kategori']);
?>
<section class="section">
    <div class="container split">
        <div class="split-media"><?= photo($item['foto'], $item['nama'], 'ratio-45') ?></div>
        <div class="split-body">
            <p class="price price--lg"><?= e(rupiah($item['harga_sewa_per_hari'])) ?> <small>/ hari</small></p>
            <p class="meta">Stok tersedia: <?= (int)$item['stok'] ?></p>
            <div class="prose"><?= nl2br(e((string)$item['deskripsi'])) ?></div>

            <div class="btn-row">
                <?php if (!$user): ?>
                    <a class="btn btn--green" href="login.php">Masuk untuk mengajukan sewa</a>
                <?php elseif ($user['role'] === 'pelanggan'): ?>
                    <a class="btn btn--green" href="sewa_ajukan.php?item_id=<?= (int)$item['id'] ?>">Ajukan sewa</a>
                <?php else: ?>
                    <p class="meta">Akun admin tidak bisa mengajukan sewa.</p>
                <?php endif; ?>
                <a class="btn btn--outline" href="katalog.php">Kembali ke katalog</a>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
