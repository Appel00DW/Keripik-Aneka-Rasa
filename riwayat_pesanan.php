<?php
require_once 'includes/koneksi.php';
require_once 'includes/fungsi.php';

if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}

$id_pengguna = $_SESSION['id_pengguna'];
$query = "SELECT * FROM pesanan WHERE id_pengguna = '$id_pengguna' ORDER BY tanggal_pesanan DESC";
$hasil_pesanan = mysqli_query($koneksi, $query);

include 'includes/header.php';
?>

<h2 class="section-title">Riwayat Pesanan Anda</h2>
<?php tampilkan_pesan(); ?>

<div class="card">
    <table class="cart-table">
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>Tanggal</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($hasil_pesanan) > 0): ?>
                <?php while($pesanan = mysqli_fetch_assoc($hasil_pesanan)): ?>
                <tr>
                    <td>#<?php echo $pesanan['id_pesanan']; ?></td>
                    <td><?php echo date('d M Y, H:i', strtotime($pesanan['tanggal_pesanan'])); ?></td>
                    <td><?php echo format_rupiah($pesanan['total_harga']); ?></td>
                    <td>
                        <span class="status status-<?php echo strtolower($pesanan['status_pesanan']); ?>">
                            <?php echo ucfirst($pesanan['status_pesanan']); ?>
                        </span>
                    </td>
                    <td><a href="detail_pesanan.php?id=<?php echo $pesanan['id_pesanan']; ?>" class="btn btn-primary">Lihat Detail</a></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding: 2rem;">Anda belum memiliki riwayat pesanan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>