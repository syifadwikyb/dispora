<?php
// 1. Mulai dengan error reporting dan session
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// 2. Sertakan HANYA file yang dibutuhkan untuk logika
require_once __DIR__ . '/../../config/database.php';

// Variabel untuk notifikasi dan data
$message = '';
$surat = null;
$error_fatal = null;
$primary_key_column = 'id_surat'; // Nama kolom primary key

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_surat = $_POST['id_surat'];
    $nomor_agenda = $_POST['nomor_agenda'];
    $asal_surat = $_POST['asal_surat'];
    $nomor_surat = $_POST['nomor_surat'];
    $isi_ringkas = $_POST['isi_ringkas'];
    $kode_klasifikasi = $_POST['kode_klasifikasi'];
    $indeks_berkas = $_POST['indeks_berkas'];
    $tanggal_surat = $_POST['tanggal_surat'];
    $keterangan = $_POST['keterangan'];
    $nama_file_lama = $_POST['nama_file_lama'];
    $nama_file_final = $nama_file_lama;

    // Logika upload file baru (jika ada)
    if (isset($_FILES['file_surat']) && $_FILES['file_surat']['error'] === 0 && !empty($_FILES['file_surat']['tmp_name'])) {
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/ams/transaksi_surat/surat_masuk/file_masuk/";
        
        // Hapus file lama terlebih dahulu
        if (!empty($nama_file_lama) && file_exists($target_dir . $nama_file_lama)) {
            unlink($target_dir . $nama_file_lama);
        }

        $file = $_FILES['file_surat'];
        $ekstensi_file = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $nama_aman = preg_replace("/[^a-zA-Z0-9_-]/", "_", pathinfo($file['name'], PATHINFO_FILENAME));
        $nama_file_final = time() . "_" . $nama_aman . "." . $ekstensi_file;
        $target_file = $target_dir . $nama_file_final;
        
        move_uploaded_file($file['tmp_name'], $target_file);
    }
    
    try {
        $sql = "UPDATE surat_masuk SET 
                    nomor_agenda = :nomor_agenda, asal_surat = :asal_surat, nomor_surat = :nomor_surat, 
                    isi_ringkas = :isi_ringkas, kode_klasifikasi = :kode_klasifikasi, indeks_berkas = :indeks_berkas, 
                    tanggal_surat = :tanggal_surat, keterangan = :keterangan, nama_file = :nama_file 
                WHERE {$primary_key_column} = :id_surat";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nomor_agenda' => $nomor_agenda, ':asal_surat' => $asal_surat, ':nomor_surat' => $nomor_surat,
            ':isi_ringkas' => $isi_ringkas, ':kode_klasifikasi' => $kode_klasifikasi, ':indeks_berkas' => $indeks_berkas,
            ':tanggal_surat' => $tanggal_surat, ':keterangan' => $keterangan, ':nama_file' => $nama_file_final,
            ':id_surat' => $id_surat
        ]);

        // Redirect ke halaman daftar dengan pesan sukses
        header("Location: screen_surat_masuk.php?sukses=Data surat berhasil diperbarui!");
        exit;

    } catch (PDOException $e) {
        // Jika gagal, redirect kembali ke form edit dengan pesan error
        header("Location: edit_surat_masuk.php?id={$id_surat}&gagal=" . urlencode("Update Gagal: " . $e->getMessage()));
        exit;
    }
}

// --- LOGIKA PENGAMBILAN DATA UNTUK DITAMPILKAN (METHOD GET) ---
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_surat = $_GET['id'];
    try {
        $stmt = $conn->prepare("SELECT * FROM surat_masuk WHERE {$primary_key_column} = ?");
        $stmt->execute([$id_surat]);
        $surat = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$surat) {
            $error_fatal = "Data surat dengan ID {$id_surat} tidak ditemukan!";
        }
    } catch (PDOException $e) {
        $error_fatal = "Database Error: " . $e->getMessage();
    }
} else {
    $error_fatal = "ID Surat tidak valid atau tidak diberikan.";
}

if (isset($_GET['gagal'])) {
    $message = '<div class="alert alert-danger">' . htmlspecialchars($_GET['gagal']) . '</div>';
}

require_once __DIR__ . '/../../templates/header.php';

// Jika ada error fatal (misal ID tidak ditemukan), tampilkan pesan dan hentikan
if (isset($error_fatal)) {
    echo '<div class="container-fluid px-4"><div class="alert alert-danger mt-4">' . $error_fatal . '</div></div>';
    require_once __DIR__ . '/../../templates/footer.php';
    exit;
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Data Surat Masuk</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item"><a href="/ams/transaksi_surat/surat_masuk/screen_surat_masuk.php">Surat Masuk</a></li>
        <li class="breadcrumb-item active">Edit Data</li>
    </ol>

    <div class="card">
        <div class="card-header bg-warning">
            <i class="bi bi-pencil-square me-1"></i>
            <b>Formulir Edit Data</b>
        </div>
        <div class="card-body">
            
            <?php if(!empty($message)) echo $message; ?>

            <form action="edit_surat_masuk.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_surat" value="<?= htmlspecialchars($surat[$primary_key_column]) ?>">
                <input type="hidden" name="nama_file_lama" value="<?= htmlspecialchars($surat['nama_file'] ?? '') ?>">

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
                            <label for="asal_surat" class="form-label">Asal Surat</label>
                            <input type="text" class="form-control" id="asal_surat" name="asal_surat" value="<?= htmlspecialchars($surat['asal_surat']) ?>" required>
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
                            <input type="text" class="form-control" id="kode_klasifikasi" name="kode_klasifikasi" value="<?= htmlspecialchars($surat['kode_klasifikasi'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="indeks_berkas" class="form-label">Indeks Berkas</label>
                            <input type="text" class="form-control" id="indeks_berkas" name="indeks_berkas" value="<?= htmlspecialchars($surat['indeks_berkas'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?= htmlspecialchars($surat['keterangan'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="file_surat" class="form-label">Upload File Baru (Opsional)</label>
                            <input class="form-control" type="file" id="file_surat" name="file_surat" accept=".jpg, .jpeg, .png, .doc, .docx, .pdf">
                            <?php if (!empty($surat['nama_file'])): ?>
                                <div class="form-text">
                                    File saat ini: 
                                    <a href="/ams/transaksi_surat/surat_masuk/file_masuk/<?= htmlspecialchars($surat['nama_file']) ?>" target="_blank"><?= htmlspecialchars($surat['nama_file']) ?></a>.
                                    <br>Kosongkan jika tidak ingin mengubah file.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy-fill me-1"></i> Simpan Perubahan
                    </button>
                    <a href="/ams/transaksi_surat/surat_masuk/screen_surat_masuk.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../templates/footer.php';
?>