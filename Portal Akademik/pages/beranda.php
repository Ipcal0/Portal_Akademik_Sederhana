<?php
$defaultHome = [
    'slug' => 'beranda',
    'judul' => 'Selamat Datang di Beranda Utama',
    'gambar_url' => '',
    'isi' => 'Ini adalah halaman utama situs web dinamis. Silakan klik menu navigasi untuk mengetahui lebih lanjut.'
];
$home = $defaultHome;
$stmtHome = mysqli_prepare($conn, 'SELECT slug, judul, gambar_url, isi FROM konten_halaman WHERE halaman = ? LIMIT 1');
if ($stmtHome) {
    $namaHalaman = 'beranda';
    mysqli_stmt_bind_param($stmtHome, 's', $namaHalaman);
    mysqli_stmt_execute($stmtHome);
    mysqli_stmt_bind_result($stmtHome, $home['slug'], $home['judul'], $home['gambar_url'], $home['isi']);
    if (!mysqli_stmt_fetch($stmtHome)) $home = $defaultHome;
    mysqli_stmt_close($stmtHome);
}
$slugId = preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $home['slug']) ? $home['slug'] : 'beranda';
$gambar = trim($home['gambar_url']);
$gambarValid = $gambar !== '' && (preg_match('#^https?://#i', $gambar) ? filter_var($gambar, FILTER_VALIDATE_URL) : preg_match('#^(?!/|\\\\)(?!.*(?:^|/)\.\.(?:/|$))[a-zA-Z0-9_./-]+$#', $gambar));
?>
<article id="<?= htmlspecialchars($slugId, ENT_QUOTES, 'UTF-8') ?>">
  <h2 style="color:#2c3e50;margin-top:0;"><?= htmlspecialchars($home['judul'], ENT_QUOTES, 'UTF-8') ?></h2>
  <?php if ($gambarValid): ?>
    <img src="<?= htmlspecialchars($gambar, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($home['judul'], ENT_QUOTES, 'UTF-8') ?>" style="display:block;max-width:100%;max-height:360px;object-fit:cover;border-radius:8px;margin:16px 0;">
  <?php endif; ?>
  <p><?= nl2br(htmlspecialchars($home['isi'], ENT_QUOTES, 'UTF-8')) ?></p>
</article>
