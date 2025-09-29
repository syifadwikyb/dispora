<?php
session_start();

require_once __DIR__ . '/config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $login_sukses = false;

    try {
        $stmt_users = $conn->prepare("SELECT id_user, nama_lengkap, username, password, role FROM users WHERE username = ?");
        $stmt_users->execute([$username]);
        $user_from_users = $stmt_users->fetch(PDO::FETCH_ASSOC);

        if ($user_from_users && password_verify($password, $user_from_users['password'])) {
            $_SESSION['user_id'] = $user_from_users['id_user'];
            $_SESSION['nama_lengkap'] = $user_from_users['nama_lengkap'];
            $_SESSION['role'] = $user_from_users['role'];
            unset($_SESSION['level']);
            $login_sukses = true;
        } else {
            $stmt_pengguna = $conn->prepare("SELECT id, nama_lengkap, username, password, level FROM pengguna WHERE username = ?");
            $stmt_pengguna->execute([$username]);
            $user_from_pengguna = $stmt_pengguna->fetch(PDO::FETCH_ASSOC);

            if ($user_from_pengguna && password_verify($password, $user_from_pengguna['password'])) {
                $_SESSION['user_id'] = $user_from_pengguna['id'];
                $_SESSION['nama_lengkap'] = $user_from_pengguna['nama_lengkap'];
                $_SESSION['level'] = $user_from_pengguna['level'];
                unset($_SESSION['role']);
                $login_sukses = true;
            }
        }

        if ($login_sukses) {
            header("Location: /ams/index.php");
            exit;
        } else {
            $error_message = "Username atau Password salah!";
        }
    } catch (PDOException $e) {
        $error_message = "Database Error: " . $e->getMessage();
    }
}

try {
    $stmt_instansi = $conn->prepare("SELECT nama_instansi, logo FROM instansi WHERE id = 1");
    $stmt_instansi->execute();
    $instansi = $stmt_instansi->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $instansi = null;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login | Arsip Manajemen Surat (AMS)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .login-container {
            min-height: 100vh;
        }

        .login-card {
            max-width: 450px;
            padding: 1.5rem 2rem;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .1);
        }

        .login-card .logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 1rem;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #80bdff;
        }

        .btn-primary {
            padding: 10px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center login-container">
            <div class="col-md-6">
                <div class="card login-card">
                    <div class="card-body text-center">

                        <?php if ($instansi && !empty($instansi['logo'])): ?>
                            <img src="/ams/assets/images/<?= htmlspecialchars($instansi['logo']) ?>?v=<?= time() ?>" alt="Logo Instansi" class="logo">
                        <?php endif; ?>

                        <h3 class="card-title mb-1">
                            Arsip Manajemen Surat
                        </h3>
                        <p class="text-muted mb-4">
                            <?= htmlspecialchars($instansi['nama_instansi'] ?? 'Silakan login untuk melanjutkan') ?>
                        </p>

                        <?php if ($error_message): ?>
                            <div class="alert alert-danger text-start"><?= $error_message; ?></div>
                        <?php endif; ?>

                        <form method="post" action="login.php" class="text-start">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mt-3">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>