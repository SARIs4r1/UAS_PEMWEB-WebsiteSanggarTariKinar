<?php
require_once __DIR__ . '/includes/bootstrap.php';

$base_url     = '';
$page_title   = 'Sewa saya';
$header_solid = true;

require_login();
$user = current_user();

$stmt = $pdo->prepare(
    'SELECT r.*, i.nama AS item_nama, i.kategori
     FROM rental_requests r
     JOIN items i ON i.id = r.item_id
     WHERE r.user_id = :user_id
     ORDER BY r.created_at DESC'
);
$stmt->execute(['user_id' => $user['id']]);
$requests = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="section section--top">
    <div class="container">
        <h1 class="h2">Sewa saya</h1>
        <?php if (!$requests): ?>
            <p class="empty">Kamu belum mengajukan sewa. <a href="katalog.php">Lihat katalog</a>.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr><th>Item</th><th>Tanggal</th><th>Jumlah</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($requests as $r): ?>
                        <tr>
                            <td><?= e($r['item_nama']) ?><br><span class="meta"><?= e($r['kategori']) ?></span></td>
                            <td><?= e(tanggal_id($r['tanggal_mulai'])) ?> sampai <?= e(tanggal_id($r['tanggal_selesai'])) ?></td>
                            <td><?= (int)$r['jumlah'] ?></td>
                            <td><span class="status status--<?= e($r['status']) ?>"><?= e(ucfirst($r['status'])) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
