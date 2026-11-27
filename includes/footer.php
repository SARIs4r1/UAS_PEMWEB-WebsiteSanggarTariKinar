</main>
<?php
$base_url = $base_url ?? '';
$wa = wa_link('Halo Sanggar Tari Kinar, saya ingin bertanya.');
?>
<footer class="site-footer">
    <div class="rebung" aria-hidden="true"></div>
    <div class="container footer-grid">
        <div>
            <p class="footer-title"><?= e(setting('nama_sanggar', 'Sanggar Tari Kinar')) ?></p>
            <p class="footer-text"><?= e(setting('tagline')) ?></p>
        </div>
        <div>
            <p class="footer-head">Jelajahi</p>
            <ul class="footer-list">
                <li><a href="<?= e($base_url) ?>profil.php">Profil</a></li>
                <li><a href="<?= e($base_url) ?>tari.php">Tari</a></li>
                <li><a href="<?= e($base_url) ?>kegiatan.php">Kegiatan</a></li>
                <li><a href="<?= e($base_url) ?>galeri.php">Galeri</a></li>
                <li><a href="<?= e($base_url) ?>katalog.php">Sewa kostum</a></li>
            </ul>
        </div>
        <div>
            <p class="footer-head">Hubungi kami</p>
            <ul class="footer-list">
                <li><?= e(setting('alamat', 'SMAN 1 Tekarang, Kec. Tekarang, Kab. Sambas, Kalimantan Barat')) ?></li>
                <?php if ($wa): ?><li><a href="<?= e($wa) ?>" target="_blank" rel="noopener">WhatsApp: <?= e(setting('whatsapp')) ?></a></li><?php endif; ?>
                <?php if (setting('email')): ?><li><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></li><?php endif; ?>
                <?php if (setting('instagram')): ?><li>Instagram: <?= e(setting('instagram')) ?></li><?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="container footer-bottom">
        <small>&copy; <?= date('Y') ?> <?= e(setting('nama_sanggar', 'Sanggar Tari Kinar')) ?>. Kecamatan Tekarang, Kabupaten Sambas, Kalimantan Barat.</small>
    </div>
</footer>
<dialog class="lightbox" id="lightbox"><img alt=""><form method="dialog"><button class="btn btn--ghost btn--sm">Tutup</button></form></dialog>
<script src="<?= e($base_url) ?>assets/js/main.js?v=<?= (int)@filemtime(__DIR__ . '/../assets/js/main.js') ?>"></script>
</body>
</html>
