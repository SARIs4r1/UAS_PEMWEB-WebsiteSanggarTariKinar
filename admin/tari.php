<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$base_url     = '../';
$page_title   = 'Kelola tari';
$admin_active = 'tari';
require_role('admin', $base_url);

$errors = [];
$form   = ['id' => 0, 'nama' => '', 'asal' => '', 'deskripsi' => '', 'foto' => null];

/* ----- HAPUS (POST) ----- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
    csrf_verify();
    $stmt = $pdo->prepare('SELECT foto FROM tari WHERE id = :id');
    $stmt->execute(['id' => (int)($_POST['id'] ?? 0)]);
    if ($row = $stmt->fetch()) {
        delete_upload($row['foto']);
        $pdo->prepare('DELETE FROM tari WHERE id = :id')->execute(['id' => (int)$_POST['id']]);
        flash('success', 'Tari dihapus.');
    }
    redirect('tari.php');
}

/* ----- SIMPAN: tambah / ubah (POST) ----- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'simpan') {
    csrf_verify();
    $form['id']        = (int)($_POST['id'] ?? 0);
    $form['nama']      = trim($_POST['nama'] ?? '');
    $form['asal']      = trim($_POST['asal'] ?? '');
    $form['deskripsi'] = trim($_POST['deskripsi'] ?? '');

    $lama = null;
    if ($form['id'] > 0) {
        $stmt = $pdo->prepare('SELECT * FROM tari WHERE id = :id');
        $stmt->execute(['id' => $form['id']]);
        $lama = $stmt->fetch();
        if (!$lama) {
            redirect('tari.php');
        }
        $form['foto'] = $lama['foto'];
    }

    if ($form['nama'] === '') {
        $errors[] = 'Nama tari wajib diisi.';
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
        if ($lama) {
            $foto = $foto_baru ?? $lama['foto'];
            $pdo->prepare('UPDATE tari SET nama=:nama, asal=:asal, deskripsi=:deskripsi, foto=:foto WHERE id=:id')
                ->execute(['nama' => $form['nama'], 'asal' => $form['asal'], 'deskripsi' => $form['deskripsi'], 'foto' => $foto, 'id' => $form['id']]);
            if ($foto_baru) {
                delete_upload($lama['foto']);
            }
            flash('success', 'Tari diperbarui.');
        } else {
            $pdo->prepare('INSERT INTO tari (nama, asal, deskripsi, foto) VALUES (:nama, :asal, :deskripsi, :foto)')
                ->execute(['nama' => $form['nama'], 'asal' => $form['asal'], 'deskripsi' => $form['deskripsi'], 'foto' => $foto_baru]);
            flash('success', 'Tari ditambahkan.');
        }
        redirect('tari.php');
    }
}

/* ----- TAMPILAN: daftar atau form ----- */
$aksi = $_GET['aksi'] ?? 'daftar';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $form['id'] > 0 ? 'ubah' : 'tambah';
} elseif ($aksi === 'ubah') {
    $stmt = $pdo->prepare('SELECT * FROM tari WHERE id = :id');
    $stmt->execute(['id' => (int)($_GET['id'] ?? 0)]);
    $form = $stmt->fetch() ?: redirect('tari.php');
}

require __DIR__ . '/../includes/admin_header.php';

if ($aksi === 'tambah' || $aksi === 'ubah'): ?>
    <h1 class="admin-title"><?= $aksi === 'ubah' ? 'Ubah tari' : 'Tambah tari' ?></h1>
    <?php foreach ($errors as $err): ?><div class="alert alert--error" role="alert"><?= e($err) ?></div><?php endforeach; ?>
    <form method="post" action="tari.php" enctype="multipart/form-data" class="form form--admin">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="simpan">
        <input type="hidden" name="id" value="<?= (int)$form['id'] ?>">
        <div class="field">
            <label for="nama">Nama tari</label>
            <input type="text" id="nama" name="nama" value="<?= e($form['nama']) ?>" required>
        </div>
        <div class="field">
            <label for="asal">Asal / keterangan singkat</label>
            <input type="text" id="asal" name="asal" value="<?= e($form['asal']) ?>" placeholder="Contoh: Melayu Sambas">
        </div>
        <div class="field">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="5"><?= e($form['deskripsi']) ?></textarea>
        </div>
        <div class="field">
            <label for="foto">Foto (JPG, PNG, WebP, maks. 4 MB)</label>
            <?php if ($form['foto']): ?><div class="thumb-preview"><?= photo($form['foto'], $form['nama']) ?></div><?php endif; ?>
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="btn-row">
            <button type="submit" class="btn btn--green">Simpan</button>
            <a class="btn btn--outline" href="tari.php">Batal</a>
        </div>
    </form>
<?php else:
    $rows = $pdo->query('SELECT * FROM tari ORDER BY id ASC')->fetchAll(); ?>
    <div class="admin-bar">
        <h1 class="admin-title">Tari</h1>
        <a class="btn btn--green btn--sm" href="tari.php?aksi=tambah">Tambah tari</a>
    </div>
    <?php if (!$rows): ?>
        <p class="empty">Belum ada tari. Klik &quot;Tambah tari&quot; untuk mulai.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Foto</th><th>Nama</th><th>Asal</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td class="td-thumb"><?= photo($r['foto'], $r['nama']) ?></td>
                        <td><?= e($r['nama']) ?></td>
                        <td><?= e($r['asal']) ?></td>
                        <td class="td-actions">
                            <a class="btn btn--outline btn--sm" href="tari.php?aksi=ubah&amp;id=<?= (int)$r['id'] ?>">Ubah</a>
                            <form method="post" action="tari.php" data-confirm="Hapus tari &quot;<?= e($r['nama']) ?>&quot;?">
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
