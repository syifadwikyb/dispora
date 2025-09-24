<?php require_once __DIR__ . '/templates/header.php'; ?>

<div class="p-5 mb-4 bg-light rounded-3">
  <div class="container-fluid py-5">
    <h1 class="display-5 fw-bold">Selamat Datang, <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>!</h1>
    <p class="col-md-8 fs-4">Anda telah berhasil login ke Sistem Manajemen Surat sebagai <strong><?= ucfirst($_SESSION['role']); ?></strong>.</p>
  </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>