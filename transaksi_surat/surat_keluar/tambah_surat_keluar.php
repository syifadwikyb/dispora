<?php
// FIX 1: Sesuaikan path require_once untuk naik dua level
require_once __DIR__ . '/../../templates/header.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor_agenda = $_POST['nomor_agenda'];
    $tujuan = $_POST['tujuan'];
    $nomor_surat = $_POST['nomor_surat'];
    $isi_ringkas = $_POST['isi_ringkas'];
    $kode_klasifikasi = $_POST['kode_klasifikasi'];
    $tanggal_surat = $_POST['tanggal_surat'];
    $keterangan = $_POST['keterangan'];
    $nama_file_final = null;

    if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] === 0) {
        $file = $_FILES['file_surat'];
        $nama_file_asli = $file['name'];
        $lokasi_file_tmp = $file['tmp_name'];
        
        // FIX 2: Sesuaikan path folder upload
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/ams/transaksi_surat/surat_keluar/file_keluar/";
        
        $ekstensi_file = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));
        $nama_asli_tanpa_ekstensi = pathinfo($nama_file_asli, PATHINFO_FILENAME);
        $nama_aman = preg_replace("/[^a-zA-Z0-9_-]/", "_", $nama_asli_tanpa_ekstensi);
        $nama_file_final = time() . "_" . $nama_aman . "." . $ekstensi_file;
        $target_file = $target_dir . $nama_file_final;
        
        // Pastikan folder tujuan ada sebelum memindahkan file
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        move_uploaded_file($lokasi_file_tmp, $target_file);
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

        $message = '<div class="alert alert-success">Data surat keluar berhasil disimpan!</div>';
    } catch (PDOException $e) {
        $message = '<div class="alert alert-danger">Database Error: ' . $e->getMessage() . '</div>';
    }
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Data Surat Keluar</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item"><a href="screen_surat_keluar.php">Surat Keluar</a></li>
        <li class="breadcrumb-item active">Tambah Data</li>
    </ol>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-envelope-plus-fill me-1"></i>
            Formulir Tambah Data Surat Keluar
        </div>
        <div class="card-body">
            <?php if (!empty($message)) echo $message; ?>
            <form action="" method="POST" enctype="multipart/form-data">
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
                            <label for="file_surat" class="form-label">Upload File</label>
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