
<?php
// GANTI 'nama_proyek_crm' DENGAN NAMA FOLDER PROYEK ANDA YANG SEBENARNYA
define('BASE_URL', 'http://localhost/nama_proyek_crm');

// Mulai session di setiap halaman
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi Database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_crm_sederhana";

// Membuat Koneksi
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek Koneksi
if (!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}
?>