<?php
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../config/database.php';

$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$search = $_GET['search'] ?? '';
$sql = "SELECT nama_file, isi_ringkas, tanggal_diterima, asal_surat, nomor_surat FROM surat_masuk";

$conditions = [];
$params = [];

$conditions[] = "(nama_file IS NOT NULL AND nama_file != '')";


if (!empty($start_date) && !empty($end_date)) {
    $conditions[] = "tanggal_diterima BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date . ' 23:59:59';
}

if (!empty($search)) {
    $conditions[] = "(isi_ringkas LIKE ? OR asal_surat LIKE ? OR nomor_surat LIKE ?)";
    $like_keyword = '%' . $search . '%';
    array_push($params, $like_keyword, $like_keyword, $like_keyword);
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY tanggal_diterima DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Galeri File Surat Masuk</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item active">Galeri File Surat Masuk</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white"><i class="bi bi-search me-1"></i><b>Filter Galeri</b></div>
        <div class="card-body">
            <form action="" method="GET" class="w-100">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3"><label for="start_date" class="form-label">Dari Tanggal</label><input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
                    </div>

                    <div class="col-md-3"><label for="end_date" class="form-label">Sampai Tanggal</label><input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="search" class="form-label">Cari Isi Ringkas, Asal, atau No. Surat</label>
                        <div class="position-relative">
                            <input type="text" class="form-control pe-5" id="search" name="search" placeholder="Masukkan kata kunci..." value="<?= htmlspecialchars($search ?? '') ?>">
                            <?php if (!empty($search)): ?>
                                <a href="screen_surat_masuk.php"
                                    class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center position-absolute"
                                    title="Hapus Filter"
                                    style="width: 24px; height: 24px; padding: 0; top: 50%; transform: translateY(-50%); right: 10px; z-index: 100;">
                                    <i class="bi bi-x-lg text-white"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-2"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-filter"></i> Filter</button></div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <?php if (empty($files)): ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle-fill h4"></i>
                    <p class="mb-0 mt-2"><strong>File tidak ditemukan.</strong><br>Silakan ubah kriteria filter Anda dan coba lagi.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($files as $file): ?>
                <?php
                $filePath = "/ams/transaksi_surat/surat_masuk/file_masuk/" . rawurlencode($file['nama_file']);
                $fileExtension = strtolower(pathinfo($file['nama_file'], PATHINFO_EXTENSION));
                $imageSrc = '';
                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($fileExtension, $imageExtensions)) {
                    $imageSrc = $filePath;
                } elseif ($fileExtension == 'pdf') {
                    $imageSrc = "/ams/assets/images/icon_pdf.svg";
                } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                    $imageSrc = "/ams/assets/images/icon_word.svg";
                } else {
                    $imageSrc = "/ams/assets/images/icon_document.svg";
                }
                ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm d-flex flex-column">
                        <a href="<?= $filePath ?>" target="_blank" class="text-decoration-none text-dark d-flex flex-column flex-grow-1">
                            <img src="<?= $imageSrc ?>" class="card-img-top" alt="Preview File" style="height: 200px; object-fit: contain; padding: 10px;">

                            <div class="card-footer text-muted small mt-auto">
                                <p class="card-text fw-bold small mb-0">
                                    <?= htmlspecialchars($file['isi_ringkas']) ?>
                                </p>
                                Diterima: <?= date('d M Y, H:i', strtotime($file['tanggal_diterima'])) ?>
                            </div>

                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/../../templates/footer.php';
?>