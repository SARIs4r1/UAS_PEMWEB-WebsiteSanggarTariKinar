<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$base_url   = '../';
$page_title = 'Tambah Item Katalog';

require_role('admin', $base_url);

$errors = [];
$nama = $deskripsi = '';
$kategori = 'Kostum Tari';
$harga = $stok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama'] ?? '');
    $kategori  = $_POST['kategori'] ?? '';
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga     = $_POST['harga_sewa_per_hari'] ?? '';
    $stok      = $_POST['stok'] ?? '';

    if ($nama === '') {
        $errors[] = 'Nama item wajib diisi.';
    }
    if (!in_array($kategori, ['Kostum Tari', 'Properti Tari'], true)) {
        $errors[] = 'Kategori tidak valid.';
    }
    if (!is_numeric($harga) || (float)$harga < 0) {
        $errors[] = 'Harga sewa per hari harus berupa angka >= 0.';
    }
    if (!ctype_digit((string)$stok) || (int)$stok < 0) {
        $errors[] = 'Stok harus berupa angka bulat >= 0.';
    }

    if (empty($errors)) {
        $insert = $pdo->prepare(
            'INSERT INTO items (nama, kategori, deskripsi, harga_sewa_per_hari, stok)
             VALUES (:nama, :kategori, :deskripsi, :harga, :stok)'
        );
        $insert->execute([
            'nama'      => $nama,
            'kategori'  => $kategori,
            'deskripsi' => $deskripsi,
            'harga'     => $harga,
            'stok'      => $stok,
        ]);

        $_SESSION['flash_success'] = 'Item katalog berhasil ditambahkan.';
        header('Location: katalog_index.php');
        exit;
    }
}

require __DIR__ . '/../includes/header.php';
?>

<h1>Tambah Item Katalog</h1>

<?php foreach ($errors as $err): ?>
    <div class="alert alert-error"><?= htmlspecialchars($err) ?></div>
<?php endforeach; ?>

<form class="form-box" method="post" action="katalog_create.php">
    <div class="form-group">
        <label for="nama">Nama Item</label>
        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($nama) ?>" required>
    </div>
    <div class="form-group">
        <label for="kategori">Kategori</label>
        <select id="kategori" name="kategori">
            <option value="Kostum Tari" <?= $kategori === 'Kostum Tari' ? 'selected' : '' ?>>Kostum Tari</option>
            <option value="Properti Tari" <?= $kategori === 'Properti Tari' ? 'selected' : '' ?>>Properti Tari</option>
        </select>
    </div>
    <div class="form-group">
        <label for="deskripsi">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" rows="3"><?= htmlspecialchars($deskripsi) ?></textarea>
    </div>
    <div class="form-group">
        <label for="harga_sewa_per_hari">Harga Sewa / Hari (Rp)</label>
        <input type="number" step="0.01" id="harga_sewa_per_hari" name="harga_sewa_per_hari" value="<?= htmlspecialchars((string)$harga) ?>" required>
    </div>
    <div class="form-group">
        <label for="stok">Stok</label>
        <input type="number" id="stok" name="stok" value="<?= htmlspecialchars((string)$stok) ?>" required>
    </div>
    <button type="submit" class="btn">Simpan</button>
    <a href="katalog_index.php" class="btn btn-secondary">Batal</a>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>
