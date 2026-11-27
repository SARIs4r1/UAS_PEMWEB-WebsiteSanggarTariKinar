<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$base_url     = '../';
$page_title   = 'Ringkasan';
$admin_active = 'dashboard';
require_role('admin', $base_url);

$count = function (string $sql) use ($pdo): int {
    return (int)$pdo->query($sql)->fetchColumn();
};
$stat = [
    ['Kegiatan',        $count('SELECT COUNT(*) FROM kegiatan'), 'kegiatan.php'],
    ['Foto galeri',     $count('SELECT COUNT(*) FROM galeri'),   'galeri.php'],
    ['Tari',            $count('SELECT COUNT(*) FROM tari'),     'tari.php'],
    ['Item katalog',    $count('SELECT COUNT(*) FROM items'),    'katalog.php'],
    ['Sewa menunggu',   $count("SELECT COUNT(*) FROM rental_requests WHERE status = 'menunggu'"), 'sewa.php'],
    ['Pelanggan',       $count("SELECT COUNT(*) FROM users WHERE role = 'pelanggan'"), 'sewa.php'],
];

$latest = $pdo->query(
    'SELECT r.*, i.nama AS item_nama, u.name AS user_name
     FROM rental_requests r
     JOIN items i ON i.id = r.item_id
     JOIN users u ON u.id = r.user_id
     ORDER BY r.created_at DESC LIMIT 5'
)->fetchAll();

require __DIR__ . '/../includes/admin_header.php';
?>
<h1 class="admin-title">Ringkasan</h1>
<p class="meta">Kelola isi website sanggar dari sini. Halaman admin hanya bisa dibuka akun dengan role admin.</p>

<div class="stat-grid">
    <?php foreach ($stat as [$label, $n, $href]): ?>
        <a class="stat" href="<?= e($href) ?>">
            <span class="stat-n"><?= $n ?></span>
            <span class="stat-l"><?= e($label) ?></span>
        </a>
    <?php endforeach; ?>
</div>

<h2 class="admin-sub">Permintaan sewa terbaru</h2>
<?php if (!$latest): ?>
    <p class="empty">Belum ada permintaan sewa.</p>
<?php else: ?>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Pelanggan</th><th>Item</th><th>Tanggal</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($latest as $r): ?>
                <tr>
                    <td><?= e($r['user_name']) ?></td>
                    <td><?= e($r['item_nama']) ?> (<?= (int)$r['jumlah'] ?>)</td>
                    <td><?= e(tanggal_id($r['tanggal_mulai'])) ?></td>
                    <td><span class="status status--<?= e($r['status']) ?>"><?= e(ucfirst($r['status'])) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
