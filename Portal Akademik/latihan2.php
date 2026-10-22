<?php
  $modul = $_GET['modul'] ?? 'beranda';

  // Daftar whitelist file halaman konten yang aman untuk dimuat
  $allowedModules = ['beranda', 'profil', 'mahasiswa', 'kontak'];

  if (in_array($modul, $allowedModules)) {
      $fileTarget = $modul . ".php";
      // Contoh: include $fileTarget;
      echo "File <code>{$fileTarget}</code> aman dan akan dimuat.";
  } else {
      echo "<p style='color:red;'>Error 404: Halaman tidak ditemukan!</p>";
  }
?>

