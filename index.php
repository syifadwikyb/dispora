<?php
// Sertakan file header dan koneksi database
// Pastikan header.php sudah memanggil session_start()
require_once __DIR__ . '/templates/header.php';
require_once __DIR__ . '/config/database.php';

// ===================================================================
// ## LOGIKA PENGAMANAN HALAMAN DAN PENGAMBILAN DATA ##
// ===================================================================

// Cek jika user belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Ambil data user dan instansi untuk notifikasi
$nama_user = $_SESSION['nama_lengkap'] ?? 'Pengguna';
$level_user = $_SESSION['level'] ?? 'User';

$stmt_instansi = $conn->prepare("SELECT nama_instansi FROM instansi WHERE id = 1");
$stmt_instansi->execute();
$instansi = $stmt_instansi->fetch(PDO::FETCH_ASSOC);
$nama_instansi = $instansi['nama_instansi'] ?? 'Akademi Keperawatan';

try {
    $stmt_masuk = $conn->query("SELECT COUNT(*) FROM surat_masuk");
    $count_surat_masuk = $stmt_masuk->fetchColumn();

    $stmt_keluar = $conn->query("SELECT COUNT(*) FROM surat_keluar");
    $count_surat_keluar = $stmt_keluar->fetchColumn();

    $count_disposisi = 0;

    $stmt_klasifikasi = $conn->query("SELECT COUNT(*) FROM klasifikasi_surat");
    $count_klasifikasi = $stmt_klasifikasi->fetchColumn();

    $stmt_pengguna = $conn->query("SELECT COUNT(*) FROM pengguna");
    $count_pengguna = $stmt_pengguna->fetchColumn();

} catch (PDOException $e) {
    $error_db = "Error mengambil data statistik: " . $e->getMessage();
}

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>

    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading">Selamat Datang, <?= htmlspecialchars($nama_user) ?>!</h4>
        <p>Anda berhasil login ke Sistem Manajemen Surat <strong><?= htmlspecialchars($nama_instansi) ?></strong> sebagai <strong><?= htmlspecialchars($level_user) ?></strong>.</p>
    </div>

    <?php if (isset($error_db)): ?>
        <div class="alert alert-danger"><?= $error_db ?></div>
    <?php else: ?>
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Jumlah Surat Masuk</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $count_surat_masuk ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-envelope-arrow-down-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Jumlah Surat Keluar</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $count_surat_keluar ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-envelope-arrow-up-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Jumlah Disposisi</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $count_disposisi ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-text-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Jumlah Klasifikasi Surat</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $count_klasifikasi ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-list-columns-reverse fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">Jumlah Pengguna</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?= $count_pengguna ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people-fill fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.card .border-left-primary { border-left: .25rem solid #4e73df!important; }
.card .border-left-success { border-left: .25rem solid #1cc88a!important; }
.card .border-left-info { border-left: .25rem solid #36b9cc!important; }
.card .border-left-warning { border-left: .25rem solid #f6c23e!important; }
.card .border-left-danger { border-left: .25rem solid #e74a3b!important; }
.text-gray-300 { color: #dddfeb!important; }
.text-gray-800 { color: #5a5c69!important; }
.text-xs { font-size: .8rem; }
</style>

<?php
require_once __DIR__ . '/templates/footer.php';
?>