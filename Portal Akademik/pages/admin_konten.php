<?php
$buatTabel = "CREATE TABLE IF NOT EXISTS konten_halaman (
    halaman VARCHAR(30) NOT NULL PRIMARY KEY,
    slug VARCHAR(150) NOT NULL DEFAULT 'beranda',
    judul VARCHAR(150) NOT NULL DEFAULT '',
    gambar_url VARCHAR(500) NOT NULL DEFAULT '',
    isi TEXT NOT NULL,
    diperbarui TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (!mysqli_query($conn, $buatTabel)) {
    echo '<p style="color:#c0392b;">Tabel konten tidak dapat disiapkan: ' . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8') . '</p>';
} else {
    $kolomBaru = [
        'slug' => "VARCHAR(150) NOT NULL DEFAULT 'beranda'",
        'judul' => "VARCHAR(150) NOT NULL DEFAULT ''",
        'gambar_url' => "VARCHAR(500) NOT NULL DEFAULT ''"
    ];
    foreach ($kolomBaru as $namaKolom => $definisi) {
        $cek = mysqli_query($conn, "SHOW COLUMNS FROM konten_halaman LIKE '$namaKolom'");
        if ($cek && mysqli_num_rows($cek) === 0) {
            mysqli_query($conn, "ALTER TABLE konten_halaman ADD `$namaKolom` $definisi");
        }
    }

    $slug = 'beranda';
    $judul = 'Selamat Datang di Beranda Utama';
    $gambarUrl = '';
    $isi = "Ini adalah halaman utama situs web dinamis yang dibangun menggunakan teknik routing URL parameter dan fungsi include PHP.\nSilakan klik menu navigasi di atas untuk berpindah halaman.";
    $stmtAwal = mysqli_prepare($conn, 'INSERT IGNORE INTO konten_halaman (halaman, slug, judul, gambar_url, isi) VALUES (?, ?, ?, ?, ?)');
    $halaman = 'beranda';
    mysqli_stmt_bind_param($stmtAwal, 'sssss', $halaman, $slug, $judul, $gambarUrl, $isi);
    mysqli_stmt_execute($stmtAwal);
    mysqli_stmt_close($stmtAwal);

    $pesan = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_konten'])) {
        $slug = 'beranda';
        $judul = trim($_POST['judul'] ?? '');
        $gambarUrl = trim($_POST['gambar_url'] ?? '');
        $isi = trim($_POST['isi'] ?? '');
        if ($gambarUrl !== '' && !filter_var($gambarUrl, FILTER_VALIDATE_URL) && !preg_match('#^(?!/|\\\\)(?!.*(?:^|/)\.\.(?:/|$))[a-zA-Z0-9_./-]+$#', $gambarUrl)) {
            $pesan = 'URL gambar harus berupa alamat URL yang valid atau path gambar lokal.';
        } else {
            $halaman = 'beranda';
            $simpan = mysqli_prepare($conn, 'INSERT INTO konten_halaman (halaman, slug, judul, gambar_url, isi) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE slug = VALUES(slug), judul = VALUES(judul), gambar_url = VALUES(gambar_url), isi = VALUES(isi)');
            mysqli_stmt_bind_param($simpan, 'sssss', $halaman, $slug, $judul, $gambarUrl, $isi);
            if (mysqli_stmt_execute($simpan)) {
                $pesan = 'Konten Beranda berhasil disimpan.';
            } else {
                $pesan = 'Konten gagal disimpan: ' . mysqli_stmt_error($simpan);
            }
            mysqli_stmt_close($simpan);
        }
    }

    $ambil = mysqli_prepare($conn, 'SELECT slug, judul, gambar_url, isi FROM konten_halaman WHERE halaman = ? LIMIT 1');
    $halaman = 'beranda';
    mysqli_stmt_bind_param($ambil, 's', $halaman);
    mysqli_stmt_execute($ambil);
    mysqli_stmt_bind_result($ambil, $slugForm, $judulForm, $gambarForm, $isiForm);
    mysqli_stmt_fetch($ambil);
    mysqli_stmt_close($ambil);

    echo '<h2 style="color:#2c3e50;margin-top:0;">Admin: Kelola Beranda</h2>';
    echo '<p>Form ini hanya mengelola konten halaman Beranda.</p>';
    if ($pesan !== '') {
        $warna = strpos($pesan, 'berhasil') !== false ? '#218c4a' : '#c0392b';
        echo '<p style="color:' . $warna . ';">' . htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') . '</p>';
    }
    echo '<form method="post">';
    echo '<div style="margin:14px 0;"><label for="slug"><strong>Slug</strong></label><input id="slug" type="text" value="beranda" readonly aria-readonly="true" style="display:block;width:100%;padding:9px;box-sizing:border-box;margin-top:5px;background:#f0f0f0;color:#666;box-shadow:inset 0 1px 4px rgba(0,0,0,.18);cursor:not-allowed;"><small style="color:#777;">Slug Beranda dikunci dan tidak dapat diubah.</small></div>';
    echo '<div style="margin:14px 0;"><label for="judul"><strong>Judul</strong></label><input id="judul" name="judul" maxlength="150" required value="' . htmlspecialchars($judulForm ?? '', ENT_QUOTES, 'UTF-8') . '" style="display:block;width:100%;padding:9px;box-sizing:border-box;margin-top:5px;"></div>';
    echo '<div style="margin:14px 0;"><label for="gambar_url"><strong>URL Gambar</strong></label><input id="gambar_url" name="gambar_url" type="text" maxlength="500" placeholder="https://... atau assets/images/..." value="' . htmlspecialchars($gambarForm ?? '', ENT_QUOTES, 'UTF-8') . '" style="display:block;width:100%;padding:9px;box-sizing:border-box;margin-top:5px;"></div>';
    echo '<div style="margin:14px 0;"><label for="isi"><strong>Konten</strong></label><textarea id="isi" name="isi" rows="8" required style="display:block;width:100%;padding:10px;box-sizing:border-box;margin-top:5px;resize:vertical;">' . htmlspecialchars($isiForm ?? '', ENT_QUOTES, 'UTF-8') . '</textarea></div>';
    echo '<button type="submit" name="simpan_konten" style="background:#27ae60;color:white;padding:10px 15px;border:0;border-radius:4px;cursor:pointer;">Simpan Perubahan</button></form>';
}
?>
