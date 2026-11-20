<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$base_url   = '';
$page_title = 'Daftar';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$name   = '';
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    if (empty($errors)) {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = :email');
        $check->execute(['email' => $email]);
        if ($check->fetch()) {
            $errors[] = 'Email sudah terdaftar. Silakan masuk.';
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insert = $pdo->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)'
        );
        // Role pelanggan di-hardcode di sini: pendaftaran publik TIDAK BOLEH bisa membuat akun admin.
        $insert->execute([
            'name'     => $name,
            'email'    => $email,
            'password' => $hash,
            'role'     => 'pelanggan',
        ]);

        $_SESSION['flash_success'] = 'Pendaftaran berhasil. Silakan masuk.';
        header('Location: login.php');
        exit;
    }
}

require __DIR__ . '/includes/header.php';
?>

<h1>Daftar Akun Pelanggan</h1>

<?php foreach ($errors as $err): ?>
    <div class="alert alert-error"><?= htmlspecialchars($err) ?></div>
<?php endforeach; ?>

<form class="form-box" method="post" action="register.php">
    <div class="form-group">
        <label for="name">Nama Lengkap</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" minlength="6" required>
    </div>
    <div class="form-group">
        <label for="password_confirm">Konfirmasi Password</label>
        <input type="password" id="password_confirm" name="password_confirm" minlength="6" required>
    </div>
    <button type="submit" class="btn">Daftar</button>
    <p class="muted">Sudah punya akun? <a href="login.php">Masuk di sini</a>.</p>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
