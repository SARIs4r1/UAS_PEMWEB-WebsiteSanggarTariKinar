<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$base_url     = '../';
$page_title   = 'Permintaan sewa';
$admin_active = 'sewa';
require_role('admin', $base_url);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $request_id = (int)($_POST['request_id'] ?? 0);
    $status     = $_POST['status'] ?? '';

    if ($request_id > 0 && in_array($status, ['disetujui', 'ditolak', 'menunggu'], true)) {
        $pdo->prepare('UPDATE rental_requests SET status = :status WHERE id = :id')
            ->execute(['status' => $status, 'id' => $request_id]);
        flash('success', 'Status permintaan diperbarui.');
    }
    redirect('sewa.php');
}

$requests = $pdo->query(
    'SELECT r.*, i.nama AS item_nama, u.name AS user_name, u.email AS user_email
     FROM rental_requests r
     JOIN items i ON i.id = r.item_id
     JOIN users u ON u.id = r.user_id
     ORDER BY r.created_at DESC'
)->fetchAll();

require __DIR__ . '/../includes/admin_header.php';
?>
<h1 class="admin-title">Permintaan sewa</h1>
<?php if (!$requests): ?>
    <p class="empty">Belum ada permintaan sewa.</p>
<?php else: ?>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Pelanggan</th><th>Item</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($requests as $r): ?>
                <tr>
                    <td><?= e($r['user_name']) ?><br><span class="meta"><?= e($r['user_email']) ?></span></td>
                    <td><?= e($r['item_nama']) ?> (<?= (int)$r['jumlah'] ?>)<?php if ($r['catatan']): ?><br><span class="meta"><?= e($r['catatan']) ?></span><?php endif; ?></td>
                    <td><?= e(tanggal_id($r['tanggal_mulai'])) ?> sampai <?= e(tanggal_id($r['tanggal_selesai'])) ?></td>
                    <td><span class="status status--<?= e($r['status']) ?>"><?= e(ucfirst($r['status'])) ?></span></td>
                    <td class="td-actions">
                        <form method="post" action="sewa.php">
                            <?= csrf_field() ?>
                            <input type="hidden" name="request_id" value="<?= (int)$r['id'] ?>">
                            <button type="submit" name="status" value="disetujui" class="btn btn--green btn--sm">Setujui</button>
                            <button type="submit" name="status" value="ditolak" class="btn btn--danger btn--sm">Tolak</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
