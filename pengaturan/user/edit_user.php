<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../../config/database.php';

$id = $_GET['id'] ?? null;
if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    header("Location: screen_user.php?gagal=ID user tidak valid!");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_post = $_POST['id'];
    $username = $_POST['username'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $level = $_POST['level'];

    if (empty($username) || empty($nama_lengkap) || empty($level)) {
        header("Location: edit_user.php?id={$id_post}&gagal=Semua field wajib diisi!");
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM pengguna WHERE username = ? AND id != ?");
    $stmt->execute([$username, $id_post]);
    if ($stmt->fetch()) {
        header("Location: edit_user.php?id={$id_post}&gagal=Username sudah digunakan oleh user lain!");
        exit;
    }

    try {
        $sql = "UPDATE pengguna SET username = ?, nama_lengkap = ?, level = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $nama_lengkap, $level, $id_post]);
        header("Location: screen_user.php?sukses=Data user berhasil diperbarui!");
        exit;
    } catch (PDOException $e) {
        header("Location: edit_user.php?id={$id_post}&gagal=" . urlencode($e->getMessage()));
        exit;
    }
}

try {
    $stmt = $conn->prepare("SELECT username, nama_lengkap, level FROM pengguna WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: screen_user.php?gagal=User tidak ditemukan!");
        exit;
    }
} catch (PDOException $e) {
    header("Location: screen_user.php?gagal=" . urlencode($e->getMessage()));
    exit;
}

require_once __DIR__ . '/../../templates/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit User</h1>
    <ol class="breadcrumb mb-3">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item"><a href="screen_user.php">Manajemen User</a></li>
        <li class="breadcrumb-item active">Edit User</li>
    </ol>

    <?php if (isset($_GET['gagal'])): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($_GET['gagal']) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-warning text-white"><i class="bi bi-pencil-square me-1"></i> <b>Formulir Edit User</b></div>
        <div class="card-body">

            <form action="edit_user.php?id=<?= htmlspecialchars($id) ?>" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

                <div class.php="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="level" class="form-label">Tipe User (Role)</label>
                    <select class="form-select" id="level" name="level" required>
                        <option value="Administrator" <?= ($user['level'] == 'Administrator') ? 'selected' : '' ?>>Administrator</option>
                    </select>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-floppy-fill me-1"></i> Update</button>
                    <a href="screen_user.php" class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../../templates/footer.php';
?>