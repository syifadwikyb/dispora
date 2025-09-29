<?php
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /ams/login.php");
    exit();
}

try {
    $stmt_instansi_header = $conn->prepare("SELECT nama_instansi, logo FROM instansi WHERE id = 1");
    $stmt_instansi_header->execute();
    $instansi = $stmt_instansi_header->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $instansi = ['nama_instansi' => 'AMS', 'logo' => null];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Surat</title>
    <link href="/ams/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 1.5cm;
            }

            body {
                background-color: #fff;
                font-size: 9pt;
            }

            #mainNav,
            #filter-panel,
            .breadcrumb,
            footer,
            header {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            #main-content {
                padding: 0 !important;
                margin: 0 !important;
            }

            .table {
                width: 100%;
            }

            .table th,
            .table td {
                padding: 3px 5px;
                word-wrap: break-word;
            }

            .card-header {
                border-bottom: 2px solid #000;
                padding-bottom: 10px;
                margin-bottom: 15px;
            }

            .card-header::before {
                content: "Laporan Buku Agenda Surat Masuk";
                font-size: 16pt;
                font-weight: bold;
                text-align: center;
                display: block;
                margin-bottom: 0;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color: #343a40;">
        <div class="container-fluid">
            <a class="navbar-brand" href="/ams/index.php">
                <img src="/ams/assets/images/<?= htmlspecialchars($instansi['logo']) ?>?v=<?= time() ?>"
                    alt="<?= htmlspecialchars($instansi['nama_instansi']) ?>"
                    height="30">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="/ams/index.php">Beranda</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="transaksiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Transaksi Surat
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="transaksiDropdown">
                            <li><a class="dropdown-item" href="/ams/transaksi_surat/surat_masuk/screen_surat_masuk.php">Surat Masuk</a></li>
                            <li><a class="dropdown-item" href="/ams/transaksi_surat/surat_keluar/screen_surat_keluar.php">Surat Keluar</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="agendaDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Buku Agenda
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="agendaDropdown">
                            <li><a class="dropdown-item" href="/ams/buku_agenda/surat_masuk/screen_surat_masuk.php">Surat Masuk</a></li>
                            <li><a class="dropdown-item" href="/ams/buku_agenda/surat_keluar/screen_surat_keluar.php">Surat Keluar</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="galeriDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Galeri File
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="galeriDropdown">
                            <li><a class="dropdown-item" href="/ams/galeri_file/surat_masuk/screen_surat_masuk.php">Surat Masuk</a></li>
                            <li><a class="dropdown-item" href="/ams/galeri_file/surat_keluar/screen_surat_keluar.php">Surat Keluar</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="/ams/referensi/screen_referensi.php">Referensi</a></li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="pengaturanDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Pengaturan
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="pengaturanDropdown">
                                <li><a class="dropdown-item" href="/ams/pengaturan/instansi_pengaturan.php">Instansi</a></li>
                                <li><a class="dropdown-item" href="/ams/pengaturan/backup_pengaturan.php">Backup</a></li>
                                <li><a class="dropdown-item" href="/ams/pengaturan/restore_pengaturan.php">Restore</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex">
                    <div class="d-flex">
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>
                                <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'User'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="#">Profile</a></li>

                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin'): ?>
                                    <li><a class="dropdown-item" href="/ams/pengaturan/user/screen_user.php">Manajemen User</a></li>
                                <?php endif; ?>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="/ams/logout.php">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mt-4">
</body>