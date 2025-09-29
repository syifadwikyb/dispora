<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../config/database.php';

// Logika untuk menangani notifikasi (jika ada)
$sukses = $_GET['sukses'] ?? null;
$gagal = $_GET['gagal'] ?? null;

// ===================================================================
// ## BLOK PHP YANG DIPERBAIKI UNTUK PAGINASI ##
// ===================================================================

// 1. Tentukan Limit Data & Halaman Saat Ini
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 2. Logika Pencarian
$search_keyword = isset($_GET['search']) ? $_GET['search'] : '';
$where_clause = "";
$params = [];

if (!empty($search_keyword)) {
    $where_clause = " WHERE kode LIKE ? OR nama LIKE ? OR uraian LIKE ?";
    $like_keyword = '%' . $search_keyword . '%';
    $params = [$like_keyword, $like_keyword, $like_keyword];
}

// 3. Query untuk MENGHITUNG TOTAL DATA (untuk paginasi)
$sql_total = "SELECT COUNT(*) FROM klasifikasi_surat" . $where_clause;
$stmt_total = $conn->prepare($sql_total);
$stmt_total->execute($params);
$total_rows = $stmt_total->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// 4. Query UTAMA untuk MENGAMBIL DATA (dengan LIMIT dan OFFSET)
$sql_data = "SELECT * FROM klasifikasi_surat" . $where_clause . " ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt_data = $conn->prepare($sql_data);

// Gunakan bindValue() untuk kontrol tipe data yang eksplisit agar tidak error
$i = 1; // Counter untuk placeholder positional
// Bind parameter pencarian (sebagai string)
foreach ($params as $param_value) {
    $stmt_data->bindValue($i, $param_value, PDO::PARAM_STR);
    $i++;
}
// Bind parameter LIMIT dan OFFSET (sebagai integer)
$stmt_data->bindValue($i, $limit, PDO::PARAM_INT);
$stmt_data->bindValue($i + 1, $offset, PDO::PARAM_INT);

$stmt_data->execute();
$klasifikasi_list = $stmt_data->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Referensi Klasifikasi Surat</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item active">Klasifikasi Surat</li>
    </ol>

    <?php if ($sukses): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($sukses) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if ($gagal): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($gagal) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-search me-1"></i><b>Kontrol & Navigasi</b>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 mb-2 mb-md-0">
                    <a href="tambah_referensi.php" class="btn btn-success me-2"><i class="bi bi-plus-circle me-1"></i> Tambah Data</a>
                    <a href="import_referensi.php" class="btn btn-secondary"><i class="bi bi-box-arrow-up me-1"></i> Import Data</a>
                </div>
                <div class="col-md-8">
                    <form action="" method="get">
                        <div class="input-group">
                            <div class="position-relative flex-grow-1">
                                <input type="text" class="form-control" placeholder="Cari username, nama, atau NIP..." name="search" value="<?= htmlspecialchars($search_keyword) ?>">
                                <?php if (!empty($search_keyword)): ?>
                                    <a href="screen_referensi.php"
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
                <span class="fs-6 fw-bold"><i class="bi bi-table me-1"></i> Tabel Data Klasifikasi Surat</span>

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
                    <thead class="table-primary">
                        <tr class="text-center align-middle">
                            <th style="width: 15%;">Kode</th>
                            <th>Nama</th>
                            <th>Uraian</th>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin'): ?>
                                <th style="width: 15%;">Tindakan</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($klasifikasi_list)): ?>
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="alert alert-warning text-center\">
                                        <i class="bi bi-exclamation-triangle-fill h4"></i>
                                        <div class="mb-0 mt-2">
                                            <p>Data dengan kata kunci "<?= htmlspecialchars($search_keyword) ?>" tidak ditemukan.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($klasifikasi_list as $klasifikasi): ?>
                                <tr>
                                    <td class="text-center align-middle fw-bold"><?= htmlspecialchars($klasifikasi['kode']) ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($klasifikasi['nama']) ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($klasifikasi['uraian']) ?></td>
                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin'): ?>
                                        <td class="text-center align-middle">
                                            <a href="edit_referensi.php?id=<?= $klasifikasi['id'] ?>" class="btn btn-warning btn-sm m-1" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                            <a href="javascript:void(0);"
                                                class="btn btn-danger btn-sm m-1"
                                                title="Delete"
                                                onclick="confirmDelete('hapus_referensi.php?id=<?= $klasifikasi['id'] ?>')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    <?php endif; ?>
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
require_once __DIR__ . '/../templates/footer.php';
?>