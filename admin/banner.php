<?php
require_once __DIR__ . '/includes/layout.php';
require_login();

$halaman = 'banner.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    wajib_csrf($halaman);
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'hapus') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('SELECT gambar FROM banner WHERE id = ?');
        $stmt->execute([$id]);
        if ($baris = $stmt->fetch()) {
            $pdo->prepare('DELETE FROM banner WHERE id = ?')->execute([$id]);
            hapus_upload($baris['gambar']);
            flash('ok', 'Banner berhasil dihapus.');
        }
        kembali($halaman);
    }

    if ($aksi === 'simpan' || $aksi === 'tambah') {
        $id = (int) ($_POST['id'] ?? 0);
        $judul = trim($_POST['judul'] ?? '');
        $subjudul = trim($_POST['subjudul'] ?? '');
        $urutan = (int) ($_POST['urutan'] ?? 0);
        $aktif = isset($_POST['aktif']) ? 1 : 0;
        $error = '';

        if ($judul === '') {
            $error = 'Judul banner wajib diisi.';
        }

        $gambarBaru = null;
        if ($error === '') {
            $gambarBaru = simpan_gambar('gambar', 'banner', $error);
        }

        if ($error === '' && $aksi === 'tambah') {
            if ($gambarBaru === null) {
                $error = 'Foto banner wajib dipilih untuk banner baru.';
            } else {
                $pdo->prepare('INSERT INTO banner (judul, subjudul, gambar, urutan, aktif) VALUES (?, ?, ?, ?, ?)')
                    ->execute([$judul, $subjudul, $gambarBaru, $urutan, $aktif]);
                flash('ok', 'Banner baru berhasil ditambahkan.');
                kembali($halaman);
            }
        }

        if ($error === '' && $aksi === 'simpan') {
            $stmt = $pdo->prepare('SELECT gambar FROM banner WHERE id = ?');
            $stmt->execute([$id]);
            $lama = $stmt->fetch();
            if (!$lama) {
                $error = 'Banner tidak ditemukan.';
            } else {
                if ($gambarBaru !== null) {
                    $pdo->prepare('UPDATE banner SET judul=?, subjudul=?, urutan=?, aktif=?, gambar=? WHERE id=?')
                        ->execute([$judul, $subjudul, $urutan, $aktif, $gambarBaru, $id]);
                    hapus_upload($lama['gambar']);
                } else {
                    $pdo->prepare('UPDATE banner SET judul=?, subjudul=?, urutan=?, aktif=? WHERE id=?')
                        ->execute([$judul, $subjudul, $urutan, $aktif, $id]);
                }
                flash('ok', 'Perubahan banner sudah disimpan.');
                kembali($halaman);
            }
        }

        flash('error', $error);
        kembali($halaman);
    }
}

$banners = $pdo->query('SELECT * FROM banner ORDER BY urutan ASC, id ASC')->fetchAll();

admin_mulai('Banner', 'banner');
?>

<p>Banner adalah foto besar di bagian paling atas website. Kalau ada lebih dari satu banner yang tampil, pengunjung bisa menggesernya ke samping.</p>
<p style="margin-bottom:24px;">
  <a class="btn" href="#tambah-banner">+ Tambah banner baru</a>
</p>

<?php if (empty($banners)): ?>
  <div class="kartu-admin">
    <p>Belum ada banner. Website akan memakai banner bawaan.</p>
  </div>
<?php endif; ?>

<?php foreach ($banners as $b): ?>
  <form class="kartu-admin" method="post" enctype="multipart/form-data">
    <?= csrf_input() ?>
    <input type="hidden" name="aksi" value="simpan">
    <input type="hidden" name="id" value="<?= (int) $b['id'] ?>">

    <span class="tanda-stok <?= $b['aktif'] ? 'tanda-ada' : 'tanda-habis' ?>">
      <?= $b['aktif'] ? 'Sedang tampil di website' : 'Disembunyikan' ?>
    </span>

    <div class="baris">
      <div class="foto-saat-ini">
        <img src="<?= e(gambar_admin($b['gambar'])) ?>" alt="Banner <?= e($b['judul']) ?>">
        <label for="gambar-<?= (int) $b['id'] ?>">Ganti foto banner</label>
        <input type="file" id="gambar-<?= (int) $b['id'] ?>" name="gambar" accept=".jpg,.jpeg,.png,.webp">
        <p class="bantuan">Pakai foto mendatar (landscape). JPG, PNG, atau WEBP, maksimal 4 MB.</p>
      </div>

      <div>
        <label for="judul-<?= (int) $b['id'] ?>">Judul</label>
        <input type="text" id="judul-<?= (int) $b['id'] ?>" name="judul" value="<?= e($b['judul']) ?>" required>

        <label for="subjudul-<?= (int) $b['id'] ?>">Teks di bawah judul</label>
        <textarea id="subjudul-<?= (int) $b['id'] ?>" name="subjudul"><?= e($b['subjudul']) ?></textarea>

        <label for="urutan-<?= (int) $b['id'] ?>">Urutan tampil (angka kecil tampil lebih dulu)</label>
        <input type="number" id="urutan-<?= (int) $b['id'] ?>" name="urutan" min="0" inputmode="numeric" value="<?= (int) $b['urutan'] ?>" style="max-width:200px;">

        <label class="cek-baris">
          <input type="checkbox" name="aktif" value="1" <?= $b['aktif'] ? 'checked' : '' ?>>
          Tampilkan banner ini di website
        </label>
      </div>
    </div>

    <div class="aksi-kartu">
      <div class="kiri">
        <button type="submit" class="btn">Simpan perubahan</button>
      </div>
    </div>
  </form>

  <form method="post" style="margin:-20px 0 36px; text-align:right;" onsubmit="return confirm('Yakin hapus banner ini? Fotonya juga ikut dihapus.');">
    <?= csrf_input() ?>
    <input type="hidden" name="aksi" value="hapus">
    <input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
    <button type="submit" class="btn btn-bahaya">Hapus banner ini</button>
  </form>
<?php endforeach; ?>

<details class="tambah-bagian" id="tambah-banner" open>
  <summary>+ Tambah banner baru</summary>
  <form class="kartu-admin" method="post" enctype="multipart/form-data">
    <?= csrf_input() ?>
    <input type="hidden" name="aksi" value="tambah">

    <label for="judul-baru">Judul</label>
    <input type="text" id="judul-baru" name="judul" required>

    <label for="subjudul-baru">Teks di bawah judul</label>
    <textarea id="subjudul-baru" name="subjudul"></textarea>

    <label for="foto-baru">Foto banner</label>
    <input type="file" id="foto-baru" name="gambar" accept=".jpg,.jpeg,.png,.webp" required>
    <p class="bantuan">Pakai foto mendatar (landscape). JPG, PNG, atau WEBP, maksimal 4 MB.</p>

    <label for="urutan-baru">Urutan tampil</label>
    <input type="number" id="urutan-baru" name="urutan" min="0" inputmode="numeric" value="<?= count($banners) + 1 ?>" style="max-width:200px;">

    <label class="cek-baris">
      <input type="checkbox" name="aktif" value="1" checked>
      Tampilkan banner ini di website
    </label>

    <div class="aksi-kartu">
      <div class="kiri"><button type="submit" class="btn">Simpan banner baru</button></div>
    </div>
  </form>
</details>

<?php admin_selesai(); ?>
