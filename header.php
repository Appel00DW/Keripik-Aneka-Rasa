<?php require_once 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Sederhana</title>
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">

</head>
<body>
    <header>
        <nav>
            <a href="<?php echo BASE_URL; ?>/index.php" class="logo">TokoKeripik</a>
            <ul>
                <li><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>/keranjang.php">Keranjang</a></li>
                <?php if (isset($_SESSION['id_pengguna'])) : ?>
                    <li><a href="<?php echo BASE_URL; ?>/riwayat_pesanan.php">Riwayat</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/logout.php">Logout (<?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>)</a></li>
                <?php else : ?>
                    <li><a href="<?php echo BASE_URL; ?>/login.php">Login</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main class="container">