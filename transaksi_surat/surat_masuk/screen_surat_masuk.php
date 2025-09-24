<?php
require_once __DIR__ . '/../../templates/header.php';

$search_keyword = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM surat_masuk";
$params = [];

if (!empty($search_keyword)) {
    $sql .= " WHERE nomor_surat LIKE ? OR asal_surat LIKE ? OR isi_ringkas LIKE ? OR nomor_agenda LIKE ?";

    $like_keyword = '%' . $search_keyword . '%';
    $params = [$like_keyword, $like_keyword, $like_keyword, $like_keyword];
}

$sql .= " ORDER BY tanggal_diterima DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$surat_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Data Surat Masuk</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item active">Surat Masuk</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-envelope-paper-fill me-1"></i>
            Kontrol & Navigasi
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 mb-2 mb-md-0">
                    <a href="/ams/transaksi_surat/surat_masuk/tambah_surat_masuk.php" class="btn btn-success me-2">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Data
                    </a>
                    <button type="button" class="btn btn-secondary">
                        <i class="bi bi-box-arrow-down me-1"></i> Export Data
                    </button>
                </div>
                <div class="col-md-8">
                    <form action="" method="get">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Cari berdasarkan no. surat, asal surat..." name="search" value="<?= htmlspecialchars($search_keyword) ?>">
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
                <span class="fs-6 fw-bold"><i class="bi bi-table me-1"></i> Tabel Data Surat Masuk</span>
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
                        <tr>
                            <th>No. Agenda & Kode</th>
                            <th>Isi Ringkas & File</th>
                            <th>Asal Surat</th>
                            <th>No. Surat & Tgl. Surat</th>
                            <th style="width: 15%;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($surat_list)): ?>
                            <tr>
                                <td colspan="5" class="text-center">
                                    <?php if (!empty($search_keyword)): ?>
                                        Data dengan kata kunci "<?= htmlspecialchars($search_keyword) ?>" tidak ditemukan.
                                    <?php else: ?>
                                        Belum ada data surat masuk.
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($surat_list as $surat): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold"><?= htmlspecialchars($surat['nomor_agenda']) ?></span> /
                                        <span class="badge bg-secondary"><?= htmlspecialchars($surat['kode_klasifikasi']) ?></span>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($surat['isi_ringkas']) ?>

                                        <?php if (!empty($surat['nama_file'])): ?>
                                            <a href="/ams/transaksi_surat/surat_masuk/file_masuk/<?= htmlspecialchars($surat['nama_file']) ?>" class="d-block text-decoration-none" target="_blank" title="Lihat file">
                                                <i class="bi bi-file-earmark-pdf-fill text-danger"></i> <?= htmlspecialchars($surat['nama_file']) ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($surat['asal_surat']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($surat['nomor_surat']) ?>
                                        <small class="d-block text-muted"><?= date('d M Y', strtotime($surat['tanggal_surat'])) ?></small>
                                    </td>
                                    <td>
                                        <a href="edit_surat_masuk.php?id=<?= $surat['id_surat'] ?>" class="btn btn-sm btn-warning m-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <button class="btn btn-sm btn-info m-1" title="Disposisi"><i class="bi bi-file-earmark-text"></i></button>
                                        <button class="btn btn-sm btn-secondary m-1" title="Print"><i class="bi bi-printer"></i></button>

                                        <a href="hapus_surat_masuk.php?id=<?= $surat['id_surat'] ?>" class="btn btn-sm btn-danger m-1" title="Delete" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end">
                    <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php
// Ubah path ini sesuai dengan struktur folder Anda
require_once __DIR__ . '/../../templates/footer.php';
?>