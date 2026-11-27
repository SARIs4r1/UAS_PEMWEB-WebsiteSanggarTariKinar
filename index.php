<?php
require_once __DIR__ . '/includes/bootstrap.php';

$base_url   = '';
$page_title = 'Beranda';
$active     = 'beranda';

$tari     = $pdo->query('SELECT * FROM tari ORDER BY id ASC LIMIT 6')->fetchAll();
$kegiatan = $pdo->query('SELECT * FROM kegiatan ORDER BY tanggal DESC, id DESC LIMIT 3')->fetchAll();
$galeri   = $pdo->query('SELECT * FROM galeri ORDER BY id DESC LIMIT 6')->fetchAll();
$items    = $pdo->query('SELECT * FROM items ORDER BY created_at DESC LIMIT 3')->fetchAll();

$hero_foto  = setting('hero_foto');
$about_foto = setting('about_foto');
$wa         = wa_link('Halo Sanggar Tari Kinar, saya ingin bertanya.');
$maps       = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode('SMAN 1 Tekarang, Sambas');

require __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="hero">
    <div class="hero-bg" aria-hidden="true">
        <?php if ($hero_foto && is_file(__DIR__ . '/' . $hero_foto)): ?>
            <img src="<?= e($hero_foto) ?>" alt="">
        <?php endif; ?>
    </div>
    <div class="container hero-inner">
        <p class="hero-place">Kecamatan Tekarang, Kabupaten Sambas, Kalimantan Barat</p>
        <h1 class="hero-title">Sanggar<br>Tari Kinar</h1>
        <p class="hero-lead"><?= e(setting('tagline')) ?></p>
        <div class="hero-actions">
            <a class="btn btn--gold" href="profil.php">Kenali sanggar kami</a>
            <a class="btn btn--ghost-light" href="galeri.php">Lihat galeri</a>
        </div>
    </div>
    <div class="hero-facts">
        <div class="container facts-grid">
            <div><span class="fact-label">Berpusat di</span><span class="fact-value">Kec. Tekarang, Kab. Sambas</span></div>
            <div><span class="fact-label">Pusat latihan</span><span class="fact-value">SMAN 1 Tekarang</span></div>
            <div><span class="fact-label">Jadwal latihan</span><span class="fact-value"><?= e(setting('jadwal_latihan')) ?></span></div>
        </div>
    </div>
</section>

<!-- TENTANG -->
<section class="section" id="tentang">
    <div class="container split">
        <div class="split-media">
            <?= photo($about_foto, 'Penari Sanggar Tari Kinar', 'ratio-45') ?>
        </div>
        <div class="split-body">
            <h2 class="h2">Tempat belajar menari dari Tekarang</h2>
            <div class="prose">
                <?= nl2br(e(setting('tentang'))) ?>
            </div>
            <dl class="pillars">
                <div><dt>Pelestarian</dt><dd>Menjaga tari tradisional daerah agar tetap dikenal generasi muda.</dd></div>
                <div><dt>Pembinaan</dt><dd>Latihan rutin untuk pelajar, dari dasar gerak sampai tampil di panggung.</dd></div>
                <div><dt>Pementasan</dt><dd>Tampil di acara sekolah, festival, dan kegiatan budaya.</dd></div>
            </dl>
            <a class="link-arrow" href="profil.php">Baca profil lengkap</a>
        </div>
    </div>
</section>

<!-- TARI -->
<?php if ($tari): ?>
<section class="section section--tint" id="tari">
    <div class="container">
        <div class="section-head">
            <h2 class="h2">Tari yang kami bawakan</h2>
            <a class="link-arrow" href="tari.php">Semua tari</a>
        </div>
        <div class="grid grid--3">
            <?php foreach ($tari as $t): ?>
                <a class="tile" href="tari.php#tari-<?= (int)$t['id'] ?>">
                    <div class="tile-media"><?= photo($t['foto'], $t['nama'], 'ratio-43') ?></div>
                    <div class="tile-body">
                        <h3><?= e($t['nama']) ?></h3>
                        <?php if ($t['asal']): ?><p class="meta"><?= e($t['asal']) ?></p><?php endif; ?>
                        <p><?= e(mb_strimwidth((string)$t['deskripsi'], 0, 110, '...')) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- KEGIATAN -->
<?php if ($kegiatan): ?>
<section class="section" id="kegiatan">
    <div class="container">
        <div class="section-head">
            <h2 class="h2">Kegiatan terbaru</h2>
            <a class="link-arrow" href="kegiatan.php">Semua kegiatan</a>
        </div>
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
    </div>
</section>
<?php endif; ?>

<!-- GALERI -->
<?php if ($galeri): ?>
<section class="section section--dark" id="galeri">
    <div class="container">
        <div class="section-head">
            <h2 class="h2">Galeri</h2>
            <a class="link-arrow link-arrow--light" href="galeri.php">Semua foto</a>
        </div>
        <div class="gallery">
            <?php foreach ($galeri as $g): ?>
                <button type="button" class="gallery-item" data-full="<?= e($g['foto']) ?>" data-alt="<?= e($g['judul'] ?: 'Foto Sanggar Tari Kinar') ?>">
                    <?= photo($g['foto'], $g['judul'] ?: 'Foto Sanggar Tari Kinar') ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- LATIHAN -->
<section class="section" id="latihan">
    <div class="container split split--center">
        <div class="split-body">
            <h2 class="h2">Latihan di SMAN 1 Tekarang</h2>
            <div class="prose">
                <p>Pusat latihan sanggar berada di SMAN 1 Tekarang. Pelajar maupun generasi muda sekitar yang ingin bergabung bisa menghubungi kami lebih dulu.</p>
            </div>
            <dl class="info-list">
                <div><dt>Lokasi</dt><dd><?= e(setting('alamat')) ?></dd></div>
                <div><dt>Jadwal</dt><dd><?= e(setting('jadwal_latihan')) ?></dd></div>
            </dl>
            <div class="btn-row">
                <?php if ($wa): ?><a class="btn btn--green" href="<?= e($wa) ?>" target="_blank" rel="noopener">Tanya lewat WhatsApp</a><?php endif; ?>
                <a class="btn btn--outline" href="<?= e($maps) ?>" target="_blank" rel="noopener">Buka di Google Maps</a>
            </div>
        </div>
        <div class="rebung-panel" aria-hidden="true"></div>
    </div>
</section>

<!-- LAYANAN PELENGKAP: SEWA -->
<?php if ($items): ?>
<section class="section section--tint" id="sewa">
    <div class="container">
        <div class="section-head">
            <div>
                <h2 class="h2">Sewa kostum dan properti tari</h2>
                <p class="section-sub">Layanan tambahan untuk sekolah dan panitia acara yang butuh busana tari.</p>
            </div>
            <a class="link-arrow" href="katalog.php">Lihat katalog</a>
        </div>
        <div class="grid grid--3">
            <?php foreach ($items as $it): ?>
                <a class="tile" href="katalog_detail.php?id=<?= (int)$it['id'] ?>">
                    <div class="tile-media"><?= photo($it['foto'], $it['nama'], 'ratio-43') ?></div>
                    <div class="tile-body">
                        <h3><?= e($it['nama']) ?></h3>
                        <p class="meta"><?= e($it['kategori']) ?></p>
                        <p class="price"><?= e(rupiah($it['harga_sewa_per_hari'])) ?> <small>/ hari</small></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="cta">
    <div class="container cta-inner">
        <h2 class="h2">Ingin bergabung atau mengundang kami tampil?</h2>
        <p>Hubungi sanggar untuk informasi latihan, undangan pentas, atau penyewaan kostum.</p>
        <div class="btn-row">
            <?php if ($wa): ?>
                <a class="btn btn--gold" href="<?= e($wa) ?>" target="_blank" rel="noopener">Hubungi lewat WhatsApp</a>
            <?php else: ?>
                <a class="btn btn--gold" href="profil.php#kontak">Lihat kontak</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
