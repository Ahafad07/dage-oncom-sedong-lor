<?php
require_once __DIR__ . '/includes/layout.php';
require_login();

$halaman = 'pengaturan.php';

$defaults = [
    'logo' => 'logo.jpeg',
    'foto_pemilik' => 'pemilik.jpeg',
    'tentang_judul' => 'Dibuat langsung oleh Pak Abdulrohman',
    'tentang_paragraf1' => '',
    'tentang_paragraf2' => '',
    'alamat' => '',
    'nomor_wa' => '',
    'jam_operasional' => '',
];

$settings = $defaults;
foreach ($pdo->query('SELECT nama_setting, nilai FROM pengaturan')->fetchAll() as $r) {
    if (array_key_exists($r['nama_setting'], $settings)) {
        $settings[$r['nama_setting']] = $r['nilai'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    wajib_csrf($halaman);

    $error = '';
    $teks = ['tentang_judul', 'tentang_paragraf1', 'tentang_paragraf2', 'alamat', 'nomor_wa', 'jam_operasional'];
    foreach ($teks as $f) {
        $settings[$f] = trim($_POST[$f] ?? '');
    }
    // 0851... diubah jadi 62851..., supaya tautan WhatsApp dan telepon benar
    $angka = preg_replace('/\D/', '', $settings['nomor_wa']);
    if (strpos($angka, '0') === 0) {
        $angka = '62' . substr($angka, 1);
    }
    $settings['nomor_wa'] = $angka;

    $logoBaru = simpan_gambar('logo', 'logo', $error);
    if ($error === '') {
        $fotoBaru = simpan_gambar('foto_pemilik', 'pemilik', $error);
    }

    if ($error === '') {
        if ($logoBaru !== null) {
            $settings['logo'] = $logoBaru;
        }
        if (isset($fotoBaru) && $fotoBaru !== null) {
            $settings['foto_pemilik'] = $fotoBaru;
        }

        $simpan = $pdo->prepare('INSERT INTO pengaturan (nama_setting, nilai) VALUES (?, ?) ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)');
        foreach ($settings as $kunci => $nilai) {
            $simpan->execute([$kunci, $nilai]);
        }
        flash('ok', 'Pengaturan sudah disimpan. Buka website untuk melihat perubahannya.');
    } else {
        flash('error', $error);
    }
    kembali($halaman);
}

admin_mulai('Pengaturan website', 'pengaturan');
?>

<form method="post" enctype="multipart/form-data">
  <?= csrf_input() ?>

  <div class="kartu-admin">
    <h2>Logo dan foto pemilik</h2>
    <div class="baris">
      <div class="foto-saat-ini">
        <img src="<?= e(gambar_admin($settings['logo'])) ?>" alt="Logo saat ini" style="aspect-ratio:1/1; width:160px; object-fit:cover; border-radius:50%;">
        <label for="logo">Ganti logo</label>
        <input type="file" id="logo" name="logo" accept=".jpg,.jpeg,.png,.webp">
      </div>
      <div class="foto-saat-ini">
        <img src="<?= e(gambar_admin($settings['foto_pemilik'])) ?>" alt="Foto pemilik saat ini">
        <label for="foto_pemilik">Ganti foto pemilik (bagian Tentang)</label>
        <input type="file" id="foto_pemilik" name="foto_pemilik" accept=".jpg,.jpeg,.png,.webp">
        <p class="bantuan">JPG, PNG, atau WEBP, maksimal 4 MB.</p>
      </div>
    </div>
  </div>

  <div class="kartu-admin">
    <h2>Bagian Tentang</h2>

    <label for="tentang_judul">Judul</label>
    <input type="text" id="tentang_judul" name="tentang_judul" value="<?= e($settings['tentang_judul']) ?>">

    <label for="tentang_paragraf1">Paragraf pertama</label>
    <textarea id="tentang_paragraf1" name="tentang_paragraf1"><?= e($settings['tentang_paragraf1']) ?></textarea>

    <label for="tentang_paragraf2">Paragraf kedua</label>
    <textarea id="tentang_paragraf2" name="tentang_paragraf2"><?= e($settings['tentang_paragraf2']) ?></textarea>
  </div>

  <div class="kartu-admin">
    <h2>Kontak</h2>

    <label for="alamat">Alamat lengkap</label>
    <textarea id="alamat" name="alamat"><?= e($settings['alamat']) ?></textarea>

    <label for="nomor_wa">Nomor WhatsApp</label>
    <input type="text" id="nomor_wa" name="nomor_wa" inputmode="numeric" value="<?= e($settings['nomor_wa']) ?>">
    <p class="bantuan">Awali dengan 62, lalu nomornya tanpa angka 0 di depan. Contoh: 6285199336859</p>

    <label for="jam_operasional">Jam buka</label>
    <input type="text" id="jam_operasional" name="jam_operasional" value="<?= e($settings['jam_operasional']) ?>">
  </div>

  <button type="submit" class="btn">Simpan semua perubahan</button>
</form>

<?php admin_selesai(); ?>
