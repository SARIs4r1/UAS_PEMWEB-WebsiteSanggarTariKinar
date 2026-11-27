<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$base_url     = '../';
$page_title   = 'Kelola katalog sewa';
$admin_active = 'katalog';
require_role('admin', $base_url);

$errors = [];
$form   = ['id' => 0, 'nama' => '', 'kategori' => 'Kostum Tari', 'deskripsi' => '', 'harga_sewa_per_hari' => '', 'stok' => '', 'foto' => null];

/* ----- HAPUS (POST) ----- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
    csrf_verify();
    $stmt = $pdo->prepare('SELECT foto FROM items WHERE id = :id');
    $stmt->execute(['id' => (int)($_POST['id'] ?? 0)]);
    if ($row = $stmt->fetch()) {
        delete_upload($row['foto']);
        $pdo->prepare('DELETE FROM items WHERE id = :id')->execute(['id' => (int)$_POST['id']]);
        flash('success', 'Item dihapus.');
    }
    redirect('katalog.php');
}

/* ----- SIMPAN (POST) ----- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'simpan') {
    csrf_verify();
    $form['id']                  = (int)($_POST['id'] ?? 0);
    $form['nama']                = trim($_POST['nama'] ?? '');
    $form['kategori']            = $_POST['kategori'] ?? '';
    $form['deskripsi']           = trim($_POST['deskripsi'] ?? '');
    $form['harga_sewa_per_hari'] = trim($_POST['harga_sewa_per_hari'] ?? '');
    $form['stok']                = trim($_POST['stok'] ?? '');

    $lama = null;
    if ($form['id'] > 0) {
        $stmt = $pdo->prepare('SELECT * FROM items WHERE id = :id');
        $stmt->execute(['id' => $form['id']]);
        $lama = $stmt->fetch();
        if (!$lama) {
            redirect('katalog.php');
        }
        $form['foto'] = $lama['foto'];
    }

    if ($form['nama'] === '') {
        $errors[] = 'Nama item wajib diisi.';
    }
    if (!in_array($form['kategori'], ['Kostum Tari', 'Properti Tari'], true)) {
        $errors[] = 'Kategori tidak valid.';
    }
    if (!is_numeric($form['harga_sewa_per_hari']) || (float)$form['harga_sewa_per_hari'] < 0) {
        $errors[] = 'Harga sewa harus berupa angka 0 atau lebih.';
    }
    if (!ctype_digit($form['stok'])) {
        $errors[] = 'Stok harus berupa bilangan bulat 0 atau lebih.';
    }

    $foto_baru = null;
    if (!$errors && !empty($_FILES['foto']['name'])) {
        $up = upload_image($_FILES['foto']);
        if ($up['ok']) {
            $foto_baru = $up['path'];
        } else {
            $errors[] = $up['error'];
        }
    }

    if (!$errors) {
        $data = [
            'nama' => $form['nama'], 'kategori' => $form['kategori'], 'deskripsi' => $form['deskripsi'],
            'harga' => $form['harga_sewa_per_hari'], 'stok' => (int)$form['stok'],
        ];
        if ($lama) {
            $data['foto'] = $foto_baru ?? $lama['foto'];
            $data['id']   = $form['id'];
            $pdo->prepare('UPDATE items SET nama=:nama, kategori=:kategori, deskripsi=:deskripsi, harga_sewa_per_hari=:harga, stok=:stok, foto=:foto WHERE id=:id')
                ->execute($data);
            if ($foto_baru) {
                delete_upload($lama['foto']);
            }
            flash('success', 'Item diperbarui.');
        } else {
            $data['foto'] = $foto_baru;
            $pdo->prepare('INSERT INTO items (nama, kategori, deskripsi, harga_sewa_per_hari, stok, foto) VALUES (:nama, :kategori, :deskripsi, :harga, :stok, :foto)')
                ->execute($data);
            flash('success', 'Item ditambahkan.');
        }
        redirect('katalog.php');
    }
}

/* ----- TAMPILAN ----- */
$aksi = $_GET['aksi'] ?? 'daftar';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $form['id'] > 0 ? 'ubah' : 'tambah';
} elseif ($aksi === 'ubah') {
    $stmt = $pdo->prepare('SELECT * FROM items WHERE id = :id');
    $stmt->execute(['id' => (int)($_GET['id'] ?? 0)]);
    $form = $stmt->fetch() ?: redirect('katalog.php');
}

require __DIR__ . '/../includes/admin_header.php';

if ($aksi === 'tambah' || $aksi === 'ubah'): ?>
    <h1 class="admin-title"><?= $aksi === 'ubah' ? 'Ubah item' : 'Tambah item' ?></h1>
    <?php foreach ($errors as $err): ?><div class="alert alert--error" role="alert"><?= e($err) ?></div><?php endforeach; ?>
    <form method="post" action="katalog.php" enctype="multipart/form-data" class="form form--admin">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="simpan">
        <input type="hidden" name="id" value="<?= (int)$form['id'] ?>">
        <div class="field">
            <label for="nama">Nama item</label>
            <input type="text" id="nama" name="nama" value="<?= e($form['nama']) ?>" required>
        </div>
        <div class="field">
            <label for="kategori">Kategori</label>
            <select id="kategori" name="kategori">
                <?php foreach (['Kostum Tari', 'Properti Tari'] as $kat): ?>
                    <option value="<?= e($kat) ?>"<?= $form['kategori'] === $kat ? ' selected' : '' ?>><?= e($kat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="4"><?= e($form['deskripsi']) ?></textarea>
        </div>
        <div class="field-row">
            <div class="field">
                <label for="harga_sewa_per_hari">Harga sewa per hari (Rp)</label>
                <input type="number" id="harga_sewa_per_hari" name="harga_sewa_per_hari" min="0" step="1000" value="<?= e($form['harga_sewa_per_hari']) ?>" required>
            </div>
            <div class="field">
                <label for="stok">Stok</label>
                <input type="number" id="stok" name="stok" min="0" value="<?= e($form['stok']) ?>" required>
            </div>
        </div>
        <div class="field">
            <label for="foto">Foto (JPG, PNG, WebP, maks. 4 MB)</label>
            <?php if ($form['foto']): ?><div class="thumb-preview"><?= photo($form['foto'], $form['nama']) ?></div><?php endif; ?>
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="btn-row">
            <button type="submit" class="btn btn--green">Simpan</button>
            <a class="btn btn--outline" href="katalog.php">Batal</a>
        </div>
    </form>
<?php else:
    $rows = $pdo->query('SELECT * FROM items ORDER BY nama ASC')->fetchAll(); ?>
    <div class="admin-bar">
        <h1 class="admin-title">Katalog sewa</h1>
        <a class="btn btn--green btn--sm" href="katalog.php?aksi=tambah">Tambah item</a>
    </div>
    <?php if (!$rows): ?>
        <p class="empty">Belum ada item katalog.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Foto</th><th>Nama</th><th>Harga / hari</th><th>Stok</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td class="td-thumb"><?= photo($r['foto'], $r['nama']) ?></td>
                        <td><?= e($r['nama']) ?><br><span class="meta"><?= e($r['kategori']) ?></span></td>
                        <td><?= e(rupiah($r['harga_sewa_per_hari'])) ?></td>
                        <td><?= (int)$r['stok'] ?></td>
                        <td class="td-actions">
                            <a class="btn btn--outline btn--sm" href="katalog.php?aksi=ubah&amp;id=<?= (int)$r['id'] ?>">Ubah</a>
                            <form method="post" action="katalog.php" data-confirm="Hapus item &quot;<?= e($r['nama']) ?>&quot;? Permintaan sewa terkait ikut terhapus.">
                                <?= csrf_field() ?>
                                <input type="hidden" name="aksi" value="hapus">
                                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <button type="submit" class="btn btn--danger btn--sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endif;
require __DIR__ . '/../includes/admin_footer.php';
