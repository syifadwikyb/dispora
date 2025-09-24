<?php
// Bagian 1: Logika PHP (Update & Pengambilan Data)
require_once __DIR__ . '/../../config/database.php'; 

$message = '';
$surat = null;
$primary_key_column = 'id_surat_keluar'; // Sesuaikan jika nama primary key berbeda

// Logika untuk memproses form UPDATE (method POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_surat = $_POST['id_surat_keluar'];
    $nomor_agenda = $_POST['nomor_agenda'];
    $tujuan = $_POST['tujuan'];
    $nomor_surat = $_POST['nomor_surat'];
    $isi_ringkas = $_POST['isi_ringkas'];
    $kode_klasifikasi = $_POST['kode_klasifikasi'];
    $tanggal_surat = $_POST['tanggal_surat'];
    $keterangan = $_POST['keterangan'];
    $nama_file_lama = $_POST['nama_file_lama'];
    $nama_file_final = $nama_file_lama;

    // Logika upload file baru (jika ada)
    if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] === 0) {
        if (!empty($nama_file_lama)) {
            // FIX 1: Tambahkan slash (/) setelah nama folder
            $path_file_lama = $_SERVER['DOCUMENT_ROOT'] . "/ams/transaksi_surat/surat_keluar/file_keluar/" . $nama_file_lama;
            if (file_exists($path_file_lama)) {
                unlink($path_file_lama);
            }
        }
        $file = $_FILES['file_surat'];
        $nama_file_asli = $file['name'];
        $lokasi_file_tmp = $file['tmp_name'];
        // FIX 1: Tambahkan slash (/) setelah nama folder
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/ams/transaksi_surat/surat_keluar/file_keluar/";
        $ekstensi_file = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));
        $nama_asli_tanpa_ekstensi = pathinfo($nama_file_asli, PATHINFO_FILENAME);
        $nama_aman = preg_replace("/[^a-zA-Z0-9_-]/", "_", $nama_asli_tanpa_ekstensi);
        $nama_file_final = time() . "_" . $nama_aman . "." . $ekstensi_file;
        $target_file = $target_dir . $nama_file_final;
        move_uploaded_file($lokasi_file_tmp, $target_file);
    }
    
    try {
        $sql = "UPDATE surat_keluar SET 
                    nomor_agenda = :nomor_agenda, tujuan = :tujuan, nomor_surat = :nomor_surat, 
                    isi_ringkas = :isi_ringkas, kode_klasifikasi = :kode_klasifikasi, 
                    tanggal_surat = :tanggal_surat, keterangan = :keterangan, nama_file = :nama_file 
                WHERE $primary_key_column = :id_surat";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nomor_agenda' => $nomor_agenda, ':tujuan' => $tujuan, ':nomor_surat' => $nomor_surat, 
            ':isi_ringkas' => $isi_ringkas, ':kode_klasifikasi' => $kode_klasifikasi,
            ':tanggal_surat' => $tanggal_surat, ':keterangan' => $keterangan, ':nama_file' => $nama_file_final, 
            ':id_surat' => $id_surat
        ]);

        // FIX 3: Arahkan redirect ke screen_surat_keluar.php
        header("Location: screen_surat_keluar.php?status=updated");
        exit();
    } catch (PDOException $e) {
        $message = '<div class="alert alert-danger">Update Gagal: ' . $e->getMessage() . '</div>';
    }
}

// Logika untuk mengambil data awal (method GET)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_surat = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM surat_keluar WHERE $primary_key_column = ?");
    $stmt->execute([$id_surat]);
    $surat = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$surat) $error_fatal = "Data surat tidak ditemukan!";
} else {
    $error_fatal = "ID Surat tidak valid.";
}

// Bagian 2: Tampilan HTML
require_once __DIR__ . '/../../templates/header.php';

if (isset($error_fatal)) {
    echo '<div class="container-fluid px-4"><div class="alert alert-danger mt-4">' . $error_fatal . '</div></div>';
    require_once __DIR__ . '/../../templates/footer.php';
    exit();
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Data Surat Keluar</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item"><a href="screen_surat_keluar.php">Surat Keluar</a></li>
        <li class="breadcrumb-item active">Edit Data</li>
    </ol>
    <div class="card">
        <div class="card-header bg-warning text-white"><i class="bi bi-pencil-square me-1"></i> Formulir Edit Data</div>
        <div class="card-body">
            <?php if (!empty($message)) echo $message; ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_surat_keluar" value="<?= htmlspecialchars($surat[$primary_key_column]) ?>">
                <input type="hidden" name="nama_file_lama" value="<?= htmlspecialchars($surat['nama_file']) ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nomor_agenda" class="form-label">Nomor Agenda</label>
                            <input type="text" class="form-control" id="nomor_agenda" name="nomor_agenda" value="<?= htmlspecialchars($surat['nomor_agenda']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="nomor_surat" class="form-label">Nomor Surat</label>
                            <input type="text" class="form-control" id="nomor_surat" name="nomor_surat" value="<?= htmlspecialchars($surat['nomor_surat']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="tujuan" class="form-label">Tujuan Surat</label>
                            <input type="text" class="form-control" id="tujuan" name="tujuan" value="<?= htmlspecialchars($surat['tujuan']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_surat" class="form-label">Tanggal Surat</label>
                            <input type="date" class="form-control" id="tanggal_surat" name="tanggal_surat" value="<?= htmlspecialchars($surat['tanggal_surat']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="isi_ringkas" class="form-label">Isi Ringkas</label>
                            <textarea class="form-control" id="isi_ringkas" name="isi_ringkas" rows="3" required><?= htmlspecialchars($surat['isi_ringkas']) ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kode_klasifikasi" class="form-label">Kode Klasifikasi</label>
                            <input type="text" class="form-control" id="kode_klasifikasi" name="kode_klasifikasi" value="<?= htmlspecialchars($surat['kode_klasifikasi']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?= htmlspecialchars($surat['keterangan']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="file_surat" class="form-label">Upload File Baru (Opsional)</label>
                            <input class="form-control" type="file" id="file_surat" name="file_surat">
                            <?php if (!empty($surat['nama_file'])): ?>
                                <div class="form-text">File saat ini: <a href="/ams/transaksi_surat/surat_keluar/file_keluar/<?= htmlspecialchars($surat['nama_file']) ?>" target="_blank"><?= htmlspecialchars($surat['nama_file']) ?></a></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-floppy-fill me-1"></i> Simpan Perubahan</button>
                    <a href="screen_surat_keluar.php" class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../templates/footer.php';
?>