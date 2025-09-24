<?php
// Pengaturan Database
$host = 'localhost';
$db_name = 'ams_db';
$username = 'root';
$password = '';    

// Membuat koneksi
try {
    $conn = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Koneksi gagal: " . $e->getMessage();
    die();
}

// Memulai Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>