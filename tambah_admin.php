<?php
// ===== KODE KEAMANAN & LOGIKA PHP =====
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login_admin.php");
    exit();
}
// Logika untuk memproses pendaftaran admin baru
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $cek_email = mysqli_query($koneksi, "SELECT email FROM pengguna WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $_SESSION['pesan'] = "Email sudah digunakan!";
    } else {
        $query = "INSERT INTO pengguna (nama_lengkap, email, password, role) VALUES ('$nama', '$email', '$password', 'admin')";
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['pesan'] = "Admin baru berhasil ditambahkan.";
        } else {
            $_SESSION['pesan'] = "Gagal menambahkan admin.";
        }
    }
    header("Location: tambah_admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Admin Baru</title>
    <link rel="stylesheet" href="../assets/css/style-admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="admin-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header"><h2 class="logo">AdminPanel</h2></div>
       <nav class="sidebar-nav">
    <ul>
        <li><a href="index.php">Dashboard</a></li>
        <li><a href="produk.php">Kelola Produk</a></li>
        <li><a href="voucher.php">Kelola Voucher</a></li>
        <li><a href="pesanan.php">Kelola Pesanan</a></li>
         <li ><a href="ulasan.php">Kelola Ulasan</a></li>
        <li><a href="tambah_admin.php">Tambah Admin</a></li>
        <li><a href="../logout.php?from=admin">Logout</a></li>
    </ul>
</nav>
    </aside>
    <main class="main-content">
        <header class="content-header"><h1>Tambah Admin Baru</h1></header>
        <div class="content-body">
            <div class="card">
                <h3>Form Pendaftaran Admin</h3>
                <?php tampilkan_pesan(); ?>
                <form action="tambah_admin.php" method="post">
                    <div class="form-grup">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" required>
                    </div>
                    <div class="form-grup">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-grup">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambahkan Admin</button>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>