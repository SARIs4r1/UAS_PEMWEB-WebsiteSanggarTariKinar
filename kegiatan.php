<?php
require_once __DIR__ . '/includes/bootstrap.php';

$base_url = '';
$active   = 'kegiatan';
$id       = (int)($_GET['id'] ?? 0);

/* ----- Detail satu kegiatan ----- */
if ($id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM kegiatan WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $k = $stmt->fetch();

    if (!$k) {
        http_response_code(404);
        $page_title = 'Kegiatan tidak ditemukan';
        require __DIR__ . '/includes/header.php';
        page_hero('Kegiatan tidak ditemukan');
        echo '<section class="section"><div class="container"><p>Kegiatan yang kamu cari tidak ada. <a href="kegiatan.php">Kembali ke daftar kegiatan</a>.</p></div></section>';
        require __DIR__ . '/includes/footer.php';
        exit;
    }

    $page_title = $k['judul'];
    require __DIR__ . '/includes/header.php';
    page_hero($k['judul'], tanggal_id($k['tanggal']) . ($k['lokasi'] ? ' di ' . $k['lokasi'] : ''));
    ?>
    <section class="section">
        <div class="container narrow">
            <?php if ($k['foto']): ?><div class="article-media"><?= photo($k['foto'], $k['judul'], 'ratio-169') ?></div><?php endif; ?>
            <?php if ($k['ringkasan']): ?><p class="statement"><?= e($k['ringkasan']) ?></p><?php endif; ?>
            <div class="prose"><?= nl2br(e((string)$k['isi'])) ?></div>
            <p><a class="link-arrow" href="kegiatan.php">Kembali ke semua kegiatan</a></p>
        </div>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

/* ----- Daftar kegiatan ----- */
$page_title = 'Kegiatan';
$kegiatan   = $pdo->query('SELECT * FROM kegiatan ORDER BY tanggal DESC, id DESC')->fetchAll();

require __DIR__ . '/includes/header.php';
page_hero('Kegiatan sanggar', 'Latihan, pentas, dan acara yang kami ikuti.');
?>
<section class="section">
    <div class="container">
        <?php if (!$kegiatan): ?>
            <p class="empty">Belum ada kegiatan yang dipublikasikan.</p>
        <?php else: ?>
            <div class="agenda">
                <?php foreach ($kegiatan as $k): ?>
                    <a class="agenda-row" href="kegiatan.php?id=<?= (int)$k['id'] ?>">
                        <time class="agenda-date" datetime="<?= e($k['tanggal']) ?>">
                            <span class="agenda-day"><?= e(date('j', strtotime($k['tanggal']))) ?></span>
                            <span class="agenda-month"><?= e(substr(tanggal_id($k['tanggal']), strpos(tanggal_id($k['tanggal']), ' ') + 1)) ?></span>
                        </time>
                        <div class="agenda-text">
                            <h3><?= e($k['judul']) ?></h3>
                            <?php if ($k['lokasi']): ?><p class="meta"><?= e($k['lokasi']) ?></p><?php endif; ?>
                            <?php if ($k['ringkasan']): ?><p><?= e($k['ringkasan']) ?></p><?php endif; ?>
                        </div>
                        <div class="agenda-thumb"><?= photo($k['foto'], $k['judul'], 'ratio-43') ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
