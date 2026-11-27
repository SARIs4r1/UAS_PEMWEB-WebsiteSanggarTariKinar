<?php
/**
 * Autentikasi & access control berbasis role (session).
 * Wajib di-include sebelum ada output apa pun (session_start & header() butuh itu).
 */

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/** Simpan data user ke session setelah login berhasil. */
function login_user(array $user): void
{
    session_regenerate_id(true); // cegah session fixation
    $_SESSION['user_id']   = (int)$user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
}

/** Hapus session (logout). */
function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/** Data user yang sedang login, atau null. */
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

/** Wajib login. Kalau belum, arahkan ke halaman login. */
function require_login(string $base = ''): void
{
    if (!is_logged_in()) {
        header('Location: ' . $base . 'login.php');
        exit;
    }
}

/** Wajib login DAN punya role tertentu (access control). */
function require_role(string $role, string $base = ''): void
{
    require_login($base);
    if ($_SESSION['user_role'] !== $role) {
        http_response_code(403);
        echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<title>Akses ditolak</title></head>';
        echo '<body style="font-family:system-ui,sans-serif;padding:3rem;max-width:34rem;margin:auto">';
        echo '<h1>403 &ndash; Akses ditolak</h1>';
        echo '<p>Halaman ini hanya untuk role <strong>' . htmlspecialchars($role) . '</strong>.</p>';
        echo '<p><a href="' . htmlspecialchars($base) . 'index.php">Kembali ke beranda</a></p>';
        echo '</body></html>';
        exit;
    }
}
