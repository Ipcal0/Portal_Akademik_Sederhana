<?php
if (($_SESSION['login_status'] ?? false) === true) {
    echo "<script>window.location='index.php?modul=mahasiswa';</script>";
    exit;
}

$pesanError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Mengikuti format praktikum: kolom password admin_users berisi password teks biasa.
    // Untuk aplikasi nyata, simpan hash password dan verifikasi dengan password_verify().
    $stmt = mysqli_prepare($conn, 'SELECT username, password, nama_lengkap FROM admin_users WHERE username = ? LIMIT 1');
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);
    } else {
        $user = null;
        $pesanError = 'Login belum dapat diproses. Pastikan tabel admin_users sudah dibuat.';
    }

    if ($pesanError === '' && $user && hash_equals((string) $user['password'], (string) $password)) {
        session_regenerate_id(true);
        $_SESSION['login_status'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        setcookie('last_user', $username, time() + 86400, '/');
        echo "<script>alert('Login berhasil, selamat datang!'); window.location='index.php?modul=mahasiswa';</script>";
        exit;
    }
    if ($pesanError === '') {
        $pesanError = 'Username atau password salah!';
    }
}

$savedUser = $_COOKIE['last_user'] ?? '';
?>

<h2 style="color:#2c3e50; margin-top:0;">Form Login Administrator</h2>
<p>Silakan masukkan kredensial akun Anda untuk mengakses fitur pengelolaan data mahasiswa.</p>

<?php if ($pesanError !== ''): ?>
  <div style="background:#fadbd8; color:#900c3f; padding:10px; border-radius:4px; margin-bottom:15px;">
    <?= htmlspecialchars($pesanError, ENT_QUOTES, 'UTF-8') ?>
  </div>
<?php endif; ?>

<form action="" method="POST">
  <div style="margin-bottom:12px;">
    <label for="username" style="display:block; font-weight:bold; margin-bottom:5px;">Username:</label>
    <input id="username" type="text" name="username" value="<?= htmlspecialchars($savedUser, ENT_QUOTES, 'UTF-8') ?>" required style="width:100%; padding:8px; box-sizing:border-box;">
  </div>
  <div style="margin-bottom:15px;">
    <label for="password" style="display:block; font-weight:bold; margin-bottom:5px;">Password:</label>
    <input id="password" type="password" name="password" required style="width:100%; padding:8px; box-sizing:border-box;">
  </div>
  <button type="submit" name="login" style="background:#27ae60; color:white; padding:10px 15px; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">Masuk Sistem</button>
</form>
