<?php
// 1. Aktifkan Error Reporting untuk mempermudah debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Panggil session_start() jika diperlukan (misal untuk hak akses nanti)
session_start();

// Sertakan file header dan koneksi database
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../config/database.php';

$message = ''; // Variabel untuk menyimpan pesan notifikasi

// ===================================================================
// LOGIKA PROSES FORM (UPDATE DATA)
// ===================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil semua data dari form
    $nama_instansi = $_POST['nama_instansi'];
    $pimpinan = $_POST['pimpinan'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];
    $email = $_POST['email'];
    $website = $_POST['website'];
    $logo_sekarang = $_POST['logo_lama']; // Ambil nama logo yang sudah ada dari hidden input

    // --- Logika Upload Logo Baru (hanya jika user memilih file baru) ---
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0 && !empty($_FILES['logo']['tmp_name'])) {
        $file = $_FILES['logo'];

        // Validasi tipe file
        $allowed_types = ['image/jpeg', 'image/png'];
        if (!in_array($file['type'], $allowed_types)) {
            $message = '<div class="alert alert-danger">Error: Format file logo tidak diizinkan. Hanya JPG dan PNG.</div>';
        }
        // Validasi ukuran file (misal maks 1MB)
        else if ($file['size'] > 1 * 1024 * 1024) {
            $message = '<div class="alert alert-danger">Error: Ukuran file logo melebihi 1 MB.</div>';
        } else {
            // Jika validasi lolos, proses upload
            $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/ams/assets/images/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $ekstensi_file = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $nama_file_final = "logo_instansi." . $ekstensi_file; // Gunakan nama file yang konsisten
            $target_file = $target_dir . $nama_file_final;

            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $logo_sekarang = $nama_file_final; // Update nama logo dengan file yang baru
            } else {
                $message = '<div class="alert alert-danger">Error: Gagal memindahkan file logo. Periksa izin folder.</div>';
            }
        }
    }

    // --- Proses UPDATE ke Database (hanya jika tidak ada error dari upload) ---
    if (empty($message)) {
        try {
            $sql = "UPDATE instansi SET 
                        nama_instansi = :nama_instansi, pimpinan = :pimpinan, alamat = :alamat, 
                        telepon = :telepon, email = :email, website = :website, logo = :logo 
                    WHERE id = 1"; // Selalu update baris dengan id = 1

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nama_instansi', $nama_instansi);
            $stmt->bindParam(':pimpinan', $pimpinan);
            $stmt->bindParam(':alamat', $alamat);
            $stmt->bindParam(':telepon', $telepon);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':website', $website);
            $stmt->bindParam(':logo', $logo_sekarang);
            $stmt->execute();

            $message = '<div class="alert alert-success">Pengaturan instansi berhasil diperbarui!</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Database Error: ' . $e->getMessage() . '</div>';
        }
    }
}

// ===================================================================
// LOGIKA MENGAMBIL DATA UNTUK DITAMPILKAN DI FORM
// ===================================================================
try {
    $stmt = $conn->prepare("SELECT * FROM instansi WHERE id = 1");
    $stmt->execute();
    $instansi = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$instansi) {
        // Fallback jika baris data terhapus, meskipun seharusnya tidak terjadi
        $instansi = [];
        $message = '<div class="alert alert-warning">Data instansi tidak ditemukan. Silakan isi dan simpan untuk membuat data awal.</div>';
    }
} catch (PDOException $e) {
    die("Gagal mengambil data instansi: " . $e->getMessage());
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Pengaturan Instansi</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item active">Pengaturan Instansi</li>
    </ol>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-building-fill-gear me-1"></i>
            <b>Formulir Pengaturan Instansi</b>
        </div>
        <div class="card-body">

            <?php if (!empty($message)) echo $message; ?>

            <form action="instansi_pengaturan.php" method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_instansi" class="form-label">Nama Instansi</label>
                            <input type="text" class="form-control" id="nama_instansi" name="nama_instansi" value="<?= htmlspecialchars($instansi['nama_instansi'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="pimpinan" class="form-label">Pimpinan</label>
                            <input type="text" class="form-control" id="pimpinan" name="pimpinan" value="<?= htmlspecialchars($instansi['pimpinan'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= htmlspecialchars($instansi['alamat'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="telepon" class="form-label">Telepon</label>
                            <input type="text" class="form-control" id="telepon" name="telepon" value="<?= htmlspecialchars($instansi['telepon'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($instansi['email'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="text" class="form-control" id="website" name="website" value="<?= htmlspecialchars($instansi['website'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="col-12">
                            <div class="row align-items-start">
                                <div class="col-md-8 col-lg-9">
                                    <label for="logo" class="form-label">Ganti Logo Instansi</label>
                                    <input class="form-control" type="file" id="logo" name="logo" accept="image/png, image/jpeg">
                                    <div class="form-text">Kosongkan jika tidak ingin mengubah logo. Format: JPG, PNG. Ukuran maks: 1MB.</div>
                                    <input type="hidden" name="logo_lama" value="<?= htmlspecialchars($instansi['logo'] ?? '') ?>">
                                </div>

                                <div class="col-md-4 col-lg-3">
                                    <?php if (!empty($instansi['logo'])): ?>
                                        <strong>Logo Saat Ini:</strong><br>
                                        <img src="/ams/assets/images/<?= htmlspecialchars($instansi['logo']) ?>?v=<?= time() ?>"
                                            alt="Logo Instansi"
                                            class="img-thumbnail mt-2"
                                            style="width: 150px; height: 150px; object-fit: cover;">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-floppy-fill me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../templates/footer.php';
?>