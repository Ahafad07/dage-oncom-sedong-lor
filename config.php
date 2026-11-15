<?php
/**
 * KONFIGURASI DATABASE
 * --------------------
 * Kredensial database TIDAK ditulis di file ini, supaya aman saat di-upload ke GitHub.
 *
 * Cara pakai:
 *   1. Salin config.example.php menjadi config.local.php (di folder yang sama)
 *   2. Isi DB_NAME, DB_USER, DB_PASS sesuai database yang kamu buat di cPanel
 *
 * config.local.php sudah masuk .gitignore, jadi tidak ikut ter-upload.
 * Tanpa config.local.php, website publik tetap tampil memakai teks dan produk bawaan.
 */

$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    require_once $localConfig;
}

if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', '');
}
if (!defined('DB_USER')) {
    define('DB_USER', '');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}

$pdo = null;
$db_error = null;

if (DB_NAME === '') {
    $db_error = 'config.local.php belum dibuat, atau DB_NAME masih kosong.';
} else {
    try {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } catch (PDOException $e) {
        // Jangan hentikan seluruh website. Halaman publik tetap tampil
        // memakai data cadangan, dan admin panel menampilkan pesan error sendiri.
        $pdo = null;
        $db_error = $e->getMessage();
    }
}
