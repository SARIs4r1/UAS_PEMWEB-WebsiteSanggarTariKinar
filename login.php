<?php
require_once __DIR__ . '/includes/bootstrap.php';

$base_url     = '';
$page_title   = 'Masuk';
$header_solid = true;

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        login_user($user);
        redirect($user['role'] === 'admin' ? 'admin/dashboard.php' : 'index.php');
    }
    $error = 'Email atau password salah.';
}

require __DIR__ . '/includes/header.php';
?>
<section class="auth">
    <div class="auth-side">
        <h1 class="auth-title">Selamat datang kembali</h1>
        <p>Masuk untuk mengajukan sewa kostum dan melihat status permintaanmu. Admin sanggar masuk dari sini juga.</p>
        <div class="rebung" aria-hidden="true"></div>
    </div>
    <div class="auth-form">
        <h2 class="h3">Masuk</h2>
        <?php if ($error): ?><div class="alert alert--error" role="alert"><?= e($error) ?></div><?php endif; ?>
        <form method="post" action="login.php" class="form">
            <?= csrf_field() ?>
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="email" required autofocus>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn--green btn--block">Masuk</button>
            <p class="meta">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
        </form>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
