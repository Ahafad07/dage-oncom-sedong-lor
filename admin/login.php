<?php
require_once __DIR__ . '/includes/auth.php';

$error = '';

if (!empty($_SESSION['admin_id'])) {
    kembali('dashboard.php');
}

if ($pdo !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    wajib_csrf('login.php');

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admin WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        kembali('dashboard.php');
    }
    $error = 'Username atau password belum benar. Coba ketik lagi dengan teliti.';
}

$flash = ambil_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk · Panel Admin Dage Abdulrohman</title>
<link rel="icon" type="image/png" href="../images/logo-favicon.png">
<link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<main class="halaman-login">
  <div class="kotak-login">
    <h1>Masuk Panel Admin</h1>
    <p class="bantuan" style="margin-bottom:24px;">Dage Abdulrohman · Sedong Lor</p>

    <?php if ($pdo === null): ?>
      <div class="pesan pesan-error">Database belum tersambung. Isi <code>config.local.php</code> dulu.</div>
    <?php endif; ?>
    <?php if ($flash): ?>
      <div class="pesan pesan-<?= e($flash['jenis']) ?>"><?= e($flash['pesan']) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="pesan pesan-error" role="alert"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php">
      <?= csrf_input() ?>
      <label for="username">Username</label>
      <input type="text" id="username" name="username" autocomplete="username" required autofocus <?= $pdo === null ? 'disabled' : '' ?>>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" autocomplete="current-password" required <?= $pdo === null ? 'disabled' : '' ?>>

      <button type="submit" class="btn" <?= $pdo === null ? 'disabled' : '' ?>>Masuk</button>
    </form>

    <a class="kembali" href="../index.php">Kembali ke website</a>
  </div>
</main>
</body>
</html>
