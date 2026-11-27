<?php
require_once __DIR__ . '/includes/bootstrap.php';

$base_url     = '';
$page_title   = 'Daftar';
$header_solid = true;

if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];
$name   = '';
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['password_confirm'] ?? '';

    if ($name === '') {
        $errors[] = 'Nama wajib diisi.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }

    if (!$errors) {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $check->execute(['email' => $email]);
        if ($check->fetch()) {
            $errors[] = 'Email sudah terdaftar. Silakan masuk.';
        }
    }

    if (!$errors) {
        $insert = $pdo->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)'
        );
        $insert->execute([
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role'     => 'pelanggan',
        ]);
        flash('success', 'Pendaftaran berhasil. Silakan masuk.');
        redirect('login.php');
    }
}

require __DIR__ . '/includes/header.php';
?>
<section class="auth">
    <div class="auth-side">
        <h1 class="auth-title">Buat akun pelanggan</h1>
        <p>Akun dipakai untuk mengajukan sewa kostum dan properti tari. Mengenal sanggar dan melihat galeri tidak perlu akun.</p>
        <div class="rebung" aria-hidden="true"></div>
    </div>
    <div class="auth-form">
        <h2 class="h3">Daftar</h2>
        <?php foreach ($errors as $err): ?><div class="alert alert--error" role="alert"><?= e($err) ?></div><?php endforeach; ?>
        <form method="post" action="register.php" class="form">
            <?= csrf_field() ?>
            <div class="field">
                <label for="name">Nama lengkap</label>
                <input type="text" id="name" name="name" value="<?= e($name) ?>" autocomplete="name" required>
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="email" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="6" autocomplete="new-password" required>
            </div>
            <div class="field">
                <label for="password_confirm">Ulangi password</label>
                <input type="password" id="password_confirm" name="password_confirm" minlength="6" autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn btn--green btn--block">Buat akun</button>
            <p class="meta">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
        </form>
    </div>
</section>
<?php require __DIR__. '/includes/footer.php'; ?>
