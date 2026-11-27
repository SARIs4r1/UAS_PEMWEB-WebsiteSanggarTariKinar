<?php
require_once __DIR__ . '/includes/bootstrap.php';

$base_url   = '';
$page_title = 'Profil';
$active     = 'profil';

$misi = array_filter(array_map('trim', explode("\n", setting('misi'))));
$wa   = wa_link('Halo Sanggar Tari Kinar, saya ingin bertanya.');
$maps = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode('SMAN 1 Tekarang, Sambas');

require __DIR__ . '/includes/header.php';
page_hero('Profil sanggar', 'Siapa kami, apa yang kami kerjakan, dan di mana kami berlatih.');
?>

<section class="section">
    <div class="container split">
        <div class="split-media"><?= photo(setting('about_foto'), 'Penari Sanggar Tari Kinar', 'ratio-45') ?></div>
        <div class="split-body">
            <h2 class="h2">Tentang kami</h2>
            <div class="prose"><?= nl2br(e(setting('tentang'))) ?></div>
        </div>
    </div>
</section>

<section class="section section--tint">
    <div class="container two-col">
        <div>
            <h2 class="h2">Visi</h2>
            <p class="statement"><?= e(setting('visi')) ?></p>
        </div>
        <div>
            <h2 class="h2">Misi</h2>
            <ul class="check-list">
                <?php foreach ($misi as $m): ?><li><?= e($m) ?></li><?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<section class="section" id="kontak">
    <div class="container two-col">
        <div>
            <h2 class="h2">Pusat latihan dan kontak</h2>
            <dl class="info-list">
                <div><dt>Alamat</dt><dd><?= e(setting('alamat')) ?></dd></div>
                <div><dt>Jadwal latihan</dt><dd><?= e(setting('jadwal_latihan')) ?></dd></div>
                <?php if ($wa): ?><div><dt>WhatsApp</dt><dd><a href="<?= e($wa) ?>" target="_blank" rel="noopener"><?= e(setting('whatsapp')) ?></a></dd></div><?php endif; ?>
                <?php if (setting('email')): ?><div><dt>Email</dt><dd><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></dd></div><?php endif; ?>
                <?php if (setting('instagram')): ?><div><dt>Instagram</dt><dd><?= e(setting('instagram')) ?></dd></div><?php endif; ?>
            </dl>
            <div class="btn-row">
                <a class="btn btn--outline" href="<?= e($maps) ?>" target="_blank" rel="noopener">Buka di Google Maps</a>
            </div>
        </div>
        <div>
            <h2 class="h2">Layanan pelengkap</h2>
            <div class="prose">
                <p>Selain berlatih dan pentas, sanggar juga menyewakan kostum dan properti tari untuk sekolah, pelajar, dan panitia acara.</p>
            </div>
            <a class="link-arrow" href="katalog.php">Lihat katalog sewa</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
