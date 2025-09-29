<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor_agenda = $_POST['nomor_agenda'];
    $tujuan = $_POST['tujuan'];
    $nomor_surat = $_POST['nomor_surat'];
    $isi_ringkas = $_POST['isi_ringkas'];
    $kode_klasifikasi = $_POST['kode_klasifikasi'];
    $tanggal_surat = $_POST['tanggal_surat'];
    $keterangan = $_POST['keterangan'];
    $nama_file_final = null;

    if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] === 0 && !empty($_FILES['file_surat']['tmp_name'])) {
        $file = $_FILES['file_surat'];
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        
        if (!in_array($file['type'], $allowed_types) || $file['size'] > 2 * 1024 * 1024) {
            header("Location: tambah_surat_keluar.php?gagal=" . urlencode("Error: Cek kembali format (JPG, PNG, PDF, DOC, DOCX) dan ukuran file (Maks 2MB)."));
            exit;
        } else {
            $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/ams/transaksi_surat/surat_keluar/file_keluar/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

            $ekstensi_file = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $nama_aman = preg_replace("/[^a-zA-Z0-9_-]/", "_", pathinfo($file['name'], PATHINFO_FILENAME));
            $nama_file_final = time() . "_" . $nama_aman . "." . $ekstensi_file;
            $target_file = $target_dir . $nama_file_final;

            if (!move_uploaded_file($file['tmp_name'], $target_file)) {
                header("Location: tambah_surat_keluar.php?gagal=" . urlencode("Error: Gagal memindahkan file. Periksa izin folder."));
                exit;
            }
        }
    }

    try {
        $sql = "INSERT INTO surat_keluar (nomor_agenda, tujuan, nomor_surat, isi_ringkas, kode_klasifikasi, tanggal_surat, keterangan, nama_file) 
                VALUES (:nomor_agenda, :tujuan, :nomor_surat, :isi_ringkas, :kode_klasifikasi, :tanggal_surat, :keterangan, :nama_file)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nomor_agenda', $nomor_agenda);
        $stmt->bindParam(':tujuan', $tujuan);
        $stmt->bindParam(':nomor_surat', $nomor_surat);
        $stmt->bindParam(':isi_ringkas', $isi_ringkas);
        $stmt->bindParam(':kode_klasifikasi', $kode_klasifikasi);
        $stmt->bindParam(':tanggal_surat', $tanggal_surat);
        $stmt->bindParam(':keterangan', $keterangan);
        $stmt->bindParam(':nama_file', $nama_file_final);
        $stmt->execute();

        header("Location: screen_surat_keluar.php?sukses=Data surat keluar berhasil disimpan!");
        exit;
    } catch (PDOException $e) {
        header("Location: tambah_surat_keluar.php?gagal=" . urlencode("Database Error: " . $e->getMessage()));
        exit;
    }
}

require_once __DIR__ . '/../../templates/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Data Surat Keluar</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item"><a href="screen_surat_keluar.php">Surat Keluar</a></li>
        <li class="breadcrumb-item active">Tambah Data</li>
    </ol>

    <div class="card">
        <div class="card-header bg-success text-white">
            <i class="bi bi-envelope-plus-fill me-1"></i>
            <b>Formulir Tambah Data Surat Keluar</b>
        </div>
        <div class="card-body">
            
            <?php if (isset($_GET['gagal'])): ?>
                <div class="alert alert-danger" role="alert"><?= htmlspecialchars(urldecode($_GET['gagal'])) ?></div>
            <?php endif; ?>

            <form action="tambah_surat_keluar.php" method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nomor_agenda" class="form-label">Nomor Agenda</label>
                            <input type="text" class="form-control" id="nomor_agenda" name="nomor_agenda" required>
                        </div>
                        <div class="mb-3">
                            <label for="nomor_surat" class="form-label">Nomor Surat</label>
                            <input type="text" class="form-control" id="nomor_surat" name="nomor_surat" required>
                        </div>
                        <div class="mb-3">
                            <label for="tujuan" class="form-label">Tujuan Surat</label>
                            <input type="text" class="form-control" id="tujuan" name="tujuan" required>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_surat" class="form-label">Tanggal Surat</label>
                            <input type="date" class="form-control" id="tanggal_surat" name="tanggal_surat" required>
                        </div>
                        <div class="mb-3">
                            <label for="isi_ringkas" class="form-label">Isi Ringkas</label>
                            <textarea class="form-control" id="isi_ringkas" name="isi_ringkas" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kode_klasifikasi" class="form-label">Kode Klasifikasi</label>
                            <input type="text" class="form-control" id="kode_klasifikasi" name="kode_klasifikasi">
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="file_surat" class="form-label">Upload File (Opsional)</label>
                            <input class="form-control" type="file" id="file_surat" name="file_surat" accept=".jpg, .jpeg, .png, .doc, .docx, .pdf">
                            <div class="form-text">Ukuran maksimal file: <strong>2 MB</strong>.</div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-floppy-fill me-1"></i> Simpan</button>
                    <a href="screen_surat_keluar.php" class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../templates/footer.php';
?>