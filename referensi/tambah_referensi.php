<?php
// Aktifkan error reporting untuk development
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Panggil session_start() di awal jika Anda menggunakannya
session_start(); 

// Sertakan HANYA file yang dibutuhkan untuk logika (koneksi database)
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Ambil data dari form
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $uraian = $_POST['uraian'];

    // 2. Validasi dasar (pastikan kode dan nama tidak kosong)
    if (empty($kode) || empty($nama)) {
        header("Location: tambah_referensi.php?gagal=Kode dan Nama wajib diisi!");
        exit;
    }

    try {
        $sql = "INSERT INTO klasifikasi_surat (kode, nama, uraian) VALUES (:kode, :nama, :uraian)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':kode', $kode);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':uraian', $uraian);

        $stmt->execute();

        header("Location: screen_referensi.php?sukses=Data klasifikasi baru berhasil disimpan!");
        exit;

    } catch (PDOException $e) {        
        if ($e->getCode() == 23000) {
            $gagal = "Error: Kode '{$kode}' sudah ada di database. Silakan gunakan kode lain.";
        } else {
            $gagal = "Database Error: " . $e->getMessage();
        }        
        header("Location: tambah_referensi.php?gagal=" . urlencode($gagal));
        exit;
    }
}

require_once __DIR__ . '/../templates/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Data Klasifikasi</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item"><a href="/ams/referensi/screen_referensi.php">Klasifikasi Surat</a></li>
        <li class="breadcrumb-item active">Tambah Data</li>
    </ol>

    <div class="card">
        <div class="card-header bg-success text-white">
            <i class="bi bi-plus-circle-fill me-1"></i>
            Formulir Tambah Data Klasifikasi
        </div>
        <div class="card-body">
            
            <?php
            // Tampilkan notifikasi jika ada pesan 'gagal' dari URL
            if (isset($_GET['gagal'])) {
                echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_GET['gagal']) . '</div>';
            }
            ?>

            <form action="tambah_referensi.php" method="POST">
                <div class="mb-3">
                    <label for="kode" class="form-label">Kode</label>
                    <input type="text" class="form-control" id="kode" name="kode" placeholder="Contoh: 426.4" required>
                </div>
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Contoh: KONI" required>
                </div>
                <div class="mb-3">
                    <label for="uraian" class="form-label">Uraian (Opsional)</label>
                    <textarea class="form-control" id="uraian" name="uraian" rows="3" placeholder="Deskripsi singkat mengenai kode klasifikasi"></textarea>
                </div>

                <hr>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy-fill me-1"></i> Simpan
                    </button>
                    <a href="screen_referensi.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../templates/footer.php';
?>