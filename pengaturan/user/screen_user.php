<?php
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../config/database.php';

$sukses = $_GET['sukses'] ?? null;
$gagal = $_GET['gagal'] ?? null;

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search_keyword = isset($_GET['search']) ? $_GET['search'] : '';
$where_clause = "";
$params = [];

if (!empty($search_keyword)) {
    $where_clause = " WHERE username LIKE ? OR nama_lengkap LIKE ? OR nip LIKE ?";
    $like_keyword = '%' . $search_keyword . '%';
    $params = [$like_keyword, $like_keyword, $like_keyword];
}

$sql_total = "SELECT COUNT(*) FROM pengguna" . $where_clause;
$stmt_total = $conn->prepare($sql_total);
$stmt_total->execute($params);
$total_rows = $stmt_total->fetchColumn();
$total_pages = ceil($total_rows / $limit);

$sql_data = "SELECT id, username, nama_lengkap, nip, level FROM pengguna" . $where_clause . " ORDER BY id ASC LIMIT ? OFFSET ?";
$stmt_data = $conn->prepare($sql_data);

$i = 1;
foreach ($params as $param_value) {
    $stmt_data->bindValue($i, $param_value, PDO::PARAM_STR);
    $i++;
}
$stmt_data->bindValue($i, $limit, PDO::PARAM_INT);
$stmt_data->bindValue($i + 1, $offset, PDO::PARAM_INT);

$stmt_data->execute();
$users = $stmt_data->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Manajemen User</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item active">Manajemen User</li>
    </ol>

    <?php if ($sukses): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($sukses) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-search me-1"></i> Kontrol & Navigasi
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 mb-2 mb-md-0">
                    <a href="tambah_user.php" class="btn btn-success me-2">
                        <i class="bi bi-person-plus-fill me-1"></i> Tambah User
                    </a>
                </div>
                <div class="col-md-8">
                    <form action="" method="get">
                        <div class="input-group">
                            <div class="position-relative flex-grow-1">
                                <input type="text" class="form-control" placeholder="Cari username, nama, atau NIP..." name="search" value="<?= htmlspecialchars($search_keyword) ?>">
                                <?php if (!empty($search_keyword)): ?>
                                    <a href="screen_user.php"
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
                <span class="fs-6 fw-bold"><i class="bi bi-table me-1"></i> Daftar Pengguna Sistem</span>
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
                <table class="table table-striped table-bordered">
                    <thead class="table-primary">
                        <tr class="text-center align-middle">
                            <th style="width: 5%;">No</th>
                            <th>Username</th>
                            <th>Nama & NIP</th>
                            <th style="width: 15%;">Level</th>
                            <th style="width: 15%;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
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
                            <?php $nomor = $offset + 1; ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="text-center align-middle"><?= $nomor++ ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($user['username']) ?></td>
                                    <td class="align-middle">
                                        <?= htmlspecialchars($user['nama_lengkap']) ?>
                                        <small class="d-block text-muted">NIP: <?= htmlspecialchars($user['nip'] ?: '-') ?></small>
                                    </td>
                                    <td class="text-center align-middle"><?= htmlspecialchars($user['level']) ?></td>
                                    <td class="text-center align-middle">
                                        <?php if ($user['level'] === 'Super Admin'): ?>
                                            <span class="badge bg-secondary">NO ACTION</span>
                                        <?php else: ?>
                                            <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm m-1" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                            <a href="javascript:void(0);"
                                                class="btn btn-sm btn-danger"
                                                title="Delete"
                                                onclick="confirmDelete('hapus_user.php?id=<?= $user['id'] ?>')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php endif; ?>
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