<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$base_url     = '../';
$page_title   = 'Pengaturan situs';
$admin_active = 'pengaturan';
require_role('admin', $base_url);

$teks = ['nama_sanggar', 'tagline', 'tentang', 'visi', 'misi', 'jadwal_latihan', 'alamat', 'whatsapp', 'email', 'instagram'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $nilai = [];
    foreach ($teks as $k) {
        $nilai[$k] = trim($_POST[$k] ?? '');
    }
    if ($nilai['nama_sanggar'] === '') {
        $errors[] = 'Nama sanggar wajib diisi.';
    }
    if ($nilai['email'] !== '' && !filter_var($nilai['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }
    if ($nilai['whatsapp'] !== '' && !preg_match('/^[0-9+\s-]{8,20}$/', $nilai['whatsapp'])) {
        $errors[] = 'Nomor WhatsApp hanya boleh berisi angka, spasi, + atau -.';
    }

    // Foto hero & foto tentang (opsional)
    foreach (['hero_foto', 'about_foto'] as $kolom) {
        if (!$errors && !empty($_FILES[$kolom]['name'])) {
            $up = upload_image($_FILES[$kolom]);
            if ($up['ok']) {
                delete_upload(settings_all()[$kolom] ?? null);
                $nilai[$kolom] = $up['path'];
            } else {
                $errors[] = $up['error'];
            }
        }
        if (!empty($_POST['hapus_' . $kolom]) && !isset($nilai[$kolom])) {
            delete_upload(settings_all()[$kolom] ?? null);
            $nilai[$kolom] = '';
        }
    }

    if (!$errors) {
        $upsert = $pdo->prepare('INSERT INTO settings (k, v) VALUES (:k, :v) ON DUPLICATE KEY UPDATE v = VALUES(v)');
        foreach ($nilai as $k => $v) {
            $upsert->execute(['k' => $k, 'v' => $v]);
        }
        flash('success', 'Pengaturan disimpan.');
        redirect('pengaturan.php');
    }
}

$s = settings_all();
$v = function (string $k) use ($s): string {
    return isset($_POST[$k]) && $_SERVER['REQUEST_METHOD'] === 'POST' ? (string)$_POST[$k] : ($s[$k] ?? '');
};

require __DIR__ . '/../includes/admin_header.php';
?>
<h1 class="admin-title">Pengaturan situs</h1>
<p class="meta">Teks dan foto utama yang tampil di beranda dan halaman profil.</p>
<?php foreach ($errors as $err): ?><div class="alert alert--error" role="alert"><?= e($err) ?></div><?php endforeach; ?>

<form method="post" action="pengaturan.php" enctype="multipart/form-data" class="form form--admin">
    <?= csrf_field() ?>

    <h2 class="admin-sub">Identitas</h2>
    <div class="field">
        <label for="nama_sanggar">Nama sanggar</label>
        <input type="text" id="nama_sanggar" name="nama_sanggar" value="<?= e($v('nama_sanggar')) ?>" required>
    </div>
    <div class="field">
        <label for="tagline">Kalimat pembuka (tampil di beranda)</label>
        <input type="text" id="tagline" name="tagline" value="<?= e($v('tagline')) ?>">
    </div>

    <h2 class="admin-sub">Foto utama</h2>
    <?php foreach (['hero_foto' => 'Foto latar beranda (disarankan landscape, minimal 1600 px lebar)', 'about_foto' => 'Foto bagian tentang kami (disarankan potret)'] as $kolom => $label): ?>
        <div class="field">
            <label for="<?= e($kolom) ?>"><?= e($label) ?></label>
            <?php if (!empty($s[$kolom])): ?>
                <div class="thumb-preview"><?= photo($s[$kolom], $label) ?></div>
                <label class="check"><input type="checkbox" name="hapus_<?= e($kolom) ?>" value="1"> Hapus foto ini</label>
            <?php endif; ?>
            <input type="file" id="<?= e($kolom) ?>" name="<?= e($kolom) ?>" accept="image/jpeg,image/png,image/webp">
        </div>
    <?php endforeach; ?>

    <h2 class="admin-sub">Profil</h2>
    <div class="field">
        <label for="tentang">Tentang sanggar (pisahkan paragraf dengan baris kosong)</label>
        <textarea id="tentang" name="tentang" rows="7"><?= e($v('tentang')) ?></textarea>
    </div>
    <div class="field">
        <label for="visi">Visi</label>
        <textarea id="visi" name="visi" rows="3"><?= e($v('visi')) ?></textarea>
    </div>
    <div class="field">
        <label for="misi">Misi (satu poin per baris)</label>
        <textarea id="misi" name="misi" rows="5"><?= e($v('misi')) ?></textarea>
    </div>

    <h2 class="admin-sub">Latihan dan kontak</h2>
    <div class="field">
        <label for="jadwal_latihan">Jadwal latihan</label>
        <input type="text" id="jadwal_latihan" name="jadwal_latihan" value="<?= e($v('jadwal_latihan')) ?>" placeholder="Contoh: Setiap Sabtu, 15.00 WIB">
    </div>
    <div class="field">
        <label for="alamat">Alamat pusat latihan</label>
        <input type="text" id="alamat" name="alamat" value="<?= e($v('alamat')) ?>">
    </div>
    <div class="field-row">
        <div class="field">
            <label for="whatsapp">Nomor WhatsApp</label>
            <input type="text" id="whatsapp" name="whatsapp" value="<?= e($v('whatsapp')) ?>" placeholder="08xxxxxxxxxx">
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($v('email')) ?>">
        </div>
    </div>
    <div class="field">
        <label for="instagram">Instagram</label>
        <input type="text" id="instagram" name="instagram" value="<?= e($v('instagram')) ?>" placeholder="@akun_sanggar">
    </div>

    <button type="submit" class="btn btn--green">Simpan pengaturan</button>
</form>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
