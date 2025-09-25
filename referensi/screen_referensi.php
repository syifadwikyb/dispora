<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../config/database.php';

// Logika untuk menangani notifikasi (jika ada)
$sukses = $_GET['sukses'] ?? null;
$gagal = $_GET['gagal'] ?? null;

// Logika Pencarian
$search_keyword = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM klasifikasi_surat";
$params = [];

if (!empty($search_keyword)) {
    $sql .= " WHERE kode LIKE ? OR nama LIKE ? OR uraian LIKE ?";
    $like_keyword = '%' . $search_keyword . '%';
    $params = [$like_keyword, $like_keyword, $like_keyword];
}
$sql .= " ORDER BY kode ASC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$klasifikasi_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Referensi Klasifikasi Surat</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item active">Klasifikasi Surat</li>
    </ol>    

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-envelope-paper-fill me-1"></i>
            Kontrol & Navigasi
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 mb-2 mb-md-0">
                    <a href="/ams/referensi/tambah_referensi.php" class="btn btn-success me-2">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Data
                    </a>
                    <a href="/ams/referensi/import_referensi.php" class="btn btn-secondary">
                        <i class="bi bi-box-arrow-up me-1"></i> Import Data
                    </a>
                </div>
                <div class="col-md-8">
                    <form action="" method="get">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Cari berdasarkan kode, nama, atau uraian" name="search" value="<?= htmlspecialchars($search_keyword) ?>">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Cari</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fs-6 fw-bold"><i class="bi bi-table me-1"></i> Tabel Data Klasifikasi Surat</span>
                <div class="d-flex align-items-center">
                    <i class="bi bi-gear-fill me-2" title="Pengaturan Tampilan"></i>
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option selected>10</option>
                        <option value="1">25</option>
                        <option value="2">50</option>
                        <option value="3">100</option>
                    </select>
                    <span class="ms-2">data per halaman</span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="table-primary">
                        <tr class="text-center align-middle">
                            <th style="width: 15%;">Kode</th>
                            <th>Nama</th>
                            <th>Uraian</th>
                            <th style="width: 15%;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($klasifikasi_list)): ?>
                            <tr>
                                <td colspan="4" class="text-center">Data tidak ditemukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($klasifikasi_list as $klasifikasi): ?>
                                <tr>
                                    <td class="text-center align-middle fw-bold"><?= htmlspecialchars($klasifikasi['kode']) ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($klasifikasi['nama']) ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($klasifikasi['uraian']) ?></td>
                                    <td class="text-center align-middle">
                                        <a href="edit_referensi.php?id=<?= $klasifikasi['id'] ?>" class="btn btn-warning btn-sm m-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="hapus_referensi.php?id=<?= $klasifikasi['id'] ?>" class="btn btn-danger btn-sm m-1" title="Delete" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../templates/footer.php';
?>