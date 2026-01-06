<?php
include 'includes/header.php';
include 'includes/fungsi.php';

// Validasi ID produk dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$id_produk = $_GET['id'];
$id_pengguna = $_SESSION['id_pengguna'] ?? 0; // Ambil ID pengguna jika login, jika tidak, 0

// Ambil data produk utama menggunakan prepared statement
$query_produk = "SELECT * FROM produk WHERE id_produk = ? AND status_produk = 'aktif'";
$stmt_produk = mysqli_prepare($koneksi, $query_produk);
mysqli_stmt_bind_param($stmt_produk, 'i', $id_produk);
mysqli_stmt_execute($stmt_produk);
$result_produk = mysqli_stmt_get_result($stmt_produk);
$produk = $result_produk->fetch_assoc();

if (!$produk) {
    echo "<div class='container'><p>Produk tidak ditemukan atau sudah tidak aktif.</p></div>";
    include 'includes/footer.php';
    exit();
}

// Ambil data variasi yang stoknya ada
$query_variasi = "SELECT * FROM produk_variasi WHERE id_produk = ? AND stok > 0";
$stmt_variasi = mysqli_prepare($koneksi, $query_variasi);
mysqli_stmt_bind_param($stmt_variasi, 'i', $id_produk);
mysqli_stmt_execute($stmt_variasi);
$variasi_list = [];
$result_variasi = mysqli_stmt_get_result($stmt_variasi);
while($row = $result_variasi->fetch_assoc()) {
    $variasi_list[] = $row;
}

// --- LOGIKA ULASAN ---
// 1. Ambil statistik rating
$query_rating = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_ulasan FROM ulasan WHERE id_produk = ?";
$stmt_rating = mysqli_prepare($koneksi, $query_rating);
mysqli_stmt_bind_param($stmt_rating, 'i', $id_produk);
mysqli_stmt_execute($stmt_rating);
$result_rating = mysqli_stmt_get_result($stmt_rating);
$stats_rating = $result_rating->fetch_assoc();
$avg_rating = round($stats_rating['avg_rating'] ?? 0, 1);
$total_ulasan = $stats_rating['total_ulasan'] ?? 0;

// 2. Ambil semua ulasan untuk ditampilkan
$query_ulasan = "SELECT u.*, p.nama_lengkap FROM ulasan u JOIN pengguna p ON u.id_pengguna = p.id_pengguna WHERE u.id_produk = ? ORDER BY u.tanggal_ulasan DESC";
$stmt_ulasan = mysqli_prepare($koneksi, $query_ulasan);
mysqli_stmt_bind_param($stmt_ulasan, 'i', $id_produk);
mysqli_stmt_execute($stmt_ulasan);
$hasil_ulasan = mysqli_stmt_get_result($stmt_ulasan);

// 3. Cek apakah user berhak memberi ulasan
$bisa_ulas = false;
if ($id_pengguna > 0) {
    $q_cek_beli = "SELECT COUNT(*) as total FROM pesanan ps JOIN detail_pesanan dp ON ps.id_pesanan = dp.id_pesanan WHERE ps.id_pengguna = ? AND dp.id_produk = ? AND ps.status_pesanan = 'selesai'";
    $stmt_cek_beli = mysqli_prepare($koneksi, $q_cek_beli);
    mysqli_stmt_bind_param($stmt_cek_beli, 'ii', $id_pengguna, $id_produk);
    mysqli_stmt_execute($stmt_cek_beli);
    $result_cek_beli = mysqli_stmt_get_result($stmt_cek_beli);
    $d_cek_beli = $result_cek_beli->fetch_assoc();
    
    if ($d_cek_beli['total'] > 0) {
        $q_cek_ulasan = "SELECT COUNT(*) as total FROM ulasan WHERE id_pengguna = ? AND id_produk = ?";
        $stmt_cek_ulasan = mysqli_prepare($koneksi, $q_cek_ulasan);
        mysqli_stmt_bind_param($stmt_cek_ulasan, 'ii', $id_pengguna, $id_produk);
        mysqli_stmt_execute($stmt_cek_ulasan);
        $result_cek_ulasan = mysqli_stmt_get_result($stmt_cek_ulasan);
        $d_cek_ulasan = $result_cek_ulasan->fetch_assoc();
        
        if ($d_cek_ulasan['total'] == 0) {
            $bisa_ulas = true;
        }
    }
}
?>

<div class="product-detail-container">
    <div class="product-detail-image">
        <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($produk['gambar_produk']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
    </div>
    <div class="product-detail-info">
        <h1><?php echo htmlspecialchars($produk['nama_produk']); ?></h1>
        
        <div class="rating-summary">
            <span class="stars" style="--rating: <?php echo $avg_rating; ?>;"></span>
            <strong><?php echo $avg_rating; ?></strong>
            <span class="total-reviews">(<?php echo $total_ulasan; ?> ulasan)</span>
        </div>

        <p class="price-detail"><?php echo format_rupiah($produk['harga']); ?></p>
        <div class="description">
            <h4>Deskripsi Produk</h4>
            <p><?php echo nl2br(htmlspecialchars($produk['deskripsi'])); ?></p>
        </div>

        <form action="<?php echo BASE_URL; ?>/keranjang.php?action=add" method="post">
            <input type="hidden" name="id_produk" value="<?php echo $produk['id_produk']; ?>">
            <div class="form-grup-detail">
                <label for="pilih-warna">Pilih Varian:</label>
                <select name="id_variasi" id="pilih-warna" required>
                    <option value="">-- Pilih Varian --</option>
                    <?php foreach ($variasi_list as $variasi): ?>
                        <option value="<?php echo $variasi['id_variasi']; ?>" data-stok="<?php echo $variasi['stok']; ?>"><?php echo htmlspecialchars($variasi['warna']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-grup-detail">
                <label for="jumlah">Jumlah:</label>
                <input type="number" id="jumlah" name="jumlah" value="1" min="1" required>
            </div>
            <div class="stok-info">Stok Tersedia: <span id="stok-tersedia">-</span></div>
            <button type="submit" class="btn btn-primary btn-lg">Tambah ke Keranjang</button>
        </form>
    </div>
</div>

<div class="reviews-section card">
    <h3>Rating & Ulasan Pelanggan</h3>
    <?php tampilkan_pesan(); ?>

    <?php if ($bisa_ulas): ?>
    <div class="review-form-container">
        <h4>Tulis Ulasan Anda</h4>
        <p>Anda pernah membeli produk ini. Bagikan pendapat Anda!</p>
        <form action="proses_ulasan.php" method="POST">
            <input type="hidden" name="id_produk" value="<?php echo $id_produk; ?>">
            <div class="form-grup">
                <label>Rating Anda</label>
                <div class="rating-input">
                    <input type="radio" id="star5" name="rating" value="5" required/><label for="star5" title="Luar biasa">★</label>
                    <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Bagus">★</label>
                    <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Cukup">★</label>
                    <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Kurang">★</label>
                    <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="Buruk">★</label>
                </div>
            </div>
            <div class="form-grup">
                <label for="ulasan">Ulasan Anda (Opsional)</label>
                <textarea id="ulasan" name="ulasan" rows="4" placeholder="Bagaimana pendapat Anda tentang produk ini?"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
        </form>
    </div>
    <hr>
    <?php endif; ?>

    <div class="review-list">
        <?php if($total_ulasan > 0): ?>
            <?php while($ulasan = mysqli_fetch_assoc($hasil_ulasan)): ?>
            <div class="review-item">
                <div class="review-header">
                    <strong><?php echo htmlspecialchars($ulasan['nama_lengkap']); ?></strong>
                    <span class="review-date"><?php echo date('d M Y', strtotime($ulasan['tanggal_ulasan'])); ?></span>
                </div>
                <div class="stars" style="--rating: <?php echo $ulasan['rating']; ?>;"></div>
                <p class="review-body"><?php echo nl2br(htmlspecialchars($ulasan['ulasan'])); ?></p>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Belum ada ulasan untuk produk ini. Jadilah yang pertama!</p>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('pilih-warna').addEventListener('change', function() {
    var stokDisplay = document.getElementById('stok-tersedia');
    var jumlahInput = document.getElementById('jumlah');
    var selectedOption = this.options[this.selectedIndex];
    var stok = selectedOption.getAttribute('data-stok');
    if (stok) {
        stokDisplay.textContent = stok;
        jumlahInput.max = stok;
        if (parseInt(jumlahInput.value) > parseInt(stok)) {
            jumlahInput.value = stok;
        }
    } else {
        stokDisplay.textContent = '-';
        jumlahInput.max = 1;
    }
});
</script>

<style>
/* Style ini bisa Anda pindahkan ke file style.css agar lebih rapi */
.product-detail-container { display: grid; grid-template-columns: 1fr 1.5fr; gap: 3rem; margin-top: 2rem; align-items: start; }
@media (max-width: 768px) { .product-detail-container { grid-template-columns: 1fr; } }
.product-detail-image img { width: 100%; border-radius: 15px; box-shadow: var(--shadow-soft); }
.price-detail { font-family: var(--font-heading); font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin: 0.5rem 0; }
.description { margin-top: 1.5rem; color: var(--text-light); line-height: 1.6; }
.form-grup-detail { margin: 1.5rem 0; }
.form-grup-detail label { display: block; margin-bottom: 0.5rem; font-weight: 700; color: var(--text-light); }
.form-grup-detail select, .form-grup-detail input { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 1rem; }
.stok-info { margin-bottom: 1.5rem; font-weight: 500; color: #777; }
.btn-lg { padding: 15px 30px; font-size: 1.1rem; }

/* === STYLE ULASAN YANG DIPERBARUI === */

/* Rating Bintang di Ringkasan Atas */
.rating-summary { display: flex; align-items: center; gap: 0.5rem; margin: -0.5rem 0 1rem 0; font-size: 0.9rem; }
.total-reviews { color: var(--text-light); }

/* Bagian Ulasan Utama */
.reviews-section { margin-top: 3rem; animation: fadeIn 1s ease-out; }

/* --- Kotak Form Ulasan yang Diperbarui --- */
.review-form-container {
    background-color: var(--bg-white);
    padding: 2rem;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    margin-bottom: 2rem;
}
.review-form-container h4 {
    font-family: var(--font-heading);
    color: var(--primary-color);
    margin-top: 0;
    font-size: 1.5rem;
}
.review-form-container p {
    margin-top: -1rem;
    margin-bottom: 1.5rem;
    color: var(--text-light);
}

/* --- Input Rating Bintang yang Diperbarui --- */
.rating-input {
    display: flex;
    flex-direction: row-reverse; /* Bintang dibalik agar CSS :hover ~ berfungsi benar */
    justify-content: flex-end;
    border: none;
}
.rating-input > input { display: none; } /* Sembunyikan radio button asli */
.rating-input > label {
    color: #e0e0e0; /* Warna bintang mati */
    font-size: 2.5rem; /* Ukuran bintang lebih besar */
    cursor: pointer;
    transition: color 0.2s ease-in-out;
}
/* Saat hover, warnai bintang dan semua bintang di sebelah kanannya */
.rating-input:not(:checked) > label:hover,
.rating-input:not(:checked) > label:hover ~ label {
    color: #ffc107;
}
/* Warnai bintang yang dipilih dan semua di sebelah kanannya */
.rating-input > input:checked ~ label {
    color: #ffc107;
}
/* Karakter bintang menggunakan unicode */
.rating-input > label::before {
    content: '★';
}

/* --- Textarea Ulasan yang Diperbarui --- */
#ulasan {
    width: 100%;
    padding: 14px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-family: var(--font-body);
    font-size: 1rem;
    transition: all 0.3s ease;
    min-height: 100px;
    background-color: var(--bg-light);
}
#ulasan:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(0, 95, 115, 0.1);
    background-color: #fff;
}


/* --- Daftar Ulasan yang Sudah Ada --- */
hr { border: 0; border-top: 1px solid var(--border-color); margin: 2rem 0; }
.review-list {}
.review-item { padding: 1.5rem 0; border-bottom: 1px solid var(--border-color); }
.review-item:last-child { border-bottom: none; }
.review-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
.review-date { font-size: 0.85rem; color: var(--text-light); }
.review-body { margin: 0.5rem 0 0 0; line-height: 1.6; }
.stars { --percent: calc(var(--rating) / 5 * 100%); display: inline-block; font-size: 20px; font-family: Times; line-height: 1; }
.stars::before { content: '★★★★★'; letter-spacing: 3px; background: linear-gradient(90deg, #ffc107 var(--percent), #e0e0e0 var(--percent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

</style>
<?php include 'includes/footer.php'; ?>