<?php
session_start();
session_destroy();

// Cek apakah ada sinyal 'from=admin' di URL
if (isset($_GET['from']) && $_GET['from'] == 'admin') {
    // Jika iya, arahkan ke login admin
    header("Location: admin/login_admin.php");
} else {
    // Jika tidak, arahkan ke halaman utama pelanggan (default)
    header("Location: index.php");
}

exit();
?>