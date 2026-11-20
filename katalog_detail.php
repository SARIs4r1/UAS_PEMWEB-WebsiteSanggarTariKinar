<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$base_url = '';
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM items WHERE id = :id');
$stmt->execute(['id' => $id]);
$item = $stmt->fetch();

if (!$item) {
    http_response_code(404);
    $page_title = 'Item Tidak Ditemukan';
    require __DIR__ . '/includes/header.php';
    echo '<p>Item yang kamu cari tidak ditemukan.</p>';
    echo '<p><a href="katalog.php">Kembali ke katalog</a></p>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$page_title = $item['nama'];
$user = current_user();

require __DIR__ . '/includes/header.php';
?>

<article class="card">
    <span class="badge"><?= htmlspecialchars($item['kategori']) ?></span>
    <h1><?= htmlspecialchars($item['nama']) ?></h1>
    <p><?= nl2br(htmlspecialchars($item['deskripsi'] ?? '')) ?></p>
    <p><strong>Harga sewa:</strong> Rp <?= number_format((float)$item['harga_sewa_per_hari'], 0, ',', '.') ?> / hari</p>
    <p><strong>Stok tersedia:</strong> <?= (int)$item['stok'] ?></p>

    <?php if (!$user): ?>
        <p><a class="btn" href="login.php">Masuk untuk mengajukan sewa</a></p>
    <?php elseif ($user['role'] === 'pelanggan'): ?>
        <p><a class="btn" href="sewa_ajukan.php?item_id=<?= (int)$item['id'] ?>">Ajukan Sewa</a></p>
    <?php else: ?>
        <p class="muted">Login sebagai admin tidak dapat mengajukan sewa.</p>
    <?php endif; ?>

    <p><a href="katalog.php">&larr; Kembali ke katalog</a></p>
</article>

<?php require __DIR__ . '/includes/footer.php'; ?>
