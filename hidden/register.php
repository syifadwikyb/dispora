<?php
// 1. Hubungkan ke database
require_once __DIR__ . '/../config/database.php';

// Variabel untuk menampung pesan feedback
$message = '';

// 2. Cek jika form sudah disubmit (method-nya POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil data dari form
    $nama_lengkap = $_POST['nama_lengkap'];
    $username     = $_POST['username'];
    $password     = $_POST['password'];
    $role         = $_POST['role'];

    // Validasi dasar agar tidak ada yang kosong
    if (empty($nama_lengkap) || empty($username) || empty($password) || empty($role)) {
        $message = '<div class="alert alert-danger">Semua field wajib diisi!</div>';
    } else {
        // PENTING: Enkripsi password sebelum disimpan ke database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Cek dulu apakah username sudah ada di database
        $stmt_check = $conn->prepare("SELECT id_user FROM users WHERE username = :username");
        $stmt_check->bindParam(':username', $username);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            // Jika username sudah ada, beri pesan error
            $message = '<div class="alert alert-danger">Registrasi Gagal! Username sudah digunakan.</div>';
        } else {
            // Jika aman, masukkan data user baru ke tabel 'users'
            try {
                $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, username, password, role) VALUES (:nama, :user, :pass, :role)");
                $stmt->bindParam(':nama', $nama_lengkap);
                $stmt->bindParam(':user', $username);
                $stmt->bindParam(':pass', $hashed_password);
                $stmt->bindParam(':role', $role);
                $stmt->execute();

                $message = '<div class="alert alert-success">Registrasi Berhasil! Silakan login.</div>';
            } catch (PDOException $e) {
                $message = '<div class="alert alert-danger">Terjadi error: ' . $e->getMessage() . '</div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun Baru</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
      .register-form { max-width: 500px; margin: 5% auto; padding: 2rem; box-shadow: 0 0 10px rgba(0,0,0,.1); }
    </style>
</head>
<body class="bg-light">

<div class="register-form">
    <h2 class="text-center mb-4">Registrasi Akun Baru</h2>

    <?php if (!empty($message)) { echo $message; } ?>

    <form method="POST" action="register.php">
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
            <select name="role" class="form-select" required>
                <option value="" disabled selected>-- Pilih Role --</option>
                <option value="admin">Admin</option>
                <option value="super_admin">Super Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Daftar</button>
        <div class="text-center mt-3">
            <a href="../login.php">Sudah punya akun? Login di sini</a>
        </div>
    </form>
</div>

</body>
</html>