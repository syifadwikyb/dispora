<?php
// Letakkan semua logika di atas sebelum HTML
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../../config/database.php';

// Proses form jika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $nip = $_POST['nip'];
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    $level = $_POST['level'];

    // Validasi
    if (empty($nip)) { // ## PERUBAHAN 1: Validasi NIP tidak boleh kosong
        header("Location: tambah_user.php?gagal=NIP wajib diisi!");
        exit;
    }
    if ($password !== $konfirmasi_password) {
        header("Location: tambah_user.php?gagal=Password dan konfirmasi password tidak cocok!");
        exit;
    }

    // ## PERUBAHAN 2: Ganti nama tabel 'users' menjadi 'pengguna'
    // Cek apakah username sudah ada
    $stmt = $conn->prepare("SELECT id FROM pengguna WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        header("Location: tambah_user.php?gagal=Username sudah digunakan, silakan pilih yang lain!");
        exit;
    }

    // ## PERUBAHAN 3: Tambah validasi NIP tidak boleh sama
    $stmt = $conn->prepare("SELECT id FROM pengguna WHERE nip = ?");
    $stmt->execute([$nip]);
    if ($stmt->fetch()) {
        header("Location: tambah_user.php?gagal=NIP sudah terdaftar, silakan gunakan NIP lain!");
        exit;
    }

    // Hash password sebelum disimpan
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Simpan ke database
    try {
        $sql = "INSERT INTO pengguna (username, nama_lengkap, nip, password, level) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $nama_lengkap, $nip, $hashed_password, $level]);
        header("Location: screen_user.php?sukses=User baru berhasil ditambahkan!");
        exit;
    } catch (PDOException $e) {
        header("Location: tambah_user.php?gagal=" . urlencode($e->getMessage()));
        exit;
    }
}

// Mulai bagian HTML
require_once __DIR__ . '/../../templates/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah User Baru</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item"><a href="screen_user.php">Manajemen User</a></li>
        <li class="breadcrumb-item active">Tambah User</li>
    </ol>

    <div class="card">
        <div class="card-header bg-success text-white"><i class="bi bi-person-plus-fill me-1"></i> Formulir Tambah User</div>
        <div class="card-body">
            <?php if (isset($_GET['gagal'])): ?>
                <div class="alert alert-danger" role="alert"><?= htmlspecialchars($_GET['gagal']) ?></div>
            <?php endif; ?>

            <form action="tambah_user.php" method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="col-md-6">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="col-md-6">
                        <label for="nip" class="form-label">NIP</label>
                        <input type="text" class="form-control" id="nip" name="nip" required>
                    </div>
                    <div class="col-md-6">
                        <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required>
                    </div>
                    <div class="col-md-6">
                        <label for="level" class="form-label">Tipe User</label>
                        <select class="form-select bg-light" id="level" disabled>
                            <option selected>Administrator</option>
                        </select>
                        <input type="hidden" name="level" value="Administrator">
                    </div>
                </div>
                <hr>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-floppy-fill me-1"></i> Simpan</button>
                    <a href="screen_user.php" class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../templates/footer.php';
?>