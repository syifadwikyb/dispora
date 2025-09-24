<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ubah path ini sesuai dengan struktur folder Anda
require_once __DIR__ . '/../../templates/header.php';

$message = '';

// Cek jika form sudah disubmit dengan method POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- 1. AMBIL DATA DARI FORM ---
    $nomor_agenda = $_POST['nomor_agenda'];
    $asal_surat = $_POST['asal_surat'];
    $nomor_surat = $_POST['nomor_surat'];
    $isi_ringkas = $_POST['isi_ringkas'];
    $kode_klasifikasi = $_POST['kode_klasifikasi'];
    $indeks_berkas = $_POST['indeks_berkas'];
    $tanggal_surat = $_POST['tanggal_surat'];
    $keterangan = $_POST['keterangan'];
    $nama_file_final = null; // Default nama file null

    // --- 2. LOGIKA PENANGANAN FILE UPLOAD ---
    if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] === 0) {
        $file = $_FILES['file_surat'];
        $nama_file_asli = $file['name'];
        $ukuran_file = $file['size'];
        $lokasi_file_tmp = $file['tmp_name'];

        // Tentukan folder tujuan
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/ams/transaksi_surat/surat_masuk/file_masuk/";

        // Dapatkan ekstensi file
        $ekstensi_file = strtolower(pathinfo($nama_file_asli, PATHINFO_EXTENSION));

        // Tentukan ekstensi yang diizinkan
        $allowed_types = ['jpg', 'jpeg', 'png', 'doc', 'docx', 'pdf'];

        // --- AWAL BAGIAN YANG DIUBAH (UNTUK NAMA FILE) ---
        
        // Ambil nama file asli tanpa ekstensi
        $nama_asli_tanpa_ekstensi = pathinfo($nama_file_asli, PATHINFO_FILENAME);

        // Bersihkan nama file dari spasi dan karakter aneh, ganti dengan underscore (_)
        $nama_aman = preg_replace("/[^a-zA-Z0-9_-]/", "_", $nama_asli_tanpa_ekstensi);

        // Gabungkan waktu unik + nama yang sudah aman + ekstensi file
        $nama_file_final = time() . "_" . $nama_aman . "." . $ekstensi_file;
        
        // --- AKHIR BAGIAN YANG DIUBAH ---
        
        $target_file = $target_dir . $nama_file_final;

        // Lakukan validasi
        if ($ukuran_file > 2 * 1024 * 1024) { // Batas 2MB
            $message = '<div class="alert alert-danger">Error: Ukuran file melebihi 2 MB.</div>';
        } else if (!in_array($ekstensi_file, $allowed_types)) {
            $message = '<div class="alert alert-danger">Error: Format file tidak diizinkan. Hanya JPG, PNG, DOC, DOCX, dan PDF yang boleh diupload.</div>';
        } else {
            // Jika semua validasi lolos, baru pindahkan file
            if (!move_uploaded_file($lokasi_file_tmp, $target_file)) {
                $message = '<div class="alert alert-danger">Error: Gagal memindahkan file. Periksa izin folder.</div>';
            }
        }
    }

    // --- 3. PROSES SIMPAN KE DATABASE ---
    if (empty($message)) {
        try {
            $sql = "INSERT INTO surat_masuk (nomor_agenda, asal_surat, nomor_surat, isi_ringkas, kode_klasifikasi, indeks_berkas, tanggal_surat, keterangan, nama_file) 
                    VALUES (:nomor_agenda, :asal_surat, :nomor_surat, :isi_ringkas, :kode_klasifikasi, :indeks_berkas, :tanggal_surat, :keterangan, :nama_file)";

            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':nomor_agenda', $nomor_agenda);
            $stmt->bindParam(':asal_surat', $asal_surat);
            $stmt->bindParam(':nomor_surat', $nomor_surat);
            $stmt->bindParam(':isi_ringkas', $isi_ringkas);
            $stmt->bindParam(':kode_klasifikasi', $kode_klasifikasi);
            $stmt->bindParam(':indeks_berkas', $indeks_berkas);
            $stmt->bindParam(':tanggal_surat', $tanggal_surat);
            $stmt->bindParam(':keterangan', $keterangan);
            $stmt->bindParam(':nama_file', $nama_file_final);

            $stmt->execute();

            // Set pesan sukses hanya jika tidak ada error sebelumnya
            $message = '<div class="alert alert-success">Data surat masuk berhasil disimpan!</div>';

        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Database Error: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Data Surat Masuk</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item"><a href="/ams/transaksi_surat/surat_masuk/screen_surat_masuk.php">Surat Masuk</a></li>
        <li class="breadcrumb-item active">Tambah Data</li>
    </ol>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-envelope-plus-fill me-1"></i>
            Formulir Tambah Data Surat Masuk
        </div>
        <div class="card-body">
            
            <?php if(!empty($message)) echo $message; // Tampilkan pesan di sini ?>

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
                            <label for="asal_surat" class="form-label">Asal Surat</label>
                            <input type="text" class="form-control" id="asal_surat" name="asal_surat" required>
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
                            <label for="indeks_berkas" class="form-label">Indeks Berkas</label>
                            <input type="text" class="form-control" id="indeks_berkas" name="indeks_berkas">
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="file_surat" class="form-label">Upload File</label>
                            <input class="form-control" type="file" id="file_surat" name="file_surat" accept=".jpg, .jpeg, .png, .doc, .docx, .pdf">
                            <div class="form-text">
                                Format file yang diperbolehkan: *.JPG, *.PNG, *.DOC, *.DOCX, *.PDF.<br>
                                Ukuran maksimal file: <strong>2 MB</strong>.
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy-fill me-1"></i> Simpan
                    </button>
                    <a href="/ams/transaksi_surat/surat_masuk.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Ubah path ini sesuai dengan struktur folder Anda
require_once __DIR__ . '/../../templates/footer.php';
?>