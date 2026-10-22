<?php
  // Menangkap parameter 'modul' dari URL (contoh: index.php?modul=profil)
  // Jika parameter tidak ada, berikan nilai default "beranda"
  $modul = $_GET['modul'] ?? 'beranda';

  echo "Modul yang diminta saat ini: <strong>" . htmlspecialchars($modul) . "</strong>";
?>

