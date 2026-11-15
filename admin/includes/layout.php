<?php
require_once __DIR__ . '/auth.php';

// Foto di admin: path relatif dari folder admin/
function gambar_admin($nama)
{
    if (is_file(__DIR__ . '/../../uploads/' . $nama)) {
        return '../uploads/' . $nama;
    }
    return '../images/' . $nama;
}

// Mengunggah satu foto. Mengembalikan nama file baru, atau null kalau tidak ada file / gagal.
// Pesan kesalahan masuk ke $error.
function simpan_gambar($field, $awalan, &$error)
{
    if (empty($_FILES[$field]['name'])) {
        return null;
    }
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        $error = 'Foto gagal diunggah. Coba lagi dengan foto yang lebih kecil.';
        return null;
    }
    if ($_FILES[$field]['size'] > 4 * 1024 * 1024) {
        $error = 'Ukuran foto maksimal 4 MB.';
        return null;
    }

    $tmp = $_FILES[$field]['tmp_name'];
    $info = @getimagesize($tmp);
    $jenis = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = $info['mime'] ?? '';
    if (!isset($jenis[$mime])) {
        $error = 'Format foto harus JPG, PNG, atau WEBP.';
        return null;
    }

    $folder = __DIR__ . '/../../uploads/';
    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }
    $nama = $awalan . '_' . time() . '_' . random_int(1000, 9999) . '.' . $jenis[$mime];
    if (!move_uploaded_file($tmp, $folder . $nama)) {
        $error = 'Foto gagal disimpan di server.';
        return null;
    }
    return $nama;
}

// Hapus file hasil unggahan (bukan foto bawaan di folder images)
function hapus_upload($nama)
{
    if ($nama === '' || $nama === null) {
        return;
    }
    $path = __DIR__ . '/../../uploads/' . basename($nama);
    if (is_file($path)) {
        unlink($path);
    }
}

function admin_mulai($judul, $aktif)
{
    $menu = [
        'produk' => ['Produk', 'dashboard.php'],
        'banner' => ['Banner', 'banner.php'],
        'pengaturan' => ['Pengaturan', 'pengaturan.php'],
        'sandi' => ['Ganti password', 'ganti-password.php'],
    ];
    $flash = ambil_flash();
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($judul) ?> · Panel Admin</title>
<link rel="icon" type="image/png" href="../images/logo-favicon.png">
<link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>

<header class="admin-atas">
  <div class="admin-atas-isi">
    <div class="admin-judul">
      <strong>Panel Admin</strong>
      <span>Dage Abdulrohman</span>
    </div>
    <nav class="admin-menu" aria-label="Menu admin">
      <?php foreach ($menu as $kunci => [$label, $tujuan]): ?>
        <a href="<?= e($tujuan) ?>" class="<?= $kunci === $aktif ? 'aktif' : '' ?>" <?= $kunci === $aktif ? 'aria-current="page"' : '' ?>><?= e($label) ?></a>
      <?php endforeach; ?>
      <a href="../index.php" target="_blank" rel="noopener">Lihat website</a>
      <a href="logout.php" class="keluar">Keluar</a>
    </nav>
  </div>
</header>

<main class="admin-isi">
  <h1><?= e($judul) ?></h1>

  <?php if ($flash): ?>
    <div class="pesan pesan-<?= e($flash['jenis']) ?>" role="status"><?= e($flash['pesan']) ?></div>
  <?php endif; ?>
    <?php
}

function admin_selesai()
{
    ?>
</main>
</body>
</html>
    <?php
}
