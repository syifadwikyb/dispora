<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../templates/header.php';

$message = '';

if (!isset($_SESSION['user_id'])) {
    die('<div class="container-fluid px-4"><div class="alert alert-danger mt-4">Akses ditolak. Silakan login terlebih dahulu.</div></div>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_password = $_POST['admin_password'];
    
    if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] === 0) {
        $file = $_FILES['backup_file'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($file_extension !== 'sql') {
            $message = '<div class="alert alert-danger">Error: File yang diupload harus berekstensi .sql!</div>';
        } else {
            try {
                $stmt = $conn->prepare("SELECT password FROM users WHERE id_user = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($admin_password, $user['password'])) {
                    
                    $sql_content = file_get_contents($file['tmp_name']);
                    
                    $sql_queries = preg_split("/;\s*(\r\n|\n|\r)/", $sql_content);

                    foreach ($sql_queries as $query) {
                        $query = trim($query);
                        if (!empty($query)) {
                            $conn->exec($query);
                        }
                    }                 

                    $message = '<div class="alert alert-success">Database berhasil direstore dari file ' . htmlspecialchars($file['name']) . '!</div>';

                } else {
                    $message = '<div class="alert alert-danger">Error: Password yang Anda masukkan salah!</div>';
                }
            } catch (PDOException $e) {
                $message = '<div class="alert alert-danger">Restore Gagal: ' . $e->getMessage() . '</div>';
            }
        }
    } else {
        $message = '<div class="alert alert-danger">Error: Anda harus memilih file backup terlebih dahulu.</div>';
    }
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Restore Database</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item"><a href="/ams/pengaturan/backup_pengaturan.php">Backup</a></li>
        <li class="breadcrumb-item active">Restore</li>
    </ol>

    <div class="card">
        <div class="card-header bg-primary text-white"><i class="bi bi-database-fill-down me-1"></i> <b>Formulir Restore Database</b></div>
        <div class="card-body">
            
            <?php if (!empty($message)) echo $message; ?>

            <div class="alert alert-danger">
                <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> PERINGATAN!</h4>
                <p>Berhati-hatilah ketika merestore database karena semua data yang ada saat ini akan <span class="fw-bold">dihapus</span> dan diganti dengan data dari file backup. Pastikan file yang Anda gunakan adalah file backup yang benar.</p>
            </div>

            <form action="restore_pengaturan.php" method="POST" enctype="multipart/form-data">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="backup_file" class="form-label">Pilih File Backup (.sql)</label>
                        <input class="form-control" type="file" id="backup_file" name="backup_file" accept=".sql" required>
                    </div>
                    <div class="col-md-4">
                        <label for="admin_password" class="form-label">Password Admin Anda</label>
                        <input type="password" class="form-control" id="admin_password" name="admin_password" placeholder="Masukkan password untuk konfirmasi" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100" onclick="return confirm('APAKAH ANDA YAKIN? Semua data saat ini akan dihapus dan diganti dengan data dari file backup. Aksi ini tidak bisa dibatalkan.');">
                            <i class="bi bi-clock-history me-1"></i> RESTORE
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../templates/footer.php';
?>