<?php
require_once __DIR__ . '/../../config/database.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "DELETE FROM pengguna WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        $stmt->execute([$id]);

        header("Location: screen_user.php?sukses=Data berhasil dihapus!");
        exit;

    } catch (PDOException $e) {
        die("Error: Gagal menghapus data. " . $e->getMessage());
    }
} else {
    header("Location: screen_user.php?gagal=ID tidak valid!");
    exit;
}
?>