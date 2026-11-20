<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$base_url   = '../';
$page_title = 'Kelola Katalog';

require_role('admin', $base_url);

$items = $pdo->query('SELECT * FROM items ORDER BY created_at DESC')->fetchAll();

require __DIR__ . '/../includes/header.php';
?>

<h1>Kelola Katalog</h1>
<p><a class="btn" href="katalog_create.php">+ Tambah Item Baru</a></p>

<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Harga/Hari</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['nama']) ?></td>
            <td><?= htmlspecialchars($item['kategori']) ?></td>
            <td>Rp <?= number_format((float)$item['harga_sewa_per_hari'], 0, ',', '.') ?></td>
            <td><?= (int)$item['stok'] ?></td>
            <td class="action-links">
                <a href="katalog_edit.php?id=<?= (int)$item['id'] ?>">Ubah</a>
                <a href="katalog_delete.php?id=<?= (int)$item['id'] ?>"
                   onclick="return confirm('Yakin hapus item ini?');">Hapus</a>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($items)): ?>
        <tr><td colspan="5" class="muted">Belum ada data.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../includes/footer.php'; ?>
