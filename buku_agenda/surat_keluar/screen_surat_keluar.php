<?php
// Pastikan header.php sudah memanggil session_start()
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../config/database.php';

$default_start_date = date('Y-m-01');
$default_end_date = date('Y-m-t');

$start_date = $_GET['start_date'] ?? $default_start_date;
$end_date = $_GET['end_date'] ?? $default_end_date;

// ## PERUBAHAN 1: Ganti query untuk menunjuk ke tabel 'surat_keluar' dan kolom yang sesuai
$sql = "SELECT nomor_agenda, kode_klasifikasi, isi_ringkas, tujuan, nomor_surat, tanggal_surat, tanggal_dikirim, keterangan 
        FROM surat_keluar 
        WHERE DATE(tanggal_dikirim) BETWEEN ? AND ? 
        ORDER BY tanggal_dikirim ASC";

$params = [$start_date, $end_date];
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$agendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nama_pengelola = $_SESSION['nama_lengkap'] ?? 'User';
?>

<div class="container-fluid px-4" id="main-content">
    <h1 class="mt-4">Buku Agenda Surat Keluar</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/ams/index.php">Beranda</a></li>
        <li class="breadcrumb-item active">Buku Agenda Surat Keluar</li>
    </ol>

    <div class="card mb-4" id="filter-panel">
        <div class="card-header bg-light">
            <i class="bi bi-filter me-1"></i>
            <b>Filter Laporan & Aksi</b>
        </div>
        <div class="card-body">
            <form action="" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4"><label for="start_date" class="form-label">Dari Tanggal</label><input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>"></div>
                <div class="col-md-4"><label for="end_date" class="form-label">Sampai Tanggal</label><input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>"></div>
                <div class="col-md-2"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-funnel-fill"></i> Terapkan</button></div>
                <div class="col-md-2"><button class="btn btn-success w-100" type="button" id="printBtn"><i class="bi bi-printer-fill"></i> Cetak</button></div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="bi bi-book-half me-1"></i>
            Menampilkan Data Agenda dari <b><?= date('d M Y', strtotime($start_date)) ?></b> sampai <b><?= date('d M Y', strtotime($end_date)) ?></b>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-primary text-center">
                        <tr class="align-middle">
                            <th>No.</th>
                            <th>No. Agenda</th>
                            <th>Kode</th>
                            <th>Isi Ringkas</th>
                            <th>Tujuan Surat</th>
                            <th>Nomor Surat</th>
                            <th>Tanggal Surat</th>
                            <th>Pengelola</th>
                            <th>Tanggal Paraf</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <?php if (empty($agendas)): ?>
                            <tr>
                                <td colspan="10">Tidak ada data untuk periode yang dipilih.</td>
                            </tr>
                        <?php else: ?>
                            <?php $nomor = 1; ?>
                            <?php foreach ($agendas as $agenda): ?>
                                <tr class="align-middle">
                                    <td><?= $nomor++ ?></td>
                                    <td><?= htmlspecialchars($agenda['nomor_agenda']) ?></td>
                                    <td><?= htmlspecialchars($agenda['kode_klasifikasi']) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($agenda['isi_ringkas']) ?></td>
                                    <td><?= htmlspecialchars($agenda['tujuan']) ?></td>
                                    <td><?= htmlspecialchars($agenda['nomor_surat']) ?></td>
                                    <td><?= date('d-m-Y', strtotime($agenda['tanggal_surat'])) ?></td>
                                    <td><?= htmlspecialchars($nama_pengelola) ?></td>
                                    <td><?= date('d-m-Y', strtotime($agenda['tanggal_surat'])) ?></td>
                                    <td class="text-start"><?= htmlspecialchars($agenda['keterangan']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Skrip cetak (tidak ada perubahan)
document.getElementById('printBtn').addEventListener('click', function() {
    window.print();
});
</script>

<?php
require_once __DIR__ . '/../../templates/footer.php';
?>