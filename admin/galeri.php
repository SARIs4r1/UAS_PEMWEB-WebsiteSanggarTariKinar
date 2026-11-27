<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$base_url     = '../';
$page_title   = 'Kelola galeri';
$admin_active = 'galeri';
require_role('admin', $base_url);

$errors = [];

/* ----- HAPUS (POST) ----- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
    csrf_verify();
    $stmt = $pdo->prepare('SELECT foto FROM galeri WHERE id = :id');
    $stmt->execute(['id' => (int)($_POST['id'] ?? 0)]);
    if ($row = $stmt->fetch()) {
        delete_upload($row['foto']);
        $pdo->prepare('DELETE FROM galeri WHERE id = :id')->execute(['id' => (int)$_POST['id']]);
        flash('success', 'Foto dihapus.');
    }
    redirect('galeri.php');
}

/* ----- TAMBAH FOTO (POST) - bisa banyak file sekaligus ----- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'tambah') {
    csrf_verify();
    $judul = trim($_POST['judul'] ?? '');
    $files = $_FILES['foto'] ?? null;
    $ok    = 0;

    if (!$files || empty($files['name'][0])) {
        $errors[] = 'Pilih minimal satu foto.';
    } else {
        $insert = $pdo->prepare('INSERT INTO galeri (judul, foto) VALUES (:judul, :foto)');
        foreach ($files['name'] as $i => $nama_file) {
            $up = upload_image([
                'name'     => $nama_file,
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ]);
            if ($up['ok']) {
                $insert->execute(['judul' => $judul !== '' ? $judul : null, 'foto' => $up['path']]);
                $ok++;
            } else {
                $errors[] = $nama_file . ': ' . $up['error'];
            }
        }
    }
    if ($ok > 0 && !$errors) {
        flash('success', $ok . ' foto ditambahkan.');
        redirect('galeri.php');
    }
    if ($ok > 0) {
        flash('success', $ok . ' foto berhasil ditambahkan, sebagian gagal.');
    }
}

$rows = $pdo->query('SELECT * FROM galeri ORDER BY id DESC')->fetchAll();

require __DIR__ . '/../includes/admin_header.php';
?>
<h1 class="admin-title">Galeri foto</h1>
<?php foreach ($errors as $err): ?><div class="alert alert--error" role="alert"><?= e($err) ?></div><?php endforeach; ?>

<form method="post" action="galeri.php" enctype="multipart/form-data" class="form form--admin form--inline-card">
    <?= csrf_field() ?>
    <input type="hidden" name="aksi" value="tambah">
    <div class="field">
        <label for="foto">Pilih foto (boleh lebih dari satu; JPG, PNG, WebP, maks. 4 MB per foto)</label>
        <input type="file" id="foto" name="foto[]" accept="image/jpeg,image/png,image/webp" multiple required>
    </div>
    <div class="field">
        <label for="judul">Keterangan foto (boleh dikosongkan)</label>
        <input type="text" id="judul" name="judul" maxlength="150">
    </div>
    <button type="submit" class="btn btn--green">Unggah</button>
</form>

<?php if (!$rows): ?>
    <p class="empty">Belum ada foto.</p>
<?php else: ?>
    <div class="admin-gallery">
        <?php foreach ($rows as $r): ?>
            <figure class="admin-gallery-item">
                <?= photo($r['foto'], $r['judul'] ?: 'Foto galeri') ?>
                <figcaption>
                    <span><?= e($r['judul'] ?: 'Tanpa keterangan') ?></span>
                    <form method="post" action="galeri.php" data-confirm="Hapus foto ini?">
                        <?= csrf_field() ?>
                        <input type="hidden" name="aksi" value="hapus">
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button type="submit" class="btn btn--danger btn--sm">Hapus</button>
                    </form>
                </figcaption>
            </figure>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
