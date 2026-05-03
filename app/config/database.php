<?php
// ============================================
// FILE: app/config/database.php
// Fungsi: Koneksi ke database MySQL
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // ganti sesuai user MySQL kamu
define('DB_PASS', '');           // ganti sesuai password MySQL kamu
define('DB_NAME', 'sig_stunting');

// Buat koneksi
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek apakah koneksi berhasil
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset agar karakter Indonesia tampil benar
mysqli_set_charset($conn, "utf8");
?>