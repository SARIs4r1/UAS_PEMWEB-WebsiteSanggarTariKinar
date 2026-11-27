<?php
require_once __DIR__ . '/includes/bootstrap.php';

$base_url   = '';
$page_title = 'Galeri';
$active     = 'galeri';

$galeri = $pdo->query('SELECT * FROM galeri ORDER BY id DESC')->fetchAll();

require __DIR__ . '/includes/header.php';
page_hero('Galeri', 'Foto latihan dan pementasan Sanggar Tari Kinar.');
?>
<section class="section">
    <div class="container">
        <?php if (!$galeri): ?>
            <p class="empty">Foto belum ditambahkan.</p>
        <?php else: ?>
            <div class="gallery gallery--page">
                <?php foreach ($galeri as $g): ?>
                    <button type="button" class="gallery-item" data-full="<?= e($g['foto']) ?>" data-alt="<?= e($g['judul'] ?: 'Foto Sanggar Tari Kinar') ?>">
                        <?= photo($g['foto'], $g['judul'] ?: 'Foto Sanggar Tari Kinar') ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
