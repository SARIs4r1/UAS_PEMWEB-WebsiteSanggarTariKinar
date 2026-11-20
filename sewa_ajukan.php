<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$base_url = '';
require_login();

$user = current_user();
if ($user['role'] !== 'pelanggan') {
    header('Location: index.php');
    exit;
}

$item_id = (int)($_GET['item_id'] ?? $_POST['item_id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM items WHERE id = :id');
$stmt->execute(['id' => $item_id]);
$item = $stmt->fetch();

if (!$item) {
    http_response_code(404);
    die('Item tidak ditemukan.');
}

$page_title = 'Ajukan Sewa';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal_mulai   = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    $jumlah          = (int)($_POST['jumlah'] ?? 1);
    $catatan         = trim($_POST['catatan'] ?? '');

    if ($tanggal_mulai === '' || $tanggal_selesai === '') {
        $errors[] = 'Tanggal mulai dan tanggal selesai wajib diisi.';
    } elseif (strtotime($tanggal_selesai) < strtotime($tanggal_mulai)) {
        $errors[] = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
    }
    if ($jumlah < 1) {
        $errors[] = 'Jumlah minimal 1.';
    }

    if (empty($errors)) {
        $insert = $pdo->prepare(
            'INSERT INTO rental_requests (user_id, item_id, tanggal_mulai, tanggal_selesai, jumlah, catatan, status)
             VALUES (:user_id, :item_id, :tanggal_mulai, :tanggal_selesai, :jumlah, :catatan, "menunggu")'
        );
        $insert->execute([
            'user_id'         => $user['id'],
            'item_id'         => $item['id'],
            'tanggal_mulai'   => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'jumlah'          => $jumlah,
            'catatan'         => $catatan,
        ]);

        $_SESSION['flash_success'] = 'Permintaan sewa berhasil diajukan. Menunggu persetujuan admin.';
        header('Location: sewa_saya.php');
        exit;
    }
}

require __DIR__ . '/includes/header.php';
?>

<h1>Ajukan Sewa: <?= htmlspecialchars($item['nama']) ?></h1>

<?php foreach ($errors as $err): ?>
    <div class="alert alert-error"><?= htmlspecialchars($err) ?></div>
<?php endforeach; ?>

<form class="form-box" method="post" action="sewa_ajukan.php">
    <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">
    <div class="form-group">
        <label for="tanggal_mulai">Tanggal Mulai</label>
        <input type="date" id="tanggal_mulai" name="tanggal_mulai" required>
    </div>
    <div class="form-group">
        <label for="tanggal_selesai">Tanggal Selesai</label>
        <input type="date" id="tanggal_selesai" name="tanggal_selesai" required>
    </div>
    <div class="form-group">
        <label for="jumlah">Jumlah</label>
        <input type="number" id="jumlah" name="jumlah" min="1" max="<?= (int)$item['stok'] ?>" value="1" required>
    </div>
    <div class="form-group">
        <label for="catatan">Catatan (opsional)</label>
        <textarea id="catatan" name="catatan" rows="3"></textarea>
    </div>
    <button type="submit" class="btn">Ajukan Sewa</button>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
