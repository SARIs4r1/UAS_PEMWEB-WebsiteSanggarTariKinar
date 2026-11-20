<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$base_url   = '';
$page_title = 'Profil Usaha';

require __DIR__ . '/includes/header.php';
?>

<section class="card">
    <h1>Profil Usaha</h1>
    <p>
        Sanggar Tari Kinar adalah usaha penyewaan kostum dan properti tari yang berdiri untuk
        mendukung pelaku seni, sekolah, dan panitia acara budaya di Kalimantan Barat dalam menyiapkan
        perlengkapan pentas tari tradisional maupun modern.
    </p>

    <h2>Visi</h2>
    <p>Menjadi mitra terpercaya dalam penyediaan kostum dan properti tari berkualitas.</p>

    <h2>Misi</h2>
    <ul>
        <li>Menyediakan koleksi kostum dan properti tari yang lengkap dan terawat.</li>
        <li>Memberikan kemudahan informasi dan proses penyewaan secara online.</li>
        <li>Mendukung pelestarian seni tari tradisional Nusantara.</li>
    </ul>

    <h2>Kontak</h2>
    <p>
        Alamat: Pontianak, Kalimantan Barat<br>
        WhatsApp: 08xx-xxxx-xxxx<br>
        Email: info@sanggartarikhatulistiwa.test
    </p>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
