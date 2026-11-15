<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config.php';

function e($nilai)
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES, 'UTF-8');
}

function tampil_belum_terhubung()
{
    global $db_error;
    http_response_code(503);
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Database belum tersambung</title>';
    echo '<link rel="stylesheet" href="assets/admin.css"></head><body><main class="kotak-pesan">';
    echo '<h1>Database belum tersambung</h1>';
    echo '<p>Salin <code>config.example.php</code> menjadi <code>config.local.php</code> di folder utama website, lalu isi DB_NAME, DB_USER, dan DB_PASS sesuai database di cPanel.</p>';
    echo '<p class="teknis">Detail teknis: ' . e($db_error ?? 'tidak diketahui') . '</p>';
    echo '</main></body></html>';
}

function require_login()
{
    global $pdo;
    if ($pdo === null) {
        tampil_belum_terhubung();
        exit;
    }
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

// Token CSRF: setiap form admin wajib membawa token ini
function csrf_token()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_input()
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

// Pesan sekali tampil (disimpan di session lalu dihapus)
function flash($jenis, $pesan)
{
    $_SESSION['flash'] = ['jenis' => $jenis, 'pesan' => $pesan];
}

function ambil_flash()
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

// Dipanggil di awal setiap POST. Kalau token salah, kembali ke halaman asal.
function wajib_csrf($halamanAsal)
{
    if (!hash_equals(csrf_token(), $_POST['csrf'] ?? '')) {
        flash('error', 'Halaman sudah kedaluwarsa. Silakan coba lagi.');
        header('Location: ' . $halamanAsal);
        exit;
    }
}

function kembali($halaman)
{
    header('Location: ' . $halaman);
    exit;
}
