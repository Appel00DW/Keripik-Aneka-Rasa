<?php
require_once 'includes/koneksi.php';
require_once 'includes/fungsi.php';

// Pastikan user sudah login dan keranjang tidak kosong
if (!isset($_SESSION['id_pengguna']) || empty($_SESSION['keranjang'])) {
    header("Location: index.php");
    exit();
}

// Ambil data user
$id_pengguna = $_SESSION['id_pengguna'];
$user_query = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE id_pengguna = $id_pengguna");
$user = mysqli_fetch_assoc($user_query);

// --- LOGIKA UNTUK MENGAMBIL ITEM & MENGHITUNG HARGA ---

// 1. Ambil diskon dari session (jika ada)
$diskon = $_SESSION['diskon'] ?? 0;
$kode_voucher_valid = $_SESSION['kode_voucher'] ?? null;

$items_in_cart = [];
$total_harga = 0; // Ini adalah subtotal sebelum diskon
$ids_variasi = array_keys($_SESSION['keranjang']);

if (!empty($ids_variasi)) {
    $query_items = "SELECT pv.id_variasi, pv.warna, p.nama_produk, p.harga, p.gambar_produk 
                    FROM produk_variasi pv 
                    JOIN produk p ON pv.id_produk = p.id_produk 
                    WHERE pv.id_variasi IN (" . implode(',', $ids_variasi) . ")";
    
    $result_items = mysqli_query($koneksi, $query_items);

    while ($item = mysqli_fetch_assoc($result_items)) {
        $jumlah = $_SESSION['keranjang'][$item['id_variasi']];
        $subtotal = $item['harga'] * $jumlah;
        $total_harga += $subtotal;
        
        $item['jumlah'] = $jumlah;
        $item['subtotal'] = $subtotal;
        $items_in_cart[] = $item;
    }
}

// 2. Hitung total akhir setelah diskon
$total_setelah_diskon = $total_harga - $diskon;
// -----------------------------------------------------------


// Proses checkout saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $alamat_pengiriman = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    // 3. Simpan pesanan ke database menggunakan total SETELAH diskon
    $query_pesanan = "INSERT INTO pesanan (id_pengguna, total_harga, alamat_pengiriman, status_pesanan) VALUES (?, ?, ?, 'pending')";
    $stmt = mysqli_prepare($koneksi, $query_pesanan);
    // Ganti $total_harga menjadi $total_setelah_diskon
    mysqli_stmt_bind_param($stmt, 'ids', $id_pengguna, $total_setelah_diskon, $alamat_pengiriman);
    
    if (mysqli_stmt_execute($stmt)) {
        $id_pesanan_baru = mysqli_insert_id($koneksi);
        
        foreach ($items_in_cart as $item) {
            $id_produk_q = mysqli_query($koneksi, "SELECT id_produk FROM produk_variasi WHERE id_variasi = {$item['id_variasi']}");
            $id_produk_data = mysqli_fetch_assoc($id_produk_q);
            $id_produk_db = $id_produk_data['id_produk'];

            $query_detail = "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_saat_pesan) VALUES (?, ?, ?, ?)";
            $stmt_detail = mysqli_prepare($koneksi, $query_detail);
            mysqli_stmt_bind_param($stmt_detail, 'iiid', $id_pesanan_baru, $id_produk_db, $item['jumlah'], $item['harga']);
            mysqli_stmt_execute($stmt_detail);

            $query_stok = "UPDATE produk_variasi SET stok = stok - ? WHERE id_variasi = ?";
            $stmt_stok = mysqli_prepare($koneksi, $query_stok);
            mysqli_stmt_bind_param($stmt_stok, 'ii', $item['jumlah'], $item['id_variasi']);
            mysqli_stmt_execute($stmt_stok);
        }
        
        // 4. Kosongkan session keranjang DAN session diskon setelah pesanan berhasil
        unset($_SESSION['keranjang']);
        unset($_SESSION['diskon']);
        unset($_SESSION['kode_voucher']);

        $_SESSION['pesan'] = "Pesanan Anda berhasil dibuat!";
        header("Location: riwayat_pesanan.php");
        exit();
    } else {
        $_SESSION['pesan'] = "Gagal membuat pesanan.";
    } 
}

include 'includes/header.php';
?>

<h2 class="section-title">Konfirmasi Pesanan</h2>
<?php tampilkan_pesan(); ?>

<div class="checkout-page-layout">
    <div class="checkout-form-container card">
        <h3>Alamat Pengiriman</h3>
        <form action="checkout.php" method="post" class="checkout-form">
            <div class="form-grup">
                <label for="alamat">Alamat Lengkap</label>
                <textarea id="alamat" name="alamat" rows="5" required placeholder="Masukkan alamat lengkap Anda di sini..."><?php echo htmlspecialchars($user['alamat']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Konfirmasi & Buat Pesanan</button>
        </form>
    </div>

    <aside class="order-summary-container card">
        <h3>Ringkasan Pesanan Anda</h3>
        <?php foreach ($items_in_cart as $item): ?>
            <div class="summary-item">
                <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($item['gambar_produk']); ?>" alt="">
                <div class="details">
                    <h4><?php echo htmlspecialchars($item['nama_produk']); ?></h4>
                    <span>Warna: <?php echo htmlspecialchars($item['warna']); ?> | Jumlah: <?php echo $item['jumlah']; ?></span>
                </div>
                <div class="price">
                    <?php echo format_rupiah($item['subtotal']); ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- 5. Tampilkan detail diskon di ringkasan -->
        <div class="summary-total-line">
            <span>Subtotal</span>
            <span class="price"><?php echo format_rupiah($total_harga); ?></span>
        </div>

        <?php if ($diskon > 0): ?>
        <div class="summary-total-line">
            <span>Diskon (<?php echo htmlspecialchars($kode_voucher_valid); ?>)</span>
            <span class="price" style="color: #2ecc71;">- <?php echo format_rupiah($diskon); ?></span>
        </div>
        <?php endif; ?>

        <div class="summary-total-line grand-total">
            <span>Total</span>
            <span class="price"><?php echo format_rupiah($total_setelah_diskon); ?></span>
        </div>
    </aside>
</div>

<!-- ... (kode <style> Anda) ... -->

<?php include 'includes/footer.php'; ?>