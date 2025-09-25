<?php
// Aktifkan error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../config/database.php';

// Ambil ID dari URL, pastikan valid
$id = $_GET['id'] ?? null;
if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    header("Location: screen_referensi.php?gagal=ID referensi tidak valid!");
    exit;
}

// --- LOGIKA UNTUK PROSES UPDATE DATA (Method POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id_post = $_POST['id'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $uraian = $_POST['uraian'];

    // Validasi dasar
    if (empty($kode) || empty($nama)) {
        header("Location: edit_referensi.php?id={$id_post}&gagal=Kode dan Nama wajib diisi!");
        exit;
    }

    // Proses update ke database
    try {
        $sql = "UPDATE klasifikasi_surat SET kode = :kode, nama = :nama, uraian = :uraian WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':kode', $kode);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':uraian', $uraian);
        $stmt->bindParam(':id', $id_post, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: screen_referensi.php?sukses=Data klasifikasi berhasil diperbarui!");
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $gagal = "Error: Kode '{$kode}' sudah digunakan oleh data lain.";
        } else {
            $gagal = "Database Error: " . $e->getMessage();
        }
        header("Location: edit_referensi.php?id={$id_post}&gagal=" . urlencode($gagal));
        exit;
    }
}

// --- LOGIKA UNTUK MENGAMBIL DATA YANG AKAN DIEDIT (Method GET) ---
try {
    $sql = "SELECT * FROM klasifikasi_surat WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $klasifikasi = $stmt->fetch(PDO::FETCH_ASSOC);

    // Jika data tidak ditemukan
    if (!$klasifikasi) {
        header("Location: screen_referensi.php?gagal=Data tidak ditemukan!");
        exit;
    }
} catch (PDOException $e) {
    // Redirect dengan pesan error jika query gagal
    header("Location: screen_referensi.php?gagal=" . urlencode("Database Error: " . $e->getMessage()));
    exit;
}

// Setelah semua logika selesai, baru panggil header.php
require_once __DIR__ . '/../templates/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Data Klasifikasi</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item"><a href="/ams/referensi/screen_referensi.php">Klasifikasi Surat</a></li>
        <li class="breadcrumb-item active">Edit Data</li>
    </ol>

    <div class="card">
        <div class="card-header bg-warning">
            <i class="bi bi-pencil-square me-1"></i>
            Formulir Edit Data Klasifikasi
        </div>
        <div class="card-body">
            
            <?php
            if (isset($_GET['gagal'])) {
                echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_GET['gagal']) . '</div>';
            }
            ?>

            <form action="edit_referensi.php?id=<?= htmlspecialchars($klasifikasi['id']) ?>" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($klasifikasi['id']) ?>">

                <div class="mb-3">
                    <label for="kode" class="form-label">Kode</label>
                    <input type="text" class="form-control" id="kode" name="kode" value="<?= htmlspecialchars($klasifikasi['kode']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($klasifikasi['nama']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="uraian" class="form-label">Uraian (Opsional)</label>
                    <textarea class="form-control" id="uraian" name="uraian" rows="3"><?= htmlspecialchars($klasifikasi['uraian']) ?></textarea>
                </div>

                <hr>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-floppy-fill me-1"></i> Update
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