<?php
require 'includes/koneksi.php';
$id_produk = $_GET['id'];

// Jika keranjang belum ada, buat dulu
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Jika produk sudah ada di keranjang, tambah jumlahnya. Jika belum, tambahkan.
if (isset($_SESSION['keranjang'][$id_produk])) {
    $_SESSION['keranjang'][$id_produk] += 1;
} else {
    $_SESSION['keranjang'][$id_produk] = 1;
}

header('Location: keranjang.php');
exit();
?>