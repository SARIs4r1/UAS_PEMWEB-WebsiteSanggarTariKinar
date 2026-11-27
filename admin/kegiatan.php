<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$base_url     = '../';
$page_title   = 'Kelola kegiatan';
$admin_active = 'kegiatan';
require_role('admin', $base_url);

$errors = [];
$form   = ['id' => 0, 'judul' => '', 'tanggal' => date('Y-m-d'), 'lokasi' => '', 'ringkasan' => '', 'isi' => '', 'foto' => null];

/* ----- HAPUS (POST) ----- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
    csrf_verify();
    $stmt = $pdo->prepare('SELECT foto FROM kegiatan WHERE id = :id');
    $stmt->execute(['id' => (int)($_POST['id'] ?? 0)]);
    if ($row = $stmt->fetch()) {
        delete_upload($row['foto']);
        $pdo->prepare('DELETE FROM kegiatan WHERE id = :id')->execute(['id' => (int)$_POST['id']]);
        flash('success', 'Kegiatan dihapus.');
    }
    redirect('kegiatan.php');
}

/* ----- SIMPAN (POST) ----- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'simpan') {
    csrf_verify();
    $form['id']        = (int)($_POST['id'] ?? 0);
    $form['judul']     = trim($_POST['judul'] ?? '');
    $form['tanggal']   = $_POST['tanggal'] ?? '';
    $form['lokasi']    = trim($_POST['lokasi'] ?? '');
    $form['ringkasan'] = trim($_POST['ringkasan'] ?? '');
    $form['isi']       = trim($_POST['isi'] ?? '');

    $lama = null;
    if ($form['id'] > 0) {
        $stmt = $pdo->prepare('SELECT * FROM kegiatan WHERE id = :id');
        $stmt->execute(['id' => $form['id']]);
        $lama = $stmt->fetch();
        if (!$lama) {
            redirect('kegiatan.php');
        }
        $form['foto'] = $lama['foto'];
    }

    if ($form['judul'] === '') {
        $errors[] = 'Judul wajib diisi.';
    }
    $d = DateTime::createFromFormat('Y-m-d', $form['tanggal']);
    if (!$d || $d->format('Y-m-d') !== $form['tanggal']) {
        $errors[] = 'Tanggal tidak valid.';
    }
    if (mb_strlen($form['ringkasan']) > 300) {
        $errors[] = 'Ringkasan maksimal 300 karakter.';
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
            'judul' => $form['judul'], 'tanggal' => $form['tanggal'], 'lokasi' => $form['lokasi'],
            'ringkasan' => $form['ringkasan'], 'isi' => $form['isi'],
        ];
        if ($lama) {
            $data['foto'] = $foto_baru ?? $lama['foto'];
            $data['id']   = $form['id'];
            $pdo->prepare('UPDATE kegiatan SET judul=:judul, tanggal=:tanggal, lokasi=:lokasi, ringkasan=:ringkasan, isi=:isi, foto=:foto WHERE id=:id')
                ->execute($data);
            if ($foto_baru) {
                delete_upload($lama['foto']);
            }
            flash('success', 'Kegiatan diperbarui.');
        } else {
            $data['foto'] = $foto_baru;
            $pdo->prepare('INSERT INTO kegiatan (judul, tanggal, lokasi, ringkasan, isi, foto) VALUES (:judul, :tanggal, :lokasi, :ringkasan, :isi, :foto)')
                ->execute($data);
            flash('success', 'Kegiatan ditambahkan.');
        }
        redirect('kegiatan.php');
    }
}

/* ----- TAMPILAN ----- */
$aksi = $_GET['aksi'] ?? 'daftar';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $form['id'] > 0 ? 'ubah' : 'tambah';
} elseif ($aksi === 'ubah') {
    $stmt = $pdo->prepare('SELECT * FROM kegiatan WHERE id = :id');
    $stmt->execute(['id' => (int)($_GET['id'] ?? 0)]);
    $form = $stmt->fetch() ?: redirect('kegiatan.php');
}

require __DIR__ . '/../includes/admin_header.php';

if ($aksi === 'tambah' || $aksi === 'ubah'): ?>
    <h1 class="admin-title"><?= $aksi === 'ubah' ? 'Ubah kegiatan' : 'Tambah kegiatan' ?></h1>
    <?php foreach ($errors as $err): ?><div class="alert alert--error" role="alert"><?= e($err) ?></div><?php endforeach; ?>
    <form method="post" action="kegiatan.php" enctype="multipart/form-data" class="form form--admin">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="simpan">
        <input type="hidden" name="id" value="<?= (int)$form['id'] ?>">
        <div class="field">
            <label for="judul">Judul</label>
            <input type="text" id="judul" name="judul" value="<?= e($form['judul']) ?>" required>
        </div>
        <div class="field-row">
            <div class="field">
                <label for="tanggal">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" value="<?= e($form['tanggal']) ?>" required>
            </div>
            <div class="field">
                <label for="lokasi">Lokasi</label>
                <input type="text" id="lokasi" name="lokasi" value="<?= e($form['lokasi']) ?>">
            </div>
        </div>
        <div class="field">
            <label for="ringkasan">Ringkasan (maks. 300 karakter)</label>
            <input type="text" id="ringkasan" name="ringkasan" maxlength="300" value="<?= e($form['ringkasan']) ?>">
        </div>
        <div class="field">
            <label for="isi">Isi kegiatan</label>
            <textarea id="isi" name="isi" rows="7"><?= e($form['isi']) ?></textarea>
        </div>
        <div class="field">
            <label for="foto">Foto (JPG, PNG, WebP, maks. 4 MB)</label>
            <?php if ($form['foto']): ?><div class="thumb-preview"><?= photo($form['foto'], $form['judul']) ?></div><?php endif; ?>
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="btn-row">
            <button type="submit" class="btn btn--green">Simpan</button>
            <a class="btn btn--outline" href="kegiatan.php">Batal</a>
        </div>
    </form>
<?php else:
    $rows = $pdo->query('SELECT * FROM kegiatan ORDER BY tanggal DESC, id DESC')->fetchAll(); ?>
    <div class="admin-bar">
        <h1 class="admin-title">Kegiatan</h1>
        <a class="btn btn--green btn--sm" href="kegiatan.php?aksi=tambah">Tambah kegiatan</a>
    </div>
    <?php if (!$rows): ?>
        <p class="empty">Belum ada kegiatan. Klik &quot;Tambah kegiatan&quot; untuk mulai.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Foto</th><th>Judul</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td class="td-thumb"><?= photo($r['foto'], $r['judul']) ?></td>
                        <td><?= e($r['judul']) ?><br><span class="meta"><?= e($r['lokasi']) ?></span></td>
                        <td><?= e(tanggal_id($r['tanggal'])) ?></td>
                        <td class="td-actions">
                            <a class="btn btn--outline btn--sm" href="kegiatan.php?aksi=ubah&amp;id=<?= (int)$r['id'] ?>">Ubah</a>
                            <form method="post" action="kegiatan.php" data-confirm="Hapus kegiatan &quot;<?= e($r['judul']) ?>&quot;?">
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
