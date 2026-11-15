# Website Dage Abdulrohman

Website profil dan katalog untuk usaha Dage dan Oncom Hideng milik Pak Abdulrohman di Sedong Lor, Kabupaten Cirebon. Dibuat sebagai bagian dari program KKM UMC 2026.

## Fitur

- **Halaman publik:** banner (bisa digeser), tentang pemilik, cara membuat, daftar produk dengan stok, dan kontak (WhatsApp, alamat, jam buka).
- **Panel admin** (`/admin`), dibuat supaya mudah dipakai:
  - **Produk dan stok:** ubah foto, nama, deskripsi, stok (tombol − dan +), dan urutan langsung di kartu produk. Tambah dan hapus produk.
  - **Banner:** ubah judul, teks, dan foto. Atur tampil atau sembunyi tanpa menghapus.
  - **Pengaturan:** logo, foto pemilik, teks bagian Tentang, alamat, nomor WhatsApp, dan jam buka.
  - **Ganti password.**

## Teknologi

PHP 8 dengan PDO, MySQL atau MariaDB, HTML, dan CSS biasa tanpa framework. Tidak ada JavaScript untuk halaman publik.

## Struktur folder

```
├── index.php              halaman publik
├── config.php             pemuat konfigurasi database (aman di-commit)
├── config.example.php     contoh isi config.local.php
├── assets/style.css       tampilan halaman publik
├── admin/
│   ├── login.php          masuk admin
│   ├── dashboard.php      produk dan stok
│   ├── banner.php         banner
│   ├── pengaturan.php     pengaturan website
│   ├── ganti-password.php
│   ├── includes/          fungsi bersama (login, CSRF, unggah foto, tampilan admin)
│   └── assets/admin.css   tampilan admin
├── database/
│   ├── schema.sql         instalasi baru (versi 2)
│   └── migrasi-v2.sql     untuk database versi 1 yang sudah berjalan
├── images/                foto dan logo bawaan
└── uploads/               foto hasil unggahan admin
```

## Menjalankan di komputer sendiri

1. Pastikan PHP 8 dan MariaDB/MySQL sudah terpasang (atau pakai XAMPP / Laragon).
2. Buat database kosong, lalu import `database/schema.sql`.
3. Buat akun admin (lihat bagian di bawah).
4. Salin `config.example.php` menjadi `config.local.php`, lalu isi kredensialnya.
5. Di folder ini, jalankan `php -S localhost:8000`, lalu buka `http://localhost:8000`.

Tanpa database, halaman publik tetap tampil memakai teks dan produk bawaan. Panel admin hanya bisa dipakai setelah database tersambung.

## Memasang di hosting cPanel

1. Buat database dan user di **cPanel > MySQL Databases**, lalu beri user itu semua hak akses ke database tersebut.
2. Buka **phpMyAdmin**, pilih database tadi, lalu import `database/schema.sql`.
3. Buat akun admin (lihat bagian di bawah).
4. Salin `config.example.php` menjadi `config.local.php`, lalu isi DB_NAME, DB_USER, dan DB_PASS.
5. Upload semua file ke `public_html`, **kecuali** `config.local.php` yang hanya dibuat di server.

### Kalau database sudah berjalan (versi 1)

Jalankan `database/migrasi-v2.sql` lewat phpMyAdmin. File ini menambah kolom stok pada produk dan membuat tabel banner. Jalankan sekali saja.

### Membuat akun admin

Jalankan perintah ini di terminal, ganti `password-kamu` dengan password yang kuat:

```bash
php -r "echo password_hash('password-kamu', PASSWORD_DEFAULT);"
```

Lalu jalankan di phpMyAdmin, dengan hasil hash dari perintah tadi:

```sql
INSERT INTO admin (username, password_hash) VALUES ('admin', 'HASIL_HASH_DI_SINI');
```

## Catatan keamanan

- Form admin sudah memakai token CSRF. Sesi login diperbarui setiap kali masuk.
- Foto diperiksa jenisnya dari isi file (bukan hanya ekstensi) dan dibatasi 4 MB.
- Belum ada pembatasan percobaan login. Cocok untuk pemakaian internal. Perlu ditambah sebelum dipakai lebih luas.
- Foto bawaan di `images/` tidak ikut terhapus saat produk atau banner dihapus. Hanya foto di `uploads/` yang dihapus.
