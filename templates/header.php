<?php
require_once __DIR__ . '/../config/database.php';

// Cek jika user belum login, alihkan ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: /ams/login.php");
    exit();
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
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color: #343a40;">
        <div class="container-fluid">
            <a class="navbar-brand" href="/ams/index.php">
                <img src="https://via.placeholder.com/120x40.png?text=LOGO" alt="Logo">
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
                            <li><a class="dropdown-item" href="/ams/buku_agenda/surat_masuk.php">Surat Masuk</a></li>
                            <li><a class="dropdown-item" href="/ams/buku_agenda/surat_keluar.php">Surat Keluar</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="galeriDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Galeri File
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="galeriDropdown">
                            <li><a class="dropdown-item" href="/ams/galeri_file/surat_masuk.php">Surat Masuk</a></li>
                            <li><a class="dropdown-item" href="/ams/galeri_file/surat_keluar.php">Surat Keluar</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="/ams/pages/referensi.php">Referensi</a></li>
                    <li class="nav-item"><a class="nav-link" href="/ams/pages/pengaturan.php">Pengaturan</a></li>
                </ul>
                <div class="d-flex">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                            <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <?php if ($_SESSION['role'] === 'super_admin'): ?>
                                <li><a class="dropdown-item" href="/ams/admin/user_management.php">Manajemen User</a></li>
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
    </nav>

    <main class="container mt-4">