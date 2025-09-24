<?php
require_once __DIR__ . '/../templates/header.php';

// Proteksi halaman: hanya super admin yang bisa akses
if ($_SESSION['role'] !== 'super_admin') {
    echo '<div class="alert alert-danger">Akses ditolak. Anda bukan Super Admin.</div>';
    require_once __DIR__ . '/../templates/footer.php';
    exit();
}

$success_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_lengkap'];
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    try {
        $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, username, password, role) VALUES (:nama, :user, :pass, :role)");
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':user', $user);
        $stmt->bindParam(':pass', $pass);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        $success_message = "User baru berhasil ditambahkan!";
    } catch (PDOException $e) {
        // Cek jika error karena username duplikat
        if ($e->errorInfo[1] == 1062) {
            $success_message = "Gagal: Username sudah ada!";
        } else {
            $success_message = "Error: " . $e->getMessage();
        }
    }
}
?>

<h3>Manajemen User</h3>
<p>Halaman ini digunakan untuk menambah user dengan role Admin atau Super Admin.</p>

<?php if ($success_message): ?>
    <div class="alert alert-info"><?= $success_message; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        Formulir Tambah User
    </div>
    <div class="card-body">
        <form method="post">
            <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" name="nama_lengkap" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="admin">Admin</option>
                    <option value="super_admin">Super Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>