-- Migrasi dari versi 1 ke versi 2 (menambah stok produk dan tabel banner).
-- Jalankan SEKALI di phpMyAdmin pada database yang sudah ada.
-- Kalau baris ALTER error karena kolom stok sudah ada, lewati baris itu saja.

ALTER TABLE produk ADD COLUMN stok INT NOT NULL DEFAULT 0 AFTER deskripsi;

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

INSERT INTO banner (judul, subjudul, gambar, urutan, aktif) VALUES
('Dage dan Oncom Hideng dari Sedong Lor', 'Dibuat di rumah dengan cara lama: direndam, difermentasi alami tanpa pengawet, lalu dijemur di atas anyaman bambu.', 'dage-proses.jpeg', 1, 1);
