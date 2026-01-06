<?php
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login_admin.php");
    exit();
}

// Logika untuk Hapus Ulasan
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_ulasan = (int)$_GET['id'];
    $stmt = mysqli_prepare($koneksi, "DELETE FROM ulasan WHERE id_ulasan = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id_ulasan);
    mysqli_stmt_execute($stmt);
    $_SESSION['pesan'] = "Ulasan berhasil dihapus.";
    header("Location: ulasan.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Ulasan</title>
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
                <li class="active"><a href="ulasan.php">Kelola Ulasan</a></li>
                <li><a href="tambah_admin.php">Tambah Admin</a></li>
                <li><a href="../logout.php?from=admin">Logout</a></li>
            </ul>
        </nav>
    </aside>
    <main class="main-content">
        <header class="content-header"><h1>Kelola Ulasan Pelanggan</h1></header>
        <div class="content-body">
            <?php tampilkan_pesan(); ?>
            <div class="card">
                <h3>Semua Ulasan</h3>
                <table class="content-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Pelanggan</th>
                            <th>Rating</th>
                            <th>Ulasan</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_ulasan = "SELECT u.*, p.nama_produk, pg.nama_lengkap 
                                         FROM ulasan u 
                                         JOIN produk p ON u.id_produk = p.id_produk
                                         JOIN pengguna pg ON u.id_pengguna = pg.id_pengguna
                                         ORDER BY u.tanggal_ulasan DESC";
                        $hasil_ulasan = mysqli_query($koneksi, $query_ulasan);
                        while($ulasan = mysqli_fetch_assoc($hasil_ulasan)):
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ulasan['nama_produk']); ?></td>
                            <td><?php echo htmlspecialchars($ulasan['nama_lengkap']); ?></td>
                            <td><?php echo $ulasan['rating']; ?> ★</td>
                            <td><?php echo htmlspecialchars($ulasan['ulasan']); ?></td>
                            <td><?php echo date('d M Y', strtotime($ulasan['tanggal_ulasan'])); ?></td>
                            <td>
                                <a href="ulasan.php?action=delete&id=<?php echo $ulasan['id_ulasan']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus ulasan ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>