<?php
// Sertakan file header dan koneksi database
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../config/database.php';

// --- LOGIKA PENGAMBILAN DATA & FILTER ---
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$search = $_GET['search'] ?? '';

// ## PERUBAHAN 1: Ganti nama tabel dan kolom di SELECT
$sql = "SELECT nama_file, isi_ringkas, tanggal_dikirim, tujuan, nomor_surat FROM surat_keluar";

$conditions = [];
$params = [];

// Tambahkan kondisi WAJIB: hanya tampilkan jika ada nama file
$conditions[] = "(nama_file IS NOT NULL AND nama_file != '')";

// Tambahkan kondisi filter tanggal jika diisi
if (!empty($start_date) && !empty($end_date)) {
    // ## PERUBAHAN 2: Ganti kolom tanggal untuk filter
    $conditions[] = "DATE(tanggal_dikirim) BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;
}

// Tambahkan kondisi filter pencarian jika diisi
if (!empty($search)) {
    // ## PERUBAHAN 3: Ganti 'asal_surat' menjadi 'tujuan'
    $conditions[] = "(isi_ringkas LIKE ? OR tujuan LIKE ? OR nomor_surat LIKE ?)";
    $like_keyword = '%' . $search . '%';
    array_push($params, $like_keyword, $like_keyword, $like_keyword);
}

// Gabungkan semua kondisi ke dalam query SQL
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// ## PERUBAHAN 4: Ganti kolom tanggal untuk pengurutan
$sql .= " ORDER BY tanggal_dikirim DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Galeri File Surat Keluar</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item active">Galeri File Surat Keluar</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white"><i class="bi bi-search me-1"></i><b>Filter Galeri</b></div>
        <div class="card-body">
            <form action="" method="GET" class="w-100">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3"><label for="start_date" class="form-label">Dari Tanggal</label><input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>"></div>
                    <div class="col-md-3"><label for="end_date" class="form-label">Sampai Tanggal</label><input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>"></div>
                    <div class="col-md-4">
                        <label for="search" class="form-label">Cari Isi Ringkas, Tujuan, atau No. Surat</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Masukkan kata kunci..." value="<?= htmlspecialchars($search) ?>">
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
                // ## PERUBAHAN 6: Ganti path folder file ##
                $filePath = "/ams/transaksi_surat/surat_keluar/file_keluar/" . rawurlencode($file['nama_file']);
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
                                Dikirim: <?= date('d M Y, H:i', strtotime($file['tanggal_dikirim'])) ?>
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