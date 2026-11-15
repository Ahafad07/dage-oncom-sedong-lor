<?php
require_once __DIR__ . '/includes/layout.php';
require_login();

$halaman = 'ganti-password.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    wajib_csrf($halaman);

    $lama = $_POST['password_lama'] ?? '';
    $baru = $_POST['password_baru'] ?? '';
    $ulang = $_POST['password_ulang'] ?? '';

    $stmt = $pdo->prepare('SELECT password_hash FROM admin WHERE id = ?');
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($lama, $admin['password_hash'])) {
        flash('error', 'Password lama belum benar.');
    } elseif (strlen($baru) < 8) {
        flash('error', 'Password baru minimal 8 karakter.');
    } elseif ($baru !== $ulang) {
        flash('error', 'Ulangi password baru belum sama dengan yang pertama.');
    } else {
        $pdo->prepare('UPDATE admin SET password_hash = ? WHERE id = ?')
            ->execute([password_hash($baru, PASSWORD_DEFAULT), $_SESSION['admin_id']]);
        flash('ok', 'Password berhasil diganti.');
    }
    kembali($halaman);
}

admin_mulai('Ganti password', 'sandi');
?>

<form class="kartu-admin" method="post" style="max-width:560px;">
  <?= csrf_input() ?>

  <label for="password_lama">Password lama</label>
  <input type="password" id="password_lama" name="password_lama" autocomplete="current-password" required>

  <label for="password_baru">Password baru (minimal 8 karakter)</label>
  <input type="password" id="password_baru" name="password_baru" autocomplete="new-password" required minlength="8">

  <label for="password_ulang">Ulangi password baru</label>
  <input type="password" id="password_ulang" name="password_ulang" autocomplete="new-password" required minlength="8">

  <div class="aksi-kartu">
    <div class="kiri"><button type="submit" class="btn">Simpan password baru</button></div>
  </div>
</form>

<?php admin_selesai(); ?>
