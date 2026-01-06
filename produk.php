<?php
// ... (seluruh logika PHP Anda dari atas tetap sama) ...
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login_admin.php");
    exit();
}

// Proses Tambah & Edit Produk beserta Variasinya
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan_produk'])) {
    // Simpan data produk utama
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga = $_POST['harga'];
    $id_produk = isset($_POST['id_produk']) ? $_POST['id_produk'] : null;
    $gambar_lama = isset($_POST['gambar_lama']) ? $_POST['gambar_lama'] : null;

    $nama_gambar = $gambar_lama;
    if (isset($_FILES['gambar_produk']) && $_FILES['gambar_produk']['error'] == 0) {
        $nama_gambar = date('YmdHis') . '_' . basename($_FILES['gambar_produk']['name']);
        $target_file = "../assets/images/" . $nama_gambar;
        move_uploaded_file($_FILES['gambar_produk']['tmp_name'], $target_file);
    }

    if ($id_produk) { // Proses Edit
        $query = "UPDATE produk SET nama_produk='$nama', deskripsi='$deskripsi', harga='$harga', gambar_produk='$nama_gambar' WHERE id_produk=$id_produk";
        mysqli_query($koneksi, $query);
        $_SESSION['pesan'] = "Produk berhasil diperbarui.";
    } else { // Proses Tambah
        $query = "INSERT INTO produk (nama_produk, deskripsi, harga, gambar_produk) VALUES ('$nama', '$deskripsi', '$harga', '$nama_gambar')";
        mysqli_query($koneksi, $query);
        $id_produk = mysqli_insert_id($koneksi); // Ambil ID produk baru
        $_SESSION['pesan'] = "Produk berhasil ditambahkan.";
    }

    // Hapus variasi lama untuk produk ini sebelum memasukkan yang baru
    mysqli_query($koneksi, "DELETE FROM produk_variasi WHERE id_produk = $id_produk");

    // Simpan data variasi
    if (isset($_POST['variasi_warna'])) {
        foreach ($_POST['variasi_warna'] as $key => $warna) {
            if (!empty($warna)) {
                $stok = (int)$_POST['variasi_stok'][$key];
                mysqli_query($koneksi, "INSERT INTO produk_variasi (id_produk, warna, stok) VALUES ($id_produk, '$warna', $stok)");
            }
        }
    }

    header("Location: produk.php");
    exit();
}
// ... (logika Hapus produk tetap sama) ...
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_query($koneksi, "DELETE FROM produk WHERE id_produk=$id");
    $_SESSION['pesan'] = "Produk berhasil dihapus.";
    header("Location: produk.php");
    exit();
}

$produk_edit = null;
$variasi_edit = [];
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id_edit = $_GET['id'];
    $result_produk = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk=$id_edit");
    $produk_edit = mysqli_fetch_assoc($result_produk);
    
    $result_variasi = mysqli_query($koneksi, "SELECT * FROM produk_variasi WHERE id_produk=$id_edit");
    while($row = mysqli_fetch_assoc($result_variasi)) {
        $variasi_edit[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Produk</title>
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
        <header class="content-header"><h1>Kelola Produk</h1></header>
        <div class="content-body">
            <?php tampilkan_pesan(); ?>
            <div class="card">
                <h3><?php echo $produk_edit ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h3>
                <form action="produk.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="simpan_produk" value="1">
                    <?php if ($produk_edit): ?>
                        <input type="hidden" name="id_produk" value="<?php echo $produk_edit['id_produk']; ?>">
                        <input type="hidden" name="gambar_lama" value="<?php echo $produk_edit['gambar_produk']; ?>">
                    <?php endif; ?>
                    
                    <h4>Informasi Utama</h4>
                    <div class="form-grup"><label>Nama Produk</label><input type="text" name="nama_produk" value="<?php echo htmlspecialchars($produk_edit['nama_produk'] ?? ''); ?>" required></div>
                    <div class="form-grup"><label>Deskripsi</label><textarea name="deskripsi" rows="4"><?php echo htmlspecialchars($produk_edit['deskripsi'] ?? ''); ?></textarea></div>
                    <div class="form-grup"><label>Harga</label><input type="number" name="harga" value="<?php echo $produk_edit['harga'] ?? ''; ?>" required></div>
                    <div class="form-grup"><label>Gambar Produk</label><input type="file" name="gambar_produk"></div>

                    <hr style="margin: 2rem 0;">
                    <h4>Variasi Warna & Stok</h4>
                    <div id="variasi-container">
                        <?php if (!empty($variasi_edit)): ?>
                            <?php foreach ($variasi_edit as $variasi): ?>
                                <div class="variasi-item"><input type="text" name="variasi_warna[]" placeholder="Warna (e.g. Merah)" value="<?php echo htmlspecialchars($variasi['warna']); ?>"><input type="number" name="variasi_stok[]" placeholder="Stok" value="<?php echo $variasi['stok']; ?>"></div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="variasi-item"><input type="text" name="variasi_warna[]" placeholder="Warna (e.g. Merah)"><input type="number" name="variasi_stok[]" placeholder="Stok"></div>
                        <?php endif; ?>
                    </div>
                    <button type="button" id="tambah-variasi" class="btn btn-secondary" style="margin-top:1rem;">+ Tambah Variasi</button>
                    
                    <hr style="margin: 2rem 0;">
                    <div class="btn-container">
                        <button type="submit" class="btn btn-primary"><?php echo $produk_edit ? 'Update Produk' : 'Tambah Produk'; ?></button>
                        <?php if ($produk_edit): ?>
                            <a href="produk.php" class="btn btn-secondary">Batal Edit</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="card">
                <h3>Daftar Produk</h3>
                <table class="content-table">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Total Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_produk_list = "SELECT p.*, SUM(pv.stok) AS total_stok
                                              FROM produk p
                                              LEFT JOIN produk_variasi pv ON p.id_produk = pv.id_produk
                                              GROUP BY p.id_produk
                                              ORDER BY p.id_produk DESC";
                        $result_produk_list = mysqli_query($koneksi, $query_produk_list);
                        while ($produk = mysqli_fetch_assoc($result_produk_list)):
                        ?>
                        <tr>
                            <td><img src="<?php echo BASE_URL; ?>/assets/images/<?php echo htmlspecialchars($produk['gambar_produk']); ?>" class="product-image"></td>
                            <td><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                            <td><?php echo format_rupiah($produk['harga']); ?></td>
                            <td><?php echo $produk['total_stok'] ?? '0'; ?></td>
                            <td>
                                <div class="btn-container">
                                    <a href="produk.php?action=edit&id=<?php echo $produk['id_produk']; ?>" class="btn btn-secondary">Edit</a>
                                    <a href="produk.php?action=delete&id=<?php echo $produk['id_produk']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus produk ini beserta semua variasinya?')">Hapus</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
        </div>
    </main>
</div>

<script>
document.getElementById('tambah-variasi').addEventListener('click', function() {
    var container = document.getElementById('variasi-container');
    var newItem = document.createElement('div');
    newItem.className = 'variasi-item';
    newItem.innerHTML = '<input type="text" name="variasi_warna[]" placeholder="Warna"><input type="number" name="variasi_stok[]" placeholder="Stok">';
    container.appendChild(newItem);
});
</script>
<style>.variasi-item { display: flex; gap: 1rem; margin-bottom: 1rem; }</style>
</body>
</html>