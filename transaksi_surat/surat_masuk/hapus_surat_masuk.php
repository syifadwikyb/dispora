<?php
require_once __DIR__ . '/../../config/database.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {        
        $stmt_select = $conn->prepare("SELECT nama_file FROM surat_masuk WHERE id_surat = ?");
        $stmt_select->execute([$id]);
        $file_to_delete = $stmt_select->fetchColumn();

        if ($file_to_delete) {
            $path_file = $_SERVER['DOCUMENT_ROOT'] . "/ams/transaksi_surat/surat_masuk/file_masuk/" . $file_to_delete;
            if (file_exists($path_file)) {
                unlink($path_file);
            }
        }
                
        $sql = "DELETE FROM surat_masuk WHERE id_surat = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        header("Location: screen_surat_masuk.php?sukses=Data surat berhasil dihapus!");
        exit;

    } catch (PDOException $e) {
        header("Location: screen_surat_masuk.php?gagal=" . urlencode("Gagal menghapus data."));
        exit;
    }
} else {
    header("Location: screen_surat_masuk.php?gagal=ID tidak valid!");
    exit;
}
?>