<?php
require_once __DIR__ . '/config.php';

function e($nilai)
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}

// Foto hasil unggahan admin ada di folder uploads, foto bawaan ada di folder images
function path_gambar($nama)
{
    if (is_file(__DIR__ . '/uploads/' . $nama)) {
        return 'uploads/' . $nama;
    }
    return 'images/' . $nama;
}

// 6285199336859 -> 0851-9933-6859
function format_telepon($nomorWa)
{
    $angka = preg_replace('/\D/', '', $nomorWa);
    if (strpos($angka, '62') === 0) {
        $angka = '0' . substr($angka, 2);
    }
    return preg_replace('/^(\d{4})(\d{4})(\d+)$/', '$1-$2-$3', $angka);
}

// Teks bawaan, dipakai kalau database belum tersambung atau belum diisi
$settings = [
    'logo' => 'logo.jpeg',
    'foto_pemilik' => 'pemilik.jpeg',
    'tentang_judul' => 'Dibuat langsung oleh Pak Abdulrohman',
    'tentang_paragraf1' => 'Usaha ini dijalankan Pak Abdulrohman bersama keluarganya di Sedong Lor. Bahan dipilih, difermentasi secara alami tanpa pengawet, dengan resep turun-temurun.',
    'tentang_paragraf2' => 'Bagi warga Sedong Lor, dage sudah jadi bagian dari makanan sehari-hari dan tradisi yang masih dijaga.',
    'alamat' => '4HPP+QJF, Kapten Mustofa, Sedong Lor, Kec. Sedong, Kab. Cirebon, Jawa Barat 45189',
    'nomor_wa' => '6285199336859',
    'jam_operasional' => 'Setiap hari, 02.00 - 07.00 WIB',
];

$banners = [
    ['judul' => 'Dage dan Oncom Hideng dari Sedong Lor', 'subjudul' => 'Dibuat di rumah dengan cara lama: direndam, difermentasi alami tanpa pengawet, lalu dijemur di atas anyaman bambu.', 'gambar' => 'dage-proses.jpeg'],
];

$produkList = [
    ['nama' => 'Dage Segar', 'deskripsi' => 'Teksturnya padat dengan aroma fermentasi yang khas.', 'stok' => 0, 'gambar' => 'dage-proses.jpeg'],
    ['nama' => 'Kemasan Harian', 'deskripsi' => 'Tersedia dalam potongan sesuai kebutuhan, untuk dapur rumah atau dijual lagi.', 'stok' => 0, 'gambar' => 'produk.jpeg'],
];

if ($pdo !== null) {
    try {
        $rows = $pdo->query('SELECT nama_setting, nilai FROM pengaturan')->fetchAll();
        foreach ($rows as $r) {
            if (isset($settings[$r['nama_setting']]) && $r['nilai'] !== '') {
                $settings[$r['nama_setting']] = $r['nilai'];
            }
        }
    } catch (Exception $e) {
        // Tabel belum ada, pakai teks bawaan
    }
    try {
        $bannerDb = $pdo->query('SELECT judul, subjudul, gambar FROM banner WHERE aktif = 1 ORDER BY urutan ASC, id ASC')->fetchAll();
        if (!empty($bannerDb)) {
            $banners = $bannerDb;
        }
    } catch (Exception $e) {
        // Tabel banner belum ada, pakai banner bawaan
    }
    try {
        $produkDb = $pdo->query('SELECT nama, deskripsi, stok, gambar FROM produk ORDER BY urutan ASC, id ASC')->fetchAll();
        if (!empty($produkDb)) {
            $produkList = $produkDb;
        }
    } catch (Exception $e) {
        // Tabel produk belum ada, pakai contoh bawaan
    }
}

$nomorWa = preg_replace('/\D/', '', $settings['nomor_wa']);

function link_wa($nomorWa, $pesan)
{
    return 'https://wa.me/' . $nomorWa . '?text=' . rawurlencode($pesan);
}

$linkWaUmum = link_wa($nomorWa, 'Halo Pak, saya mau tanya soal dage.');
$linkMaps = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($settings['alamat']);
$teleponTampil = format_telepon($settings['nomor_wa']);

$langkah = [
    ['Direndam', 'Bahan direndam dulu supaya teksturnya melunak sebelum diolah.'],
    ['Difermentasi', 'Dibungkus dan disimpan di suhu ruang beberapa hari sampai tumbuh alami.'],
    ['Ditata di anyaman', 'Ditata rapi lapis demi lapis di atas nyiru bambu.'],
    ['Dijemur lalu dipanggang', 'Dijemur alami, lalu dipanggang sampai matang sebelum dikirim.'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dage Abdulrohman, Sedong Lor, Kabupaten Cirebon</title>
<meta name="description" content="Dage dan oncom hideng buatan rumah dari Sedong Lor, Kabupaten Cirebon. Difermentasi alami tanpa pengawet.">
<link rel="icon" type="image/png" href="images/logo-favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header class="site-header">
  <div class="container header-isi">
    <a class="merek" href="#atas">
      <img src="<?= e(path_gambar($settings['logo'])) ?>" alt="" width="44" height="44">
      <span>Dage Abdulrohman</span>
    </a>
    <a class="tombol tombol-kecil" href="<?= e($linkWaUmum) ?>" target="_blank" rel="noopener">Hubungi WhatsApp</a>
  </div>
</header>

<main id="atas">

  <section class="banner-bagian" aria-label="Banner">
    <div class="container">
      <div class="banner-track">
        <?php foreach ($banners as $i => $b): ?>
          <article class="banner-slide">
            <img src="<?= e(path_gambar($b['gambar'])) ?>" alt="">
            <div class="banner-teks">
              <?php if ($i === 0): ?>
                <h1><?= e($b['judul']) ?></h1>
              <?php else: ?>
                <h2><?= e($b['judul']) ?></h2>
              <?php endif; ?>
              <?php if (!empty($b['subjudul'])): ?>
                <p><?= e($b['subjudul']) ?></p>
              <?php endif; ?>
              <div class="aksi">
                <a class="tombol" href="<?= e($linkWaUmum) ?>" target="_blank" rel="noopener">Pesan lewat WhatsApp</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="tentang">
    <div class="container tentang-grid">
      <figure class="tentang-foto">
        <img src="<?= e(path_gambar($settings['foto_pemilik'])) ?>" alt="Pak Abdulrohman dan rekannya">
      </figure>
      <div>
        <h2><?= e($settings['tentang_judul']) ?></h2>
        <p><?= e($settings['tentang_paragraf1']) ?></p>
        <p><?= e($settings['tentang_paragraf2']) ?></p>
      </div>
    </div>
  </section>

  <section id="cara-buat">
    <div class="container">
      <h2>Cara membuat dage</h2>
      <ol class="langkah">
        <?php foreach ($langkah as $item): ?>
          <li>
            <h3><?= e($item[0]) ?></h3>
            <p><?= e($item[1]) ?></p>
          </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </section>

  <section id="produk">
    <div class="container">
      <h2>Produk</h2>
      <p class="pengantar">Dibuat sesuai pesanan, lalu dikemas untuk dikirim ke Cirebon dan sekitarnya. Stok di bawah bisa berubah sewaktu-waktu.</p>
      <ul class="daftar-produk">
        <?php foreach ($produkList as $p):
          $stok = (int) $p['stok'];
          $pesanProduk = link_wa($nomorWa, 'Halo Pak, saya mau pesan ' . $p['nama'] . '. Apakah masih ada?');
        ?>
          <li class="produk">
            <img src="<?= e(path_gambar($p['gambar'])) ?>" alt="<?= e($p['nama']) ?>">
            <div class="produk-isi">
              <h3><?= e($p['nama']) ?></h3>
              <p><?= e($p['deskripsi']) ?></p>
              <?php if ($stok > 0): ?>
                <span class="stok stok-ada">Stok tersedia: <?= $stok ?></span>
                <a class="tombol tombol-kecil" href="<?= e($pesanProduk) ?>" target="_blank" rel="noopener">Pesan produk ini</a>
              <?php else: ?>
                <span class="stok stok-habis">Stok habis</span>
                <a class="tombol tombol-kedua tombol-kecil" href="<?= e($pesanProduk) ?>" target="_blank" rel="noopener">Tanya kapan tersedia</a>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section id="kontak">
    <div class="container kontak-grid">
      <div>
        <h2>Pesan atau datang langsung</h2>
        <p>Pak Abdulrohman biasanya membalas lewat WhatsApp. Untuk pesanan dalam jumlah besar, mohon konfirmasi H-1.</p>
        <a class="tombol" href="<?= e($linkWaUmum) ?>" target="_blank" rel="noopener">Chat di WhatsApp</a>
      </div>
      <dl class="info-kontak">
        <dt>Alamat</dt>
        <dd><a href="<?= e($linkMaps) ?>" target="_blank" rel="noopener"><?= e($settings['alamat']) ?></a></dd>

        <dt>Telepon</dt>
        <dd><a href="tel:+<?= e($nomorWa) ?>"><?= e($teleponTampil) ?></a></dd>

        <dt>Jam</dt>
        <dd><?= e($settings['jam_operasional']) ?></dd>
      </dl>
    </div>
  </section>

</main>

<footer class="site-footer">
  <div class="container footer-isi">
    <span>&copy; 2026 Dage Abdulrohman, Sedong Lor</span>
    <span>Website dibuat untuk program KKM UMC 2026 &middot; <a href="admin/login.php">Kelola website</a></span>
  </div>
</footer>

<!-- Bar bawah khusus HP: pembeli bisa langsung pesan tanpa perlu buka laptop -->
<nav class="bar-hp" aria-label="Pesan cepat">
  <a class="tombol tombol-hitam" href="<?= e($linkWaUmum) ?>" target="_blank" rel="noopener">WhatsApp</a>
  <a class="tombol tombol-kedua" href="<?= e($linkMaps) ?>" target="_blank" rel="noopener">Lokasi</a>
</nav>

</body>
</html>
