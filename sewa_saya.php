<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$base_url   = '';
$page_title = 'Penyewaan Saya';

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

<h1>Penyewaan Saya</h1>

<?php if (empty($requests)): ?>
    <p class="muted">Kamu belum mengajukan penyewaan apapun. <a href="katalog.php">Lihat katalog</a>.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Kategori</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($requests as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['item_nama']) ?></td>
                <td><?= htmlspecialchars($r['kategori']) ?></td>
                <td><?= htmlspecialchars($r['tanggal_mulai']) ?></td>
                <td><?= htmlspecialchars($r['tanggal_selesai']) ?></td>
                <td><?= (int)$r['jumlah'] ?></td>
                <td class="status-<?= htmlspecialchars($r['status']) ?>"><?= htmlspecialchars(ucfirst($r['status'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
