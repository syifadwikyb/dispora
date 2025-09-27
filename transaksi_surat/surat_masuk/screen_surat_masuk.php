<?php
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../config/database.php';

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search_keyword = isset($_GET['search']) ? $_GET['search'] : '';
$where_clause = "";
$params = [];

if (!empty($search_keyword)) {
    $where_clause = " WHERE nomor_surat LIKE ? OR asal_surat LIKE ? OR isi_ringkas LIKE ? OR nomor_agenda LIKE ?";
    $like_keyword = '%' . $search_keyword . '%';
    $params = [$like_keyword, $like_keyword, $like_keyword, $like_keyword];
}

$sql_total = "SELECT COUNT(*) FROM surat_masuk" . $where_clause;
$stmt_total = $conn->prepare($sql_total);
$stmt_total->execute($params);
$total_rows = $stmt_total->fetchColumn();
$total_pages = ceil($total_rows / $limit);

$sql_data = "SELECT * FROM surat_masuk" . $where_clause . " ORDER BY tanggal_diterima DESC LIMIT ? OFFSET ?";
$stmt_data = $conn->prepare($sql_data);

$i = 1;
foreach ($params as $param_value) {
    $stmt_data->bindValue($i, $param_value, PDO::PARAM_STR);
    $i++;
}
$stmt_data->bindValue($i, $limit, PDO::PARAM_INT);
$stmt_data->bindValue($i + 1, $offset, PDO::PARAM_INT);

$stmt_data->execute();
$surat_list = $stmt_data->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Data Surat Masuk</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item active">Surat Masuk</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-envelope-paper-fill me-1"></i> <b>Kontrol & Navigasi</b>
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
                            <div class="position-relative flex-grow-1">
                                <input type="text" class="form-control pe-5" placeholder="Cari berdasarkan no. surat, tujuan..." name="search" value="<?= htmlspecialchars($search_keyword) ?>">
                                <?php if (!empty($search_keyword)): ?>
                                    <a href="screen_surat_masuk.php"
                                        class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center position-absolute"
                                        title="Hapus Filter"
                                        style="width: 24px; height: 24px; padding: 0; top: 50%; transform: translateY(-50%); right: 10px; z-index: 100;">
                                        <i class="bi bi-x-lg text-white"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
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

                <form action="" method="GET" id="limitForm" class="d-flex align-items-center">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search_keyword) ?>">
                    <select class="form-select form-select-sm" name="limit" onchange="this.form.submit();" style="width: auto;">
                        <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                    <span class="ms-2 text-muted">data per halaman</span>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>No. Agenda & Kode</th>
                            <th>Isi Ringkas & File</th>
                            <th>Asal Surat</th>
                            <th>No. Surat & Tgl. Surat</th>
                            <th style="width: 15%;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle">
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
                                                <i class="bi bi-file-earmark-text-fill text-secondary"></i> <?= htmlspecialchars($surat['nama_file']) ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($surat['asal_surat']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($surat['nomor_surat']) ?>
                                        <small class="d-block text-muted"><?= date('d M Y', strtotime($surat['tanggal_surat'])) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-wrap justify-content-center align-items-center" style="gap: 0.25rem;">
                                            <a href="edit_surat_masuk.php?id=<?= $surat['id_surat'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                            <button class="btn btn-sm btn-info" title="Disposisi"><i class="bi bi-file-earmark-text"></i></button>
                                            <button class="btn btn-sm btn-secondary" title="Print"><i class="bi bi-printer"></i></button>
                                            <a href="hapus_surat_masuk.php?id=<?= $surat['id_surat'] ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');"><i class="bi bi-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation" class="mt-3">
                <ul class="pagination justify-content-end">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>&search=<?= urlencode($search_keyword) ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&limit=<?= $limit ?>&search=<?= urlencode($search_keyword) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>&search=<?= urlencode($search_keyword) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../templates/footer.php';
?>