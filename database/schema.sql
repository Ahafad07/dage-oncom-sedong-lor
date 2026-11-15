-- Struktur database website Dage Abdulrohman (versi 2)
-- Jalankan sekali di database kosong lewat phpMyAdmin.
-- Akun admin TIDAK ada di file ini. Buat sendiri, lihat README.md.
-- Kalau database lama sudah ada, pakai database/migrasi-v2.sql saja.

CREATE TABLE IF NOT EXISTS admin (
  id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS pengaturan (
  nama_setting VARCHAR(100) NOT NULL,
  nilai TEXT DEFAULT NULL,
  PRIMARY KEY (nama_setting)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS produk (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nama VARCHAR(150) NOT NULL,
  deskripsi TEXT DEFAULT NULL,
  stok INT NOT NULL DEFAULT 0,
  gambar VARCHAR(255) NOT NULL,
  urutan INT(11) DEFAULT 0,
  created_at TIMESTAMP NULL DEFAULT current_timestamp(),
  updated_at TIMESTAMP NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS banner (
  id INT(11) NOT NULL AUTO_INCREMENT,
  judul VARCHAR(150) NOT NULL,
  subjudul TEXT DEFAULT NULL,
  gambar VARCHAR(255) NOT NULL,
  urutan INT(11) DEFAULT 0,
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Teks awal. Semuanya bisa diubah lewat Panel Admin.
INSERT INTO pengaturan (nama_setting, nilai) VALUES
('alamat', '4HPP+QJF, Kapten Mustofa, Sedong Lor, Kec. Sedong, Kab. Cirebon, Jawa Barat 45189'),
('foto_pemilik', 'pemilik.jpeg'),
('jam_operasional', 'Setiap hari, 02.00 - 07.00 WIB'),
('logo', 'logo.jpeg'),
('nomor_wa', '6285199336859'),
('tentang_judul', 'Dibuat langsung oleh Pak Abdulrohman'),
('tentang_paragraf1', 'Usaha ini dijalankan Pak Abdulrohman bersama keluarganya di Sedong Lor. Bahan dipilih, difermentasi secara alami tanpa pengawet, dengan resep turun-temurun.'),
('tentang_paragraf2', 'Bagi warga Sedong Lor, dage sudah jadi bagian dari makanan sehari-hari dan tradisi yang masih dijaga.');

-- Banner awal. Gambar harus ada di folder images/ atau uploads/.
INSERT INTO banner (judul, subjudul, gambar, urutan, aktif) VALUES
('Dage dan Oncom Hideng dari Sedong Lor', 'Dibuat di rumah dengan cara lama: direndam, difermentasi alami tanpa pengawet, lalu dijemur di atas anyaman bambu.', 'dage-proses.jpeg', 1, 1);

-- Produk awal. Angka stok hanya contoh, ubah di Panel Admin.
INSERT INTO produk (id, nama, deskripsi, stok, gambar, urutan) VALUES
(1, 'Dage Segar', 'Teksturnya padat dengan aroma fermentasi yang khas.', 20, 'dage-proses.jpeg', 1),
(2, 'Kemasan Harian', 'Tersedia dalam potongan sesuai kebutuhan, untuk dapur rumah atau dijual lagi.', 15, 'produk.jpeg', 2),
(3, 'Kemasan Tutug Oncom', 'Oncom yang ditumbuk halus dan dibumbui khas Sunda, cocok dimakan dengan nasi hangat.', 10, 'tutug-oncom.jpg', 3);
