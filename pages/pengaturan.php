<?php
require_once __DIR__ . '/../templates/header.php';

// Variabel untuk menampung pesan feedback
$message = '';

// KONDISI PALING PENTING ADA DI SINI
// Kode di dalam blok ini HANYA akan berjalan jika:
// 1. Tombol bernama 'submit_tambah_user' ditekan, DAN
// 2. Role user yang sedang login adalah 'super_admin'
if (isset($_POST['submit_tambah_user']) && $_SESSION['role'] === 'super_admin') {
    
    // Ambil data dari form
    $nama = $_POST['nama_lengkap'];
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Cek apakah username sudah ada
    $stmt_check = $conn->prepare("SELECT id_user FROM users WHERE username = :user");
    $stmt_check->bindParam(':user', $user);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        $message = '<div class="alert alert-danger">Gagal! Username sudah digunakan.</div>';
    } else {
        // Jika username belum ada, masukkan data ke database
        try {
            $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, username, password, role) VALUES (:nama, :user, :pass, :role)");
            $stmt->bindParam(':nama', $nama);
            $stmt->bindParam(':user', $user);
            $stmt->bindParam(':pass', $pass);
            $stmt->bindParam(':role', $role);
            $stmt->execute();
            $message = '<div class="alert alert-success">User baru berhasil ditambahkan!</div>';
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<h2 class="mb-4">Halaman Pengaturan</h2>

<?= $message; ?>

<?php // Bagian ini hanya akan tampil jika yang login adalah SUPER ADMIN ?>
<?php if ($_SESSION['role'] === 'super_admin'): ?>
<div class="card mb-4">
  <div class="card-header">
    <strong>Manajemen User (Registrasi Akun Baru)</strong>
  </div>
  <div class="card-body">
    <p>Fitur ini digunakan untuk menambahkan pengguna baru ke dalam sistem.</p>
    <form method="POST" action="pengaturan.php">
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
      <button type="submit" name="submit_tambah_user" class="btn btn-primary">Tambah User</button>
    </form>
  </div>
</div>
<?php endif; ?>


<div class="card">
  <div class="card-header">
    <strong>Sesi & Akun</strong>
  </div>
  <div class="card-body">
    <p>Keluar dari sesi Anda saat ini untuk mengamankan akun.</p>
    <a href="logout.php" class="btn btn-danger">Logout Sekarang</a>
  </div>
</div>


<?php require_once __DIR__ . '/../templates/footer.php'; ?>