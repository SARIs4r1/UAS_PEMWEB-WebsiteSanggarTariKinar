<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$base_url   = '../';
$page_title = 'Dashboard Admin';

require_role('admin', $base_url);

$total_items    = $pdo->query('SELECT COUNT(*) AS c FROM items')->fetch()['c'];
$total_pending  = $pdo->query("SELECT COUNT(*) AS c FROM rental_requests WHERE status = 'menunggu'")->fetch()['c'];
$total_users    = $pdo->query("SELECT COUNT(*) AS c FROM users WHERE role = 'pelanggan'")->fetch()['c'];

require __DIR__ . '/../includes/header.php';
?>

<h1>Dashboard Admin</h1>
<p class="muted">Halaman ini hanya bisa diakses oleh role <strong>admin</strong> (access control via <code>require_role()</code>).</p>

<div class="grid">
    <div class="card">
        <h3>Total Item Katalog</h3>
        <p style="font-size:1.6rem;margin:0;"><?= (int)$total_items ?></p>
        <a href="katalog_index.php">Kelola Katalog &rarr;</a>
    </div>
    <div class="card">
        <h3>Permintaan Sewa Menunggu</h3>
        <p style="font-size:1.6rem;margin:0;"><?= (int)$total_pending ?></p>
        <a href="sewa_index.php">Kelola Penyewaan &rarr;</a>
    </div>
    <div class="card">
        <h3>Total Pelanggan Terdaftar</h3>
        <p style="font-size:1.6rem;margin:0;"><?= (int)$total_users ?></p>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
