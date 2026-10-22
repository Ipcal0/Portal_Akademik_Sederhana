<?php
function ambilKontenHalaman(mysqli $conn, string $halaman, string $default): string
{
    $stmt = mysqli_prepare($conn, 'SELECT isi FROM konten_halaman WHERE halaman = ? LIMIT 1');
    if (!$stmt) {
        return $default;
    }
    mysqli_stmt_bind_param($stmt, 's', $halaman);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $isi);
    $ditemukan = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $ditemukan ? $isi : $default;
}

function tampilkanKontenHalaman(string $isi): void
{
    echo nl2br(htmlspecialchars($isi, ENT_QUOTES, 'UTF-8'));
}

function ambilJudulHalaman(mysqli $conn, string $halaman, string $default): string
{
    $stmt = mysqli_prepare($conn, 'SELECT judul FROM konten_halaman WHERE halaman = ? LIMIT 1');
    if (!$stmt) return $default;
    mysqli_stmt_bind_param($stmt, 's', $halaman);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $judul);
    $ada = mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $ada && $judul !== '' ? $judul : $default;
}
?>
