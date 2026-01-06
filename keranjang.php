<?php
require_once 'includes/koneksi.php';
require_once 'includes/fungsi.php';

// Inisialisasi keranjang
if (!isset($_SESSION['keranjang'])) { $_SESSION['keranjang'] = []; }


// ==========================================================
// BAGIAN 1: LOGIKA TAMBAH/HAPUS PRODUK
// ==========================================================
if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];

    // Logika untuk menambah berdasarkan variasi
    if ($action == 'add' && isset($_POST['id_variasi']) && isset($_POST['jumlah'])) {
        $id_variasi = (int)$_POST['id_variasi'];
        $jumlah_dipesan = (int)$_POST['jumlah'];
        
        if ($id_variasi > 0 && $jumlah_dipesan > 0) {
            $cek_stok_q = mysqli_query($koneksi, "SELECT stok FROM produk_variasi WHERE id_variasi = $id_variasi");
            $data_stok = mysqli_fetch_assoc($cek_stok_q);
            $stok_tersedia = $data_stok ? $data_stok['stok'] : 0;
            
            $jumlah_di_keranjang = isset($_SESSION['keranjang'][$id_variasi]) ? $_SESSION['keranjang'][$id_variasi] : 0;
            
            if (($jumlah_di_keranjang + $jumlah_dipesan) <= $stok_tersedia) {
                $_SESSION['keranjang'][$id_variasi] = $jumlah_di_keranjang + $jumlah_dipesan;
                $_SESSION['pesan'] = "Produk ditambahkan ke keranjang.";
            } else {
                $_SESSION['pesan'] = "Gagal, jumlah pesanan melebihi stok yang tersedia.";
            }
        }
        header('Location: keranjang.php');
        exit();
    }

    // Logika untuk menghapus berdasarkan variasi
    if ($action == 'remove' && isset($_GET['id'])) {
        $id_variasi = (int)$_GET['id'];
        if (isset($_SESSION['keranjang'][$id_variasi])) {
            unset($_SESSION['keranjang'][$id_variasi]);
            $_SESSION['pesan'] = "Produk dihapus dari keranjang.";
        }
        header('Location: keranjang.php');
        exit();
    }
}


// ==========================================================
// BAGIAN 2: KALKULASI HARGA & LOGIKA VOUCHER
// ==========================================================
$total_harga = 0; // Ini adalah subtotal SEBELUM diskon
if (!empty($_SESSION['keranjang'])) {
    $ids_variasi_for_total = array_keys($_SESSION['keranjang']);
    $query_total = "SELECT pv.id_variasi, p.harga 
                    FROM produk_variasi pv JOIN produk p ON pv.id_produk = p.id_produk 
                    WHERE pv.id_variasi IN (" . implode(',', $ids_variasi_for_total) . ")";
    $result_total = mysqli_query($koneksi, $query_total);
    if ($result_total) {
        while($item_total = mysqli_fetch_assoc($result_total)) {
            $jumlah = $_SESSION['keranjang'][$item_total['id_variasi']];
            $total_harga += $item_total['harga'] * $jumlah;
        }
    }
}

$diskon = 0;
$kode_voucher_valid = null;
if(isset($_POST['terapkan_voucher'])) {
    $kode = strtoupper($_POST['kode_voucher']);
    
    $cek_voucher = mysqli_query($koneksi, "SELECT * FROM voucher WHERE kode_voucher = '$kode' AND status = 'aktif' AND (berlaku_hingga IS NULL OR berlaku_hingga >= CURDATE())");
    if($voucher = mysqli_fetch_assoc($cek_voucher)) {
        if($total_harga >= $voucher['minimal_belanja']) {
            if($voucher['jenis'] == 'persen') { $diskon = ($voucher['nilai'] / 100) * $total_harga; } else { $diskon = $voucher['nilai']; }
            $_SESSION['diskon'] = $diskon; $_SESSION['kode_voucher'] = $kode; $_SESSION['pesan'] = "Voucher '$kode' berhasil diterapkan!";
        } else { $_SESSION['pesan'] = "Belanja minimal ".format_rupiah($voucher['minimal_belanja'])." untuk voucher ini."; }
    } else { $_SESSION['pesan'] = "Kode voucher tidak valid atau sudah kedaluwarsa."; }
    header('Location: keranjang.php'); exit();
}

if (isset($_SESSION['diskon'])) { $diskon = $_SESSION['diskon']; $kode_voucher_valid = $_SESSION['kode_voucher']; }

$query_vouchers = "SELECT * FROM voucher WHERE status = 'aktif' AND (berlaku_hingga IS NULL OR berlaku_hingga >= CURDATE())";
$hasil_vouchers = mysqli_query($koneksi, $query_vouchers);

include 'includes/header.php';
?>

<h2 class="section-title">Keranjang Belanja Anda</h2>
<?php tampilkan_pesan(); ?>

<?php if (empty($_SESSION['keranjang'])) : ?>
    <div class="cart-empty">
        <h2>Keranjang Anda masih kosong</h2>
        <p>Mari jelajahi produk kami dan temukan yang Anda suka!</p>
        <a href="index.php" class="btn btn-primary">Kembali Belanja</a>
    </div>
<?php else : ?>
    <div class="cart-page-wrapper">
        <div class="cart-items-list">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $ids_variasi_display = array_keys($_SESSION['keranjang']);
                    $query_display = "SELECT pv.id_variasi, pv.warna, p.nama_produk, p.harga, p.gambar_produk 
                                      FROM produk_variasi pv 
                                      JOIN produk p ON pv.id_produk = p.id_produk 
                                      WHERE pv.id_variasi IN (" . implode(',', $ids_variasi_display) . ")";
                    $result_display = mysqli_query($koneksi, $query_display);
                    while ($item = mysqli_fetch_assoc($result_display)):
                    ?>
                        <tr>
                            <td>
                                <div class="cart-product-info">
                                    <img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($item['gambar_produk']); ?>" alt="<?php echo htmlspecialchars($item['nama_produk']); ?>">
                                    <div class="details">
                                        <h4><?php echo htmlspecialchars($item['nama_produk']); ?></h4>
                                        <span>Warna: <?php echo htmlspecialchars($item['warna']); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo format_rupiah($item['harga']); ?></td>
                            <td><?php echo $_SESSION['keranjang'][$item['id_variasi']]; ?></td>
                            <td><?php echo format_rupiah($item['harga'] * $_SESSION['keranjang'][$item['id_variasi']]); ?></td>
                            <td>
                                <a href="keranjang.php?action=remove&id=<?php echo $item['id_variasi']; ?>" class="btn-danger">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <aside class="cart-summary">
            <h3>Ringkasan Belanja</h3>
            <?php if(mysqli_num_rows($hasil_vouchers) > 0): ?>
            <div class="available-vouchers">
                <h4>Voucher Tersedia</h4>
                <?php while($voucher = mysqli_fetch_assoc($hasil_vouchers)): ?>
                    <div class="voucher-card">
                        <div class="voucher-details">
                            <div class="code"><?php echo $voucher['kode_voucher']; ?></div>
                            <div class="desc">
                                <?php 
                                if($voucher['jenis'] == 'persen') echo "Diskon {$voucher['nilai']}%"; else echo "Potongan ".format_rupiah($voucher['nilai']);
                                if($voucher['minimal_belanja'] > 0) echo " | Min. Blj ".format_rupiah($voucher['minimal_belanja']);
                                ?>
                            </div>
                        </div>
                        <button class="btn-copy" data-kode="<?php echo $voucher['kode_voucher']; ?>">Salin</button>
                    </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
            
            <form action="keranjang.php" method="POST">
                <div class="form-grup">
                    <label>Punya Kode Voucher?</label>
                    <input type="text" name="kode_voucher" id="kode-voucher-input" placeholder="Masukkan kode di sini">
                </div>
                <button type="submit" name="terapkan_voucher" class="btn btn-secondary" style="width:100%">Terapkan</button>
            </form>
            <hr>
            
            <div class="summary-total-line"><span>Subtotal</span><span><?php echo format_rupiah($total_harga); ?></span></div>
            <?php if($diskon > 0): ?>
            <div class="summary-total-line"><span>Diskon (<?php echo htmlspecialchars($kode_voucher_valid); ?>)</span><span style="color: #2ecc71;">- <?php echo format_rupiah($diskon); ?></span></div>
            <?php endif; ?>
            <div class="summary-total-line grand-total"><span>Total</span><span class="price"><?php echo format_rupiah($total_harga - $diskon); ?></span></div>
            <a href="checkout.php" class="btn btn-primary">Lanjutkan ke Checkout</a>
        </aside>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const copyButtons = document.querySelectorAll('.btn-copy');
    const voucherInput = document.getElementById('kode-voucher-input');
    if(copyButtons.length > 0 && voucherInput) {
        copyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const code = this.getAttribute('data-kode');
                navigator.clipboard.writeText(code).then(() => {
                    voucherInput.value = code;
                    const originalText = this.textContent;
                    this.textContent = 'Tersalin!';
                    setTimeout(() => { this.textContent = originalText; }, 2000);
                }).catch(err => { console.error('Gagal menyalin kode: ', err); });
            });
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>