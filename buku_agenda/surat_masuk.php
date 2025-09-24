<?php require_once __DIR__ . '/../templates/header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Data Surat Masuk</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
        <li class="breadcrumb-item active">Surat Masuk</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-envelope-paper-fill me-1"></i>
            Kontrol & Navigasi
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 mb-2 mb-md-0">
                    <button type="button" class="btn btn-success me-2">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Data
                    </button>
                    <button type="button" class="btn btn-secondary">
                        <i class="bi bi-box-arrow-down me-1"></i> Export Data
                    </button>
                </div>

                <div class="col-md-8">
                    <form action="" method="get">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Cari berdasarkan no. surat, asal surat..." name="search">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fs-6 fw-bold">
                    <i class="bi bi-table me-1"></i>
                    Tabel Data Surat Masuk
                </span>
                <div class="d-flex align-items-center">
                    <i class="bi bi-gear-fill me-2" title="Pengaturan Tampilan"></i>
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option selected>10</option>
                        <option value="1">25</option>
                        <option value="2">50</option>
                        <option value="3">100</option>
                    </select>
                    <span class="ms-2">data per halaman</span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>No. Agenda & Kode</th>
                            <th>Isi Ringkas & File</th>
                            <th>Asal Surat</th>
                            <th>No. Surat & Tgl. Surat</th>
                            <th style="width: 15%;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span class="fw-bold">001</span> / <span class="badge bg-secondary">B-01</span>
                            </td>
                            <td>
                                Undangan Rapat Koordinasi
                                <a href="#" class="d-block text-decoration-none" title="Lihat file">
                                    <i class="bi bi-file-earmark-pdf-fill text-danger"></i> document.pdf
                                </a>
                            </td>
                            <td>Kementerian Pendidikan & Kebudayaan</td>
                            <td>
                                123/A1/UND/IX/2025
                                <small class="d-block text-muted">16 Sep 2025</small>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning m-1" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                <button class="btn btn-sm btn-info m-1" title="Disposisi"><i class="bi bi-file-earmark-text"></i></button>
                                <button class="btn btn-sm btn-secondary m-1" title="Print"><i class="bi bi-printer"></i></button>
                                <button class="btn btn-sm btn-danger m-1" title="Delete"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="fw-bold">002</span> / <span class="badge bg-secondary">C-04</span>
                            </td>
                            <td>
                                Permohonan Data Pegawai
                                <a href="#" class="d-block text-decoration-none" title="Lihat file">
                                    <i class="bi bi-file-earmark-word-fill text-primary"></i> permohonan.docx
                                </a>
                            </td>
                            <td>Badan Kepegawaian Negara</td>
                            <td>
                                456/BKN/IX/2025
                                <small class="d-block text-muted">15 Sep 2025</small>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning m-1" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                <button class="btn btn-sm btn-info m-1" title="Disposisi"><i class="bi bi-file-earmark-text"></i></button>
                                <button class="btn btn-sm btn-secondary m-1" title="Print"><i class="bi bi-printer"></i></button>
                                <button class="btn btn-sm btn-danger m-1" title="Delete"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                        </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                    </li>
                    <li class="page-item active" aria-current="page"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>