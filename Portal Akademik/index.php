<?php
session_start();
ob_start();

require_once __DIR__ . '/koneksi.php';

$modul = $_GET['modul'] ?? 'beranda';
$allowedModules = [
    'beranda', 'profil', 'mahasiswa', 'mahasiswa_tambah',
    'mahasiswa_edit', 'mahasiswa_hapus', 'kontak', 'login', 'logout', 'admin_konten'
];
$protectedModules = ['mahasiswa', 'mahasiswa_tambah', 'mahasiswa_edit', 'mahasiswa_hapus', 'admin_konten'];

if (in_array($modul, $protectedModules, true) && ($_SESSION['login_status'] ?? false) !== true) {
    header('Location: index.php?modul=login');
    exit;
}

include __DIR__ . '/header.php';

if (in_array($modul, $allowedModules, true)) {
    $fileTujuan = __DIR__ . '/pages/' . $modul . '.php';
    if (is_file($fileTujuan)) {
        include $fileTujuan;
    } else {
        echo '<p style="color:red;">Berkas konten <code>' . htmlspecialchars($modul, ENT_QUOTES, 'UTF-8') . '.php</code> tidak ditemukan.</p>';
    }
} else {
    echo '<h2 style="color:red;">Error 404: Halaman Tidak Ditemukan!</h2>';
    echo '<p>Modul yang Anda minta tidak terdaftar dalam sistem.</p>';
}

include __DIR__ . '/footer.php';
?>
