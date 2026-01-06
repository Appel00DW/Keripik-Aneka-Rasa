<?php
include 'includes/header.php';
include 'includes/fungsi.php';

// Keamanan: Pastikan user sudah login
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}

// Keamanan: Pastikan ID pesanan ada dan valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: riwayat_pesanan.php");
    exit();
}

$id_pesanan = $_GET['id'];
$id_pengguna = $_SESSION['id_pengguna'];

// Ambil data pesanan utama
// Keamanan: Pastikan pesanan ini milik user yang sedang login
$query_pesanan = "SELECT * FROM pesanan WHERE id_pesanan = ? AND id_pengguna = ?";
$stmt = mysqli_prepare($koneksi, $query_pesanan);
mysqli_stmt_bind_param($stmt, 'ii', $id_pesanan, $id_pengguna);
mysqli_stmt_execute($stmt);
$result_pesanan = mysqli_stmt_get_result($stmt);
$pesanan = mysqli_fetch_assoc($result_pesanan);

// Jika pesanan tidak ditemukan atau bukan milik user, tendang kembali
if (!$pesanan) {
    header("Location: riwayat_pesanan.php");
    exit();
}

// Ambil item-item yang ada di dalam pesanan ini
$query_items = "SELECT dp.*, p.nama_produk, p.gambar_produk 
                FROM detail_pesanan dp
                JOIN produk p ON dp.id_produk = p.id_produk
                WHERE dp.id_pesanan = ?";
$stmt_items = mysqli_prepare($koneksi, $query_items);
mysqli_stmt_bind_param($stmt_items, 'i', $id_pesanan);
mysqli_stmt_execute($stmt_items);
$result_items = mysqli_stmt_get_result($stmt_items);
?>

<h2 class="section-title">Detail Pesanan #<?php echo $pesanan['id_pesanan']; ?></h2>

<div class="order-detail-layout">
    <div class="order-items-list">
        <div class="card">
            <h3>Produk yang Dipesan</h3>
            <?php while($item = mysqli_fetch_assoc($result_items)): ?>
            <div class="summary-item">
                <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($item['gambar_produk']); ?>" alt="">
                <div class="details">
                    <h4><?php echo htmlspecialchars($item['nama_produk']); ?></h4>
                    <span>Jumlah: <?php echo $item['jumlah']; ?></span>
                </div>
                <div class="price">
                    <?php echo format_rupiah($item['harga_saat_pesan'] * $item['jumlah']); ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <aside class="order-summary">
        <div class="card">
            <h3>Ringkasan Pesanan</h3>
            <div class="summary-line">
                <span>Tanggal Pesanan:</span>
                <span><?php echo date('d M Y', strtotime($pesanan['tanggal_pesanan'])); ?></span>
            </div>
            <div class="summary-line">
                <span>Status:</span>
                <span>
                    <span class="status status-<?php echo strtolower($pesanan['status_pesanan']); ?>">
                        <?php echo ucfirst($pesanan['status_pesanan']); ?>
                    </span>
                </span>
            </div>
            <div class="summary-line grand-total">
                <span>Total Pembayaran:</span>
                <span class="price"><?php echo format_rupiah($pesanan['total_harga']); ?></span>
            </div>
        </div>
        <div class="card">
            <h3>Alamat Pengiriman</h3>
            <p><?php echo nl2br(htmlspecialchars($pesanan['alamat_pengiriman'])); ?></p>
        </div>
    </aside> <div class="back-button-wrapper">
        <a href="riwayat_pesanan.php" class="btn-cool-back">&larr; Kembali ke Riwayat</a>
    </div>

</div> <style>
.order-detail-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 2.5rem; align-items: flex-start; grid-template-areas: "list summary" "button button"; }
.order-items-list { grid-area: list; }
.order-summary { grid-area: summary; }
.back-button-wrapper { grid-area: button; }
.card { background-color: #fff; border-radius: 15px; padding: 2rem; box-shadow: var(--card-shadow); margin-bottom: 1.5rem; }
.card h3 { margin-top: 0; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color); }
.summary-item { display: flex; gap: 1rem; align-items: center; padding: 1.5rem 0; border-bottom: 1px solid #f0f0f0; }
.summary-item:last-child { border-bottom: none; }
.summary-item img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
.summary-item .details { flex-grow: 1; }
.summary-item .details h4 { margin: 0 0 4px 0; font-size: 0.9rem; font-weight: 600; }
.summary-item .details span { font-size: 0.8rem; color: var(--text-light); }
.summary-item .price { font-weight: 600; }
.summary-line { display: flex; justify-content: space-between; margin-bottom: 1rem; }
.summary-line.grand-total { font-weight: 700; font-size: 1.2rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color); }
.summary-line .price { color: var(--primary-color); }
.btn.btn-secondary { background-color: #6c757d; color: white; }
.btn.btn-secondary:hover { background-color: #5a6268; }

/* -- Style untuk posisi tombol kembali -- */
.back-button-wrapper {
    text-align: right; /* Membuat tombol rata ke kanan */
    margin-top: 1rem;
}

/* -- Style untuk Tombol Keren -- */
.btn-cool-back {
    display: inline-block;
    text-decoration: none;
    background: linear-gradient(45deg, #6c757d, #343a40); /* Gradien abu-abu gelap */
    color: white;
    padding: 12px 24px;
    border-radius: 50px; /* Membuatnya berbentuk pil */
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.btn-cool-back:hover {
    transform: translateY(-3px); /* Efek terangkat saat disentuh */
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

@media (max-width: 992px) { 
    .order-detail-layout { 
        grid-template-columns: 1fr;
        grid-template-areas: "summary" "list" "button";
    } 
}
</style>

<?php include 'includes/footer.php'; ?>