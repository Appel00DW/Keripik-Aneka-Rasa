<?php
require_once 'includes/koneksi.php';

if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pengguna = $_SESSION['id_pengguna'];
    $id_produk = (int)$_POST['id_produk'];
    $rating = (int)$_POST['rating'];
    $ulasan = htmlspecialchars($_POST['ulasan']);

    if ($id_produk > 0 && $rating >= 1 && $rating <= 5) {
        $cek_ulasan_q = mysqli_prepare($koneksi, "SELECT id_ulasan FROM ulasan WHERE id_produk = ? AND id_pengguna = ?");
        mysqli_stmt_bind_param($cek_ulasan_q, 'ii', $id_produk, $id_pengguna);
        mysqli_stmt_execute($cek_ulasan_q);
        if (mysqli_stmt_get_result($cek_ulasan_q)->num_rows == 0) {
            $query = "INSERT INTO ulasan (id_produk, id_pengguna, rating, ulasan) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($koneksi, $query);
            mysqli_stmt_bind_param($stmt, 'iiis', $id_produk, $id_pengguna, $rating, $ulasan);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['pesan'] = "Terima kasih atas ulasan Anda!";
            }
        } else {
            $_SESSION['pesan'] = "Anda sudah pernah memberikan ulasan untuk produk ini.";
        }
    }
    header("Location: produk_detail.php?id=" . $id_produk);
    exit();
}
?>