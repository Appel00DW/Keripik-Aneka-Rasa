<?php
require_once 'includes/koneksi.php';
require_once 'includes/fungsi.php';

// Logika untuk memproses registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_telp = mysqli_real_escape_string($koneksi, $_POST['no_telp']);

    $cek_email = mysqli_query($koneksi, "SELECT email FROM pengguna WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $_SESSION['pesan'] = "Email sudah terdaftar! Silakan login.";
    } else {
        $query = "INSERT INTO pengguna (nama_lengkap, email, password, alamat, no_telp, role) VALUES ('$nama', '$email', '$password', '$alamat', '$no_telp', 'pelanggan')";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['pesan'] = "Registrasi berhasil! Silakan login.";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['pesan'] = "Registrasi gagal, coba lagi.";
        }
    }
    header("Location: register.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun</title>
    <link rel="stylesheet" href="assets/css/style-login.css">
</head>
<body>
    <div class="form-container">
        <h2>Buat Akun Baru</h2>
        
        <?php tampilkan_pesan(); ?>
        
        <form action="register.php" method="post">
            <div class="form-grup">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" required placeholder="Contoh: Budi Santoso">
            </div>
            <div class="form-grup">
                <label>Email</label>
                <input type="email" name="email" required placeholder="Contoh: budi@email.com">
            </div>
            <div class="form-grup">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Buat password yang kuat">
            </div>
             <div class="form-grup">
                <label>Alamat</label>
                <input type="text" name="alamat" required placeholder="Masukkan Alamat">
            </div>
             <div class="form-grup">
                <label>No. Telepon</label>
                <input type="text" name="no_telp" required placeholder="Masukkan Nomor Telepon">
            </div>
            <button type="submit" class="btn">Daftar</button>
        </form>

        <p class="form-text-info">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </p>
    </div>
</body>
</html>