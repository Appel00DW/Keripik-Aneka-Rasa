<?php
// TULIS PASSWORD YANG ANDA INGINKAN DI SINI
$password_admin = 'admin123'; // Ganti dengan password yang Anda mau

// Proses pembuatan hash
$hash_password = password_hash($password_admin, PASSWORD_BCRYPT);

echo "<h3>Password Generator</h3>";
echo "Password Plaintext: " . htmlspecialchars($password_admin);
echo "<hr>";
echo "<b>Hash untuk Database (COPY INI):</b><br>";
echo "<textarea rows='3' style='width:100%;'>" . $hash_password . "</textarea>";
?>