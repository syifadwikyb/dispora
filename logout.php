<?php
// Selalu mulai session untuk bisa menghapusnya
session_start();

// Hapus semua variabel session
session_unset();

// Hancurkan session
session_destroy();

// Alihkan ke halaman login
// Ganti '/ams/' sesuai dengan nama folder proyek Anda jika berbeda
header("Location: /ams/login.php"); 
exit();
?>