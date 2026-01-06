<?php 
include 'includes/header.php';
include 'includes/fungsi.php'; 
?>


<div class="hero-section">
    <h1>KERIPIK PISANG ANEKA RASA </h1>
    <p>Keripik Pisang Berkualitas Berbagai Rasa Untuk Kepuasan.</p>
</div>

<h2 class="section-title"> JAJANAN KERIPIK PISANG ANEKA RASA</h2>
<?php tampilkan_pesan(); ?>
<div class="product-grid">
    <?php
    // INI ADALAH QUERY YANG BENAR MENGGUNAKAN JOIN
    $query = "SELECT DISTINCT p.* FROM produk p 
              JOIN produk_variasi pv ON p.id_produk = pv.id_produk 
              WHERE pv.stok > 0 AND p.status_produk = 'aktif'
              ORDER BY p.id_produk DESC";

    $result = mysqli_query($koneksi, $query);
    while ($produk = mysqli_fetch_assoc($result)) :
    ?>
        <div class="product-card">
            <a href="produk_detail.php?id=<?php echo $produk['id_produk']; ?>">
                <div class="product-image-container">
                    <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($produk['gambar_produk']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
                </div>
                <div class="product-info">
                    <h3><?php echo htmlspecialchars($produk['nama_produk']); ?></h3>
                    <p class="price"><?php echo format_rupiah($produk['harga']); ?></p>
                </div>
            </a>
            <a href="produk_detail.php?id=<?php echo $produk['id_produk']; ?>" class="btn btn-primary">Lihat Detail</a>
        </div>
    <?php endwhile; ?>
</div>

<?php include 'includes/footer.php'; ?>