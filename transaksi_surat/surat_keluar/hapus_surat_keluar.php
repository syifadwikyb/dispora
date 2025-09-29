<?php
require_once __DIR__ . '/../../config/database.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $primary_key_column = 'id_surat_keluar';

    try {
        $stmt_select = $conn->prepare("SELECT nama_file FROM surat_keluar WHERE {$primary_key_column} = ?");
        $stmt_select->execute([$id]);
        $file_to_delete = $stmt_select->fetchColumn();

        $sql = "DELETE FROM surat_keluar WHERE {$primary_key_column} = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        if ($file_to_delete) {
            $path_file = $_SERVER['DOCUMENT_ROOT'] . "/ams/transaksi_surat/surat_keluar/file_keluar/" . $file_to_delete;
            if (file_exists($path_file)) {
                unlink($path_file);
            }
        }
        header("Location: screen_surat_keluar.php?sukses=Data surat keluar berhasil dihapus!");
        exit;

    } catch (PDOException $e) {
        header("Location: screen_surat_keluar.php?gagal=" . urlencode("Error: Gagal menghapus data."));
        exit;
    }
} else {
    header("Location: screen_surat_keluar.php?gagal=ID tidak valid!");
    exit;
}
?>