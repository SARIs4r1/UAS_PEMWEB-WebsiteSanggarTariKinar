<?php
/** Fungsi bantu umum: escape, setting situs, flash, CSRF, upload foto. */

function e($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/* ---------- Pengaturan situs (tabel settings) ---------- */

function settings_all(): array
{
    static $cache = null;
    global $pdo;
    if ($cache === null) {
        $cache = [];
        foreach ($pdo->query('SELECT k, v FROM settings') as $row) {
            $cache[$row['k']] = (string)$row['v'];
        }
    }
    return $cache;
}

function setting(string $key, string $default = ''): string
{
    $all = settings_all();
    return (isset($all[$key]) && $all[$key] !== '') ? $all[$key] : $default;
}

/* ---------- Format ---------- */

function rupiah($n): string
{
    return 'Rp ' . number_format((float)$n, 0, ',', '.');
}

function tanggal_id(?string $date): string
{
    if (!$date) {
        return '';
    }
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
              'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $t = strtotime($date);
    return date('j', $t) . ' ' . $bulan[(int)date('n', $t)] . ' ' . date('Y', $t);
}

function wa_link(string $text = ''): string
{
    $num = preg_replace('/\D+/', '', setting('whatsapp'));
    if ($num === '') {
        return '';
    }
    if (strpos($num, '0') === 0) {
        $num = '62' . substr($num, 1);
    }
    return 'https://wa.me/' . $num . ($text !== '' ? '?text=' . rawurlencode($text) : '');
}

/* ---------- Flash message ---------- */

function flash(string $type, string $msg): void
{
    $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg];
}

function flash_render(): string
{
    if (empty($_SESSION['flash'])) {
        return '';
    }
    $out = '';
    foreach ($_SESSION['flash'] as $f) {
        $out .= '<div class="toast toast--' . e($f['type']) . '">' . e($f['msg']) . '</div>';
    }
    unset($_SESSION['flash']);
    return $out;
}

/* ---------- CSRF ---------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/** Panggil di awal setiap handler POST. */
function csrf_verify(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals($_SESSION['csrf'] ?? '', $sent)) {
        http_response_code(419);
        exit('Sesi formulir tidak valid atau sudah kedaluwarsa. Kembali dan muat ulang halaman.');
    }
}

/* ---------- Foto: tampil & upload ---------- */

/** Tampilkan foto; kalau belum ada, tampilkan blok placeholder bermotif. */
function photo(?string $path, string $alt, string $class = ''): string
{
    global $base_url;
    $base = $base_url ?? '';
    if ($path && is_file(__DIR__ . '/../' . $path)) {
        return '<img class="' . e($class) . '" src="' . e($base . $path) . '" alt="' . e($alt) . '" loading="lazy">';
    }
    return '<div class="ph ' . e($class) . '" role="img" aria-label="' . e($alt) . '"></div>';
}

/**
 * Validasi & simpan foto ke folder uploads/.
 * Return: ['ok' => bool, 'path' => 'uploads/xxx.jpg'|null, 'error' => string]
 */
function upload_image(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'path' => null, 'error' => 'Upload foto gagal. Coba pilih file lagi.'];
    }
    if ($file['size'] > 4 * 1024 * 1024) {
        return ['ok' => false, 'path' => null, 'error' => 'Ukuran foto maksimal 4 MB.'];
    }
    $info = @getimagesize($file['tmp_name']);
    $ext_by_mime = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if ($info === false || !isset($ext_by_mime[$info['mime']])) {
        return ['ok' => false, 'path' => null, 'error' => 'Format foto harus JPG, PNG, atau WebP.'];
    }
    $dir = __DIR__ . '/../uploads';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $name = bin2hex(random_bytes(10)) . '.' . $ext_by_mime[$info['mime']];
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        return ['ok' => false, 'path' => null, 'error' => 'Foto tidak bisa disimpan. Cek izin folder uploads/.'];
    }
    return ['ok' => true, 'path' => 'uploads/' . $name, 'error' => ''];
}

/** Hapus file foto lama (hanya di dalam uploads/). */
function delete_upload(?string $path): void
{
    if (!$path || strpos($path, 'uploads/') !== 0) {
        return;
    }
    $full = __DIR__ . '/../uploads/' . basename($path);
    if (is_file($full)) {
        unlink($full);
    }
}

/** Banner judul untuk halaman dalam. */
function page_hero(string $title, string $lead = ''): void
{
    echo '<section class="page-hero"><div class="container">';
    echo '<h1>' . e($title) . '</h1>';
    if ($lead !== '') {
        echo '<p class="lead">' . e($lead) . '</p>';
    }
    echo '</div><div class="rebung" aria-hidden="true"></div></section>';
}
