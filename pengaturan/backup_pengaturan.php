<?php
// Pastikan hanya admin yang bisa mengakses
session_start();
// if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'Super Admin') {
//     die("Akses ditolak. Anda harus login sebagai Super Admin.");
// }

// 1. Sertakan file koneksi database Anda
require_once __DIR__ . '/../config/database.php';

// ## PERBAIKAN: Mengambil nama database langsung dari koneksi aktif ##
try {
    $stmt = $conn->query("SELECT database()");
    $dbName = $stmt->fetchColumn();
} catch (PDOException $e) {
    die("Error mendapatkan nama database: " . $e->getMessage());
}

// 2. Set Header HTTP untuk memicu download file
$backup_file_name = 'backup_database_' . $dbName . '_' . date("Y-m-d-H-i-s") . '.sql';
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($backup_file_name) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');

// Variabel untuk menampung seluruh output SQL
$sql_output = "-- Backup untuk database: `{$dbName}`\n";
$sql_output .= "-- Dibuat pada: " . date('Y-m-d H:i:s') . "\n";
$sql_output .= "-- --------------------------------------------------------\n\n";

// 3. Ambil daftar semua tabel dari database
try {
    $tables = [];
    $stmt = $conn->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
} catch (PDOException $e) {
    die("Error mengambil daftar tabel: " . $e->getMessage());
}

// 4. Loop melalui setiap tabel untuk membuat backup
foreach ($tables as $table) {
    // Ambil struktur tabel (CREATE TABLE)
    $stmt_create = $conn->query("SHOW CREATE TABLE `{$table}`");
    $create_table_row = $stmt_create->fetch(PDO::FETCH_ASSOC);
    
    $sql_output .= "\n-- Struktur tabel untuk `{$table}`\n";
    $sql_output .= "DROP TABLE IF EXISTS `{$table}`;\n";
    $sql_output .= $create_table_row['Create Table'] . ";\n\n";

    // Ambil data dari tabel (INSERT INTO)
    $stmt_data = $conn->query("SELECT * FROM `{$table}`");
    $rows = $stmt_data->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows) > 0) {
        $sql_output .= "-- Dumping data untuk tabel `{$table}`\n";
        foreach ($rows as $row) {
            $sql_output .= "INSERT INTO `{$table}` VALUES(";
            $values = [];
            foreach ($row as $value) {
                if (isset($value)) {
                    // Escape special characters dalam data
                    $values[] = "'" . addslashes($value) . "'";
                } else {
                    $values[] = "NULL";
                }
            }
            $sql_output .= implode(', ', $values) . ");\n";
        }
        $sql_output .= "\n";
    }
}

// 5. Tampilkan output SQL yang akan diunduh oleh browser
echo $sql_output;

exit;
?>