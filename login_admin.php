<?php
// Perbaiki path di sini dengan menambahkan ../
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';

// Logika untuk memproses login tetap sama
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM pengguna WHERE email = '$email' AND role = 'admin'";
    $result = mysqli_query($koneksi, $query);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['id_pengguna'] = $user['id_pengguna'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit();
        }
    }
    $_SESSION['pesan'] = "Email atau password admin salah!";
    // Arahkan kembali ke halaman login untuk menampilkan pesan error
    header("Location: login_admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="../assets/css/style-login.css">
</head>

<body>
<div class="container">
    
    <div class="form-container">
        <h2>Login Admin</h2>

        <?php 
        // Fungsi ini sekarang akan berjalan karena 'fungsi.php' sudah berhasil dimuat
        tampilkan_pesan(); 
        ?>

        <form action="login_admin.php" method="post">
            <div class="form-grup">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-grup">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>

</div>
</body>
</html>