<?php
require_once __DIR__ . '/includes/bootstrap.php';

$base_url   = '';
$page_title = 'Tari';
$active     = 'tari';

$tari = $pdo->query('SELECT * FROM tari ORDER BY id ASC')->fetchAll();

require __DIR__ . '/includes/header.php';
page_hero('Tari yang kami bawakan', 'Dari tari tradisional daerah sampai garapan kreasi sanggar.');
?>

<section class="section">
    <div class="container">
        <?php if (!$tari): ?>
            <p class="empty">Daftar tari belum diisi.</p>
        <?php endif; ?>
        <?php foreach ($tari as $i => $t): ?>
            <article class="feature<?= $i % 2 ? ' feature--flip' : '' ?>" id="tari-<?= (int)$t['id'] ?>">
                <div class="feature-media"><?= photo($t['foto'], $t['nama'], 'ratio-43') ?></div>
                <div class="feature-body">
                    <h2 class="h3"><?= e($t['nama']) ?></h2>
                    <?php if ($t['asal']): ?><p class="meta"><?= e($t['asal']) ?></p><?php endif; ?>
                    <div class="prose"><?= nl2br(e((string)$t['deskripsi'])) ?></div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
