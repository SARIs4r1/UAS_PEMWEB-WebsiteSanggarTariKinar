<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$base_url   = '../';
$page_title = 'Kelola Penyewaan';

require_role('admin', $base_url);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = (int)($_POST['request_id'] ?? 0);
    $new_status = $_POST['status'] ?? '';

    if ($request_id > 0 && in_array($new_status, ['disetujui', 'ditolak', 'menunggu'], true)) {
        $update = $pdo->prepare('UPDATE rental_requests SET status = :status WHERE id = :id');
        $update->execute(['status' => $new_status, 'id' => $request_id]);
        $_SESSION['flash_success'] = 'Status permintaan sewa berhasil diperbarui.';
    }

    header('Location: sewa_index.php');
    exit;
}

$requests = $pdo->query(
    'SELECT r.*, i.nama AS item_nama, u.name AS user_name, u.email AS user_email
     FROM rental_requests r
     JOIN items i ON i.id = r.item_id
     JOIN users u ON u.id = r.user_id
     ORDER BY r.created_at DESC'
)->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<h1>Kelola Permintaan Sewa</h1>

<table>
    <thead>
        <tr>
            <th>Pelanggan</th>
            <th>Item</th>
            <th>Tanggal</th>
            <th>Jumlah</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($requests as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['user_name']) ?><br><span class="muted"><?= htmlspecialchars($r['user_email']) ?></span></td>
            <td><?= htmlspecialchars($r['item_nama']) ?></td>
            <td><?= htmlspecialchars($r['tanggal_mulai']) ?> s/d <?= htmlspecialchars($r['tanggal_selesai']) ?></td>
            <td><?= (int)$r['jumlah'] ?></td>
            <td class="status-<?= htmlspecialchars($r['status']) ?>"><?= htmlspecialchars(ucfirst($r['status'])) ?></td>
            <td>
                <form method="post" action="sewa_index.php" style="display:flex;gap:0.4rem;">
                    <input type="hidden" name="request_id" value="<?= (int)$r['id'] ?>">
                    <button type="submit" name="status" value="disetujui" class="btn btn-small">Setujui</button>
                    <button type="submit" name="status" value="ditolak" class="btn btn-small btn-danger">Tolak</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($requests)): ?>
        <tr><td colspan="6" class="muted">Belum ada permintaan sewa.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../includes/footer.php'; ?>
