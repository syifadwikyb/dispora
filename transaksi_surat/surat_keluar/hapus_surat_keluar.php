<?php
// FIX 1: Path require_once harus naik dua level (../..) untuk mencapai folder config
require_once __DIR__ . '/../../config/database.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $primary_key_column = 'id_surat_keluar'; // Sesuaikan jika nama primary key berbeda

    try {
        // Opsional: Ambil nama file untuk dihapus dari server
        $stmt_select = $conn->prepare("SELECT nama_file FROM surat_keluar WHERE $primary_key_column = ?");
        $stmt_select->execute([$id]);
        $file_to_delete = $stmt_select->fetchColumn();

        if ($file_to_delete) {
            // FIX 2: Sesuaikan path ke folder lampiran surat keluar yang benar
            $path_file = $_SERVER['DOCUMENT_ROOT'] . "/ams/transaksi_surat/surat_keluar/file_keluar/" . $file_to_delete;
            if (file_exists($path_file)) {
                unlink($path_file);
            }
        }
        
        // Hapus data dari database
        $sql = "DELETE FROM surat_keluar WHERE $primary_key_column = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        // FIX 3: Redirect kembali ke halaman daftar yang benar (screen_surat_keluar.php)
        header("Location: screen_surat_keluar.php?status=deleted");
        exit();

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    // Redirect jika ID tidak valid
    header("Location: screen_surat_keluar.php?status=invalid_id");
    exit();
}
?>