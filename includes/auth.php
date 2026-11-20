<?php
/**
 * Helper autentikasi & access control berbasis role (session-based).
 * File ini WAJIB di-include di setiap halaman SEBELUM ada output apapun,
 * karena session_start() dan header('Location: ...') butuh itu.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Simpan data user ke session setelah login berhasil. */
function login_user(array $user): void
{
    // Cegah session fixation: regenerasi id session setiap kali login.
    session_regenerate_id(true);
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
}

/** Hapus session (logout). */
function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain']);
    }
    session_destroy();
}

/** Ambil data user yang sedang login, atau null kalau belum login. */
function current_user(): ?array
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    return [
        'id'   => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'role' => $_SESSION['user_role'],
    ];
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

/** Wajib login. Kalau belum, redirect ke halaman login. */
function require_login(string $redirect_to_base = ''): void
{
    if (!is_logged_in()) {
        header('Location: ' . $redirect_to_base . 'login.php');
        exit;
    }
}

/**
 * Wajib login DAN memiliki role tertentu (access control).
 * Contoh: require_role('admin') di awal halaman khusus admin.
 */
function require_role(string $role, string $redirect_to_base = ''): void
{
    require_login($redirect_to_base);
    if ($_SESSION['user_role'] !== $role) {
        http_response_code(403);
        echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">';
        echo '<title>Akses Ditolak</title></head><body style="font-family:sans-serif;padding:2rem;">';
        echo '<h1>403 - Akses Ditolak</h1>';
        echo '<p>Halaman ini khusus untuk role <strong>' . htmlspecialchars($role) . '</strong>.</p>';
        echo '<p><a href="' . htmlspecialchars($redirect_to_base) . 'index.php">Kembali ke beranda</a></p>';
        echo '</body></html>';
        exit;
    }
}
