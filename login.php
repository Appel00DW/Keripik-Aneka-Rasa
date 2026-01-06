<?php
require_once 'includes/koneksi.php';
require_once 'includes/fungsi.php';

// Logika untuk memproses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM pengguna WHERE email = '$email' AND role = 'pelanggan'";
    $result = mysqli_query($koneksi, $query);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['id_pengguna'] = $user['id_pengguna'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            
            // PERUBAHAN DI SINI: Tambahkan parameter ?login=success
            header("Location: index.php?login=success");
            exit();
        }
    }
    $_SESSION['pesan'] = "Email atau password salah!";
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pelanggan</title>
    <link rel="stylesheet" href="assets/css/style-login.css">
</head>
<body>
    <div class="form-container">
        <h2>Selamat Datang</h2>
        <?php tampilkan_pesan(); ?>
        <form action="login.php" method="post">
            <div class="form-grup">
                <label>Email</label>
                <input type="email" name="email" required placeholder="Masukkan email Anda">
            </div>
            <div class="form-grup">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Masukkan password">
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <p class="form-text-info">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </p>
    </div>
</body>
</html>