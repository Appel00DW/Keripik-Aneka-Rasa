<?php
// Fungsi untuk memformat angka menjadi format Rupiah
function format_rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Fungsi untuk menampilkan pesan (flash message) dari session
function tampilkan_pesan() {
    if (isset($_SESSION['pesan'])) {
        echo "<div class='pesan'>" . htmlspecialchars($_SESSION['pesan']) . "</div>";
        unset($_SESSION['pesan']); // Hapus pesan setelah ditampilkan
    }
}
?>