<?php
// ===== KODE KEAMANAN & LOGIKA PHP =====
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login_admin.php");
    exit();
}

// Proses update status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $id_pesanan = $_POST['id_pesanan'];
    $status_baru = $_POST['status_pesanan'];
    mysqli_query($koneksi, "UPDATE pesanan SET status_pesanan='$status_baru' WHERE id_pesanan=$id_pesanan");
    $_SESSION['pesan'] = "Status pesanan berhasil diupdate.";
    header("Location: pesanan.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan</title>
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
        <header class="content-header"><h1>Kelola Pesanan</h1></header>
        <div class="content-body">
            <?php tampilkan_pesan(); ?>
            <div class="card">
                <h3>Daftar Pesanan Masuk</h3>
                <table class="content-table">
                    <thead>
                        <tr>
                            <th>ID Pesanan</th><th>Nama Pelanggan</th><th>Tanggal</th>
                            <th>Total</th><th>Status</th><th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT pesanan.*, pengguna.nama_lengkap FROM pesanan JOIN pengguna ON pesanan.id_pengguna = pengguna.id_pengguna ORDER BY tanggal_pesanan DESC";
                        $result = mysqli_query($koneksi, $query);
                        while ($pesanan = mysqli_fetch_assoc($result)):
                        ?>
                        <tr>
                            <td>#<?php echo $pesanan['id_pesanan']; ?></td>
                            <td><?php echo htmlspecialchars($pesanan['nama_lengkap']); ?></td>
                            <td><?php echo date('d M Y, H:i', strtotime($pesanan['tanggal_pesanan'])); ?></td>
                            <td><?php echo format_rupiah($pesanan['total_harga']); ?></td>
                            <td>
    <span class="status status-<?php echo strtolower($pesanan['status_pesanan']); ?>">
        <?php echo ucfirst($pesanan['status_pesanan']); ?>
    </span>
</td>
                            <td>
                                <form action="pesanan.php" method="post" style="display:flex; gap:5px;">
                                    <input type="hidden" name="id_pesanan" value="<?php echo $pesanan['id_pesanan']; ?>">
                                    <select name="status_pesanan" class="form-grup">
                                        <option value="pending" <?php if($pesanan['status_pesanan']=='pending') echo 'selected'; ?>>Pending</option>
                                        <option value="diproses" <?php if($pesanan['status_pesanan']=='diproses') echo 'selected'; ?>>Diproses</option>
                                        <option value="dikirim" <?php if($pesanan['status_pesanan']=='dikirim') echo 'selected'; ?>>Dikirim</option>
                                        <option value="selesai" <?php if($pesanan['status_pesanan']=='selesai') echo 'selected'; ?>>Selesai</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                                </form>
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