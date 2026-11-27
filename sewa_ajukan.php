<?php
require_once __DIR__ . '/includes/bootstrap.php';

$base_url     = '';
$active       = 'sewa';
$header_solid = true;
require_login();

$user = current_user();
if ($user['role'] !== 'pelanggan') {
    redirect('index.php');
}

$item_id = (int)($_GET['item_id'] ?? $_POST['item_id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM items WHERE id = :id');
$stmt->execute(['id' => $item_id]);
$item = $stmt->fetch();

if (!$item) {
    http_response_code(404);
    exit('Item tidak ditemukan.');
}

$page_title      = 'Ajukan sewa';
$errors          = [];
$tanggal_mulai   = '';
$tanggal_selesai = '';
$jumlah          = 1;
$catatan         = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $tanggal_mulai   = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    $jumlah          = (int)($_POST['jumlah'] ?? 1);
    $catatan         = trim($_POST['catatan'] ?? '');

    if ($tanggal_mulai === '' || $tanggal_selesai === '') {
        $errors[] = 'Tanggal mulai dan selesai wajib diisi.';
    } elseif (strtotime($tanggal_selesai) < strtotime($tanggal_mulai)) {
        $errors[] = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
    } elseif (strtotime($tanggal_mulai) < strtotime(date('Y-m-d'))) {
        $errors[] = 'Tanggal mulai tidak boleh sudah lewat.';
    }
    if ($jumlah < 1) {
        $errors[] = 'Jumlah minimal 1.';
    } elseif ($jumlah > (int)$item['stok']) {
        $errors[] = 'Jumlah melebihi stok yang tersedia (' . (int)$item['stok'] . ').';
    }

    if (!$errors) {
        $insert = $pdo->prepare(
            "INSERT INTO rental_requests (user_id, item_id, tanggal_mulai, tanggal_selesai, jumlah, catatan, status)
             VALUES (:user_id, :item_id, :mulai, :selesai, :jumlah, :catatan, 'menunggu')"
        );
        $insert->execute([
            'user_id' => $user['id'],
            'item_id' => $item['id'],
            'mulai'   => $tanggal_mulai,
            'selesai' => $tanggal_selesai,
            'jumlah'  => $jumlah,
            'catatan' => $catatan,
        ]);
        flash('success', 'Permintaan sewa terkirim. Tunggu persetujuan admin.');
        redirect('sewa_saya.php');
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="section section--top">
    <div class="container narrow">
        <h1 class="h2">Ajukan sewa: <?= e($item['nama']) ?></h1>
        <p class="meta"><?= e(rupiah($item['harga_sewa_per_hari'])) ?> / hari, stok <?= (int)$item['stok'] ?></p>

        <?php foreach ($errors as $err): ?><div class="alert alert--error" role="alert"><?= e($err) ?></div><?php endforeach; ?>

        <form method="post" action="sewa_ajukan.php" class="form">
            <?= csrf_field() ?>
            <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">
            <div class="field-row">
                <div class="field">
                    <label for="tanggal_mulai">Tanggal mulai</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" min="<?= e(date('Y-m-d')) ?>" value="<?= e($tanggal_mulai) ?>" required>
                </div>
                <div class="field">
                    <label for="tanggal_selesai">Tanggal selesai</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" min="<?= e(date('Y-m-d')) ?>" value="<?= e($tanggal_selesai) ?>" required>
                </div>
            </div>
            <div class="field">
                <label for="jumlah">Jumlah</label>
                <input type="number" id="jumlah" name="jumlah" min="1" max="<?= (int)$item['stok'] ?>" value="<?= (int)$jumlah ?>" required>
            </div>
            <div class="field">
                <label for="catatan">Catatan (boleh dikosongkan)</label>
                <textarea id="catatan" name="catatan" rows="3"><?= e($catatan) ?></textarea>
            </div>
            <div class="btn-row">
                <button type="submit" class="btn btn--green">Kirim permintaan</button>
                <a class="btn btn--outline" href="katalog_detail.php?id=<?= (int)$item['id'] ?>">Batal</a>
            </div>
        </form>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
