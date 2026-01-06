<?php
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login_admin.php");
    exit();
}

// Logika untuk Tambah/Edit Voucher
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode = strtoupper($_POST['kode_voucher']);
    $jenis = $_POST['jenis'];
    $nilai = $_POST['nilai'];
    $minimal = $_POST['minimal_belanja'] ?? 0;
    $status = $_POST['status'];
    $id_voucher = $_POST['id_voucher'] ?? null;

    if ($id_voucher) { // Edit
        $query = "UPDATE voucher SET kode_voucher=?, jenis=?, nilai=?, minimal_belanja=?, status=? WHERE id_voucher=?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'ssdsdi', $kode, $jenis, $nilai, $minimal, $status, $id_voucher);
    } else { // Tambah
        $query = "INSERT INTO voucher (kode_voucher, jenis, nilai, minimal_belanja, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'ssdsd', $kode, $jenis, $nilai, $minimal, $status);
    }
    mysqli_stmt_execute($stmt);
    $_SESSION['pesan'] = "Data voucher berhasil disimpan.";
    header("Location: voucher.php");
    exit();
}

// Logika Hapus
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_query($koneksi, "DELETE FROM voucher WHERE id_voucher=$id");
    $_SESSION['pesan'] = "Voucher berhasil dihapus.";
    header("Location: voucher.php");
    exit();
}

// Logika untuk Toggle Status (Aktif/Nonaktif)
if (isset($_GET['action']) && $_GET['action'] == 'toggle' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status_sekarang = $_GET['status'];
    $status_baru = ($status_sekarang == 'aktif') ? 'nonaktif' : 'aktif';
    
    $query = "UPDATE voucher SET status = ? WHERE id_voucher = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'si', $status_baru, $id);
    mysqli_stmt_execute($stmt);
    
    $_SESSION['pesan'] = "Status voucher berhasil diubah.";
    header("Location: voucher.php");
    exit();
}


$voucher_edit = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $result = mysqli_query($koneksi, "SELECT * FROM voucher WHERE id_voucher=".$_GET['id']);
    $voucher_edit = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Voucher</title>
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
                <li class="active"><a href="voucher.php">Kelola Voucher</a></li>
                <li><a href="pesanan.php">Kelola Pesanan</a></li>
                   <li ><a href="ulasan.php">Kelola Ulasan</a></li>
                <li><a href="tambah_admin.php">Tambah Admin</a></li>
                <li><a href="../logout.php?from=admin">Logout</a></li>
            </ul>
        </nav>
    </aside>
    <main class="main-content">
        <header class="content-header"><h1>Kelola Voucher & Diskon</h1></header>
        <div class="content-body">
            <?php tampilkan_pesan(); ?>
            <div class="card">
                <h3><?php echo $voucher_edit ? 'Edit' : 'Tambah'; ?> Voucher</h3>
                <form action="voucher.php" method="POST">
                    <?php if($voucher_edit) echo "<input type='hidden' name='id_voucher' value='{$voucher_edit['id_voucher']}'>"; ?>
                    <div class="form-grup"><label>Kode Voucher</label><input type="text" name="kode_voucher" value="<?php echo $voucher_edit['kode_voucher'] ?? ''; ?>" required></div>
                    <div class="form-grup"><label>Jenis</label><select name="jenis" required><option value="persen" <?php if(isset($voucher_edit) && $voucher_edit['jenis']=='persen') echo 'selected'; ?>>Persen (%)</option><option value="nominal" <?php if(isset($voucher_edit) && $voucher_edit['jenis']=='nominal') echo 'selected'; ?>>Nominal (Rp)</option></select></div>
                    <div class="form-grup"><label>Nilai</label><input type="number" name="nilai" step="0.01" value="<?php echo $voucher_edit['nilai'] ?? ''; ?>" required></div>
                    <div class="form-grup"><label>Minimal Belanja (Rp)</label><input type="number" name="minimal_belanja" step="0.01" value="<?php echo $voucher_edit['minimal_belanja'] ?? '0'; ?>"></div>
                    <div class="form-grup"><label>Status</label><select name="status" required><option value="aktif" <?php if(isset($voucher_edit) && $voucher_edit['status']=='aktif') echo 'selected'; ?>>Aktif</option><option value="nonaktif" <?php if(isset($voucher_edit) && $voucher_edit['status']=='nonaktif') echo 'selected'; ?>>Nonaktif</option></select></div>
                    <button type="submit" class="btn btn-primary"><?php echo $voucher_edit ? 'Update' : 'Tambah'; ?></button>
                </form>
            </div>
            <div class="card">
                <h3>Daftar Voucher</h3>
                <table class="content-table">
                    <thead><tr><th>Kode</th><th>Jenis</th><th>Nilai</th><th>Min. Belanja</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php
                        $vouchers = mysqli_query($koneksi, "SELECT * FROM voucher ORDER BY id_voucher DESC");
                        while($v = mysqli_fetch_assoc($vouchers)):
                        ?>
                        <tr>
                            <td><strong><?php echo $v['kode_voucher']; ?></strong></td>
                            <td><?php echo ucfirst($v['jenis']); ?></td>
                            <td><?php echo ($v['jenis'] == 'persen') ? $v['nilai'].'%' : format_rupiah($v['nilai']); ?></td>
                            <td><?php echo format_rupiah($v['minimal_belanja']); ?></td>
                            <td><span class="status status-<?php echo $v['status']=='aktif' ? 'selesai' : 'pending'; ?>"><?php echo ucfirst($v['status']); ?></span></td>
                            <td>
                                <div class="btn-container">
                                    <a href="voucher.php?action=edit&id=<?php echo $v['id_voucher']; ?>" class="btn btn-secondary">Edit</a>
                                    
                                    <?php if ($v['status'] == 'aktif'): ?>
                                        <a href="voucher.php?action=toggle&id=<?php echo $v['id_voucher']; ?>&status=aktif" class="btn btn-warning">Nonaktifkan</a>
                                    <?php else: ?>
                                        <a href="voucher.php?action=toggle&id=<?php echo $v['id_voucher']; ?>&status=nonaktif" class="btn btn-success">Aktifkan</a>
                                    <?php endif; ?>
                                    
                                    <a href="voucher.php?action=delete&id=<?php echo $v['id_voucher']; ?>" class="btn btn-danger" onclick="return confirm('Yakin?')">Hapus</a>
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
<style>
.btn-success { background-color: #2ecc71; color: white; }
.btn-success:hover { background-color: #27ae60; }
.btn-warning { background-color: #f39c12; color: white; }
.btn-warning:hover { background-color: #e67e22; }
</style>
</body>
</html>