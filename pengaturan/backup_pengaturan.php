<?php
session_start();
require_once __DIR__ . '/../config/database.php';

try {
    $stmt = $conn->query("SELECT database()");
    $dbName = $stmt->fetchColumn();
} catch (PDOException $e) {
    die("Error mendapatkan nama database: " . $e->getMessage());
}

$backup_file_name = 'backup_database_' . $dbName . '_' . date("Y-m-d-H-i-s") . '.sql';
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($backup_file_name) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$sql_output = "-- Backup untuk database: `{$dbName}`\n";
$sql_output .= "-- Dibuat pada: " . date('Y-m-d H:i:s') . "\n";
$sql_output .= "-- --------------------------------------------------------\n\n";

try {
    $tables = [];
    $stmt = $conn->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
} catch (PDOException $e) {
    die("Error mengambil daftar tabel: " . $e->getMessage());
}

foreach ($tables as $table) {
    $stmt_create = $conn->query("SHOW CREATE TABLE `{$table}`");
    $create_table_row = $stmt_create->fetch(PDO::FETCH_ASSOC);
    
    $sql_output .= "\n-- Struktur tabel untuk `{$table}`\n";
    $sql_output .= "DROP TABLE IF EXISTS `{$table}`;\n";
    $sql_output .= $create_table_row['Create Table'] . ";\n\n";

    $stmt_data = $conn->query("SELECT * FROM `{$table}`");
    $rows = $stmt_data->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows) > 0) {
        $sql_output .= "-- Dumping data untuk tabel `{$table}`\n";
        foreach ($rows as $row) {
            $sql_output .= "INSERT INTO `{$table}` VALUES(";
            $values = [];
            foreach ($row as $value) {
                if (isset($value)) {
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

echo $sql_output;

exit;
?>