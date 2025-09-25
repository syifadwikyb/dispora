<?php
// 1. Sertakan koneksi database
require_once __DIR__ . '/../config/database.php';

// 2. Cek apakah ID ada dan merupakan angka
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // 3. Siapkan dan jalankan query DELETE
        $sql = "DELETE FROM klasifikasi_surat WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        // Eksekusi dengan mengirimkan ID dalam array
        $stmt->execute([$id]);

        // 4. Jika berhasil, redirect kembali ke halaman utama dengan pesan sukses
        header("Location: screen_referensi.php?sukses=Data berhasil dihapus!");
        exit;

    } catch (PDOException $e) {
        // 5. Jika gagal, hentikan program dan tampilkan error
        die("Error: Gagal menghapus data. " . $e->getMessage());
    }
} else {
    // 6. Redirect jika ID tidak ada atau tidak valid
    header("Location: screen_referensi.php?gagal=ID tidak valid!");
    exit;
}
?>