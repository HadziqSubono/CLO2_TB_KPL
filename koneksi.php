<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "db_vandesu_store";

$con = mysqli_connect($host, $user, $password, $database);
// Cek apakah semua parameter koneksi sudah diset
if (empty($host) || empty($user) || empty($database)) {
    die("Konfigurasi koneksi tidak lengkap. Mohon cek kembali parameter koneksi.");
}

// Lakukan koneksi ke database
$koneksi = mysqli_connect($host, $user, $password, $database);

// Cek apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Optional: set charset untuk mencegah masalah encoding
if (!mysqli_set_charset($koneksi, "utf8")) {
    die("Gagal mengatur karakter set: " . mysqli_error($koneksi));
}
?>