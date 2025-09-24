<?php
require_once __DIR__ . '/../../config/database.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "DELETE FROM surat_masuk WHERE id_surat = ?";
        $stmt = $conn->prepare($sql);
        
        $stmt->execute([$id]);

        // Redirect kembali ke halaman daftar surat
        header("Location: screen_surat_masuk.php?status=deleted");
        exit();

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    // Redirect jika ID tidak valid
    header("Location: screen_surat_masuk.php?status=invalid_id");
    exit();
}
?>