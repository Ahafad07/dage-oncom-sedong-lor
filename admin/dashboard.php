<?php
require_once __DIR__ . '/includes/layout.php';
require_login();

$halaman = 'dashboard.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    wajib_csrf($halaman);
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'hapus') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('SELECT gambar FROM produk WHERE id = ?');
        $stmt->execute([$id]);
        if ($baris = $stmt->fetch()) {
            $pdo->prepare('DELETE FROM produk WHERE id = ?')->execute([$id]);
            hapus_upload($baris['gambar']);
            flash('ok', 'Produk berhasil dihapus.');
        }
        kembali($halaman);
    }

    if ($aksi === 'simpan' || $aksi === 'tambah') {
        $id = (int) ($_POST['id'] ?? 0);
        $nama = trim($_POST['nama'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $stok = max(0, (int) ($_POST['stok'] ?? 0));
        $urutan = (int) ($_POST['urutan'] ?? 0);
        $error = '';

        if ($nama === '') {
            $error = 'Nama produk wajib diisi.';
        }

        $gambarBaru = null;
        if ($error === '') {
            $gambarBaru = simpan_gambar('gambar', 'produk', $error);
        }

        if ($error === '' && $aksi === 'tambah') {
            if ($gambarBaru === null) {
                $error = 'Foto produk wajib dipilih untuk produk baru.';
            } else {
                $pdo->prepare('INSERT INTO produk (nama, deskripsi, stok, gambar, urutan) VALUES (?, ?, ?, ?, ?)')
                    ->execute([$nama, $deskripsi, $stok, $gambarBaru, $urutan]);
                flash('ok', 'Produk baru berhasil ditambahkan.');
                kembali($halaman);
            }
        }

        if ($error === '' && $aksi === 'simpan') {
            $stmt = $pdo->prepare('SELECT gambar FROM produk WHERE id = ?');
            $stmt->execute([$id]);
            $lama = $stmt->fetch();
            if (!$lama) {
                $error = 'Produk tidak ditemukan.';
            } else {
                if ($gambarBaru !== null) {
                    $pdo->prepare('UPDATE produk SET nama=?, deskripsi=?, stok=?, urutan=?, gambar=? WHERE id=?')
                        ->execute([$nama, $deskripsi, $stok, $urutan, $gambarBaru, $id]);
                    hapus_upload($lama['gambar']);
                } else {
                    $pdo->prepare('UPDATE produk SET nama=?, deskripsi=?, stok=?, urutan=? WHERE id=?')
                        ->execute([$nama, $deskripsi, $stok, $urutan, $id]);
                }
                flash('ok', 'Perubahan produk "' . $nama . '" sudah disimpan.');
                kembali($halaman);
            }
        }

        flash('error', $error);
        kembali($halaman);
    }
}

$produk = $pdo->query('SELECT * FROM produk ORDER BY urutan ASC, id ASC')->fetchAll();

admin_mulai('Produk dan stok', 'produk');
?>

<p>Ubah foto, nama, deskripsi, dan stok langsung di kartu produk, lalu tekan <strong>Simpan perubahan</strong>.</p>
<p style="margin-bottom:24px;">
  <a class="btn" href="#tambah-produk">+ Tambah produk baru</a>
</p>

<?php if (empty($produk)): ?>
  <div class="kartu-admin">
    <p>Belum ada produk. Tekan tombol <strong>Tambah produk baru</strong> di atas.</p>
  </div>
<?php endif; ?>

<?php foreach ($produk as $p): $stok = (int) $p['stok']; ?>
  <form class="kartu-admin" method="post" enctype="multipart/form-data">
    <?= csrf_input() ?>
    <input type="hidden" name="aksi" value="simpan">
    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">

    <div class="baris">
      <div class="foto-saat-ini">
        <img src="<?= e(gambar_admin($p['gambar'])) ?>" alt="Foto <?= e($p['nama']) ?>">
        <label for="gambar-<?= (int) $p['id'] ?>">Ganti foto</label>
        <input type="file" id="gambar-<?= (int) $p['id'] ?>" name="gambar" accept=".jpg,.jpeg,.png,.webp">
        <p class="bantuan">JPG, PNG, atau WEBP. Maksimal 4 MB.</p>
      </div>

      <div>
        <span class="tanda-stok <?= $stok > 0 ? 'tanda-ada' : 'tanda-habis' ?>">
          <?= $stok > 0 ? 'Tersedia: ' . $stok : 'Stok habis' ?>
        </span>

        <label for="nama-<?= (int) $p['id'] ?>">Nama produk</label>
        <input type="text" id="nama-<?= (int) $p['id'] ?>" name="nama" value="<?= e($p['nama']) ?>" required>

        <label for="deskripsi-<?= (int) $p['id'] ?>">Deskripsi</label>
        <textarea id="deskripsi-<?= (int) $p['id'] ?>" name="deskripsi"><?= e($p['deskripsi']) ?></textarea>

        <label for="stok-<?= (int) $p['id'] ?>">Stok (jumlah yang bisa dijual)</label>
        <div class="stok-kontrol">
          <button type="button" class="tombol-bulat" data-langkah="-1" data-target="stok-<?= (int) $p['id'] ?>" aria-label="Kurangi stok 1">&minus;</button>
          <input type="number" id="stok-<?= (int) $p['id'] ?>" name="stok" min="0" inputmode="numeric" value="<?= $stok ?>">
          <button type="button" class="tombol-bulat" data-langkah="1" data-target="stok-<?= (int) $p['id'] ?>" aria-label="Tambah stok 1">+</button>
        </div>

        <label for="urutan-<?= (int) $p['id'] ?>">Urutan tampil (angka kecil tampil lebih dulu)</label>
        <input type="number" id="urutan-<?= (int) $p['id'] ?>" name="urutan" min="0" inputmode="numeric" value="<?= (int) $p['urutan'] ?>" style="max-width:200px;">
      </div>
    </div>

    <div class="aksi-kartu">
      <div class="kiri">
        <button type="submit" class="btn">Simpan perubahan</button>
      </div>
    </div>
  </form>

  <form method="post" style="margin:-20px 0 36px; text-align:right;" onsubmit="return confirm('Yakin hapus produk ini? Fotonya juga ikut dihapus.');">
    <?= csrf_input() ?>
    <input type="hidden" name="aksi" value="hapus">
    <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
    <button type="submit" class="btn btn-bahaya">Hapus produk "<?= e($p['nama']) ?>"</button>
  </form>
<?php endforeach; ?>

<details class="tambah-bagian" id="tambah-produk" open>
  <summary>+ Tambah produk baru</summary>
  <form class="kartu-admin" method="post" enctype="multipart/form-data">
    <?= csrf_input() ?>
    <input type="hidden" name="aksi" value="tambah">

    <label for="nama-baru">Nama produk</label>
    <input type="text" id="nama-baru" name="nama" required>

    <label for="foto-baru">Foto produk</label>
    <input type="file" id="foto-baru" name="gambar" accept=".jpg,.jpeg,.png,.webp" required>
    <p class="bantuan">JPG, PNG, atau WEBP. Maksimal 4 MB.</p>

    <label for="deskripsi-baru">Deskripsi</label>
    <textarea id="deskripsi-baru" name="deskripsi"></textarea>

    <label for="stok-baru">Stok awal</label>
    <div class="stok-kontrol">
      <button type="button" class="tombol-bulat" data-langkah="-1" data-target="stok-baru" aria-label="Kurangi stok 1">&minus;</button>
      <input type="number" id="stok-baru" name="stok" min="0" inputmode="numeric" value="0">
      <button type="button" class="tombol-bulat" data-langkah="1" data-target="stok-baru" aria-label="Tambah stok 1">+</button>
    </div>

    <label for="urutan-baru">Urutan tampil</label>
    <input type="number" id="urutan-baru" name="urutan" min="0" inputmode="numeric" value="<?= count($produk) + 1 ?>" style="max-width:200px;">

    <div class="aksi-kartu">
      <div class="kiri"><button type="submit" class="btn">Simpan produk baru</button></div>
    </div>
  </form>
</details>

<script>
  // Tombol − dan + untuk stok. Angka tidak boleh di bawah 0.
  document.querySelectorAll('[data-langkah]').forEach(function (tombol) {
    tombol.addEventListener('click', function () {
      var kotak = document.getElementById(tombol.dataset.target);
      var nilai = parseInt(kotak.value, 10) || 0;
      kotak.value = Math.max(0, nilai + parseInt(tombol.dataset.langkah, 10));
    });
  });
</script>

<?php admin_selesai(); ?>
