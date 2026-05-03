<?php
require_once ROOT . '/app/views/layouts/header.php';
?>

<div class="sidebar">
    <?php require_once ROOT . '/app/views/layouts/sidebar.php'; ?>
</div>

<div class="main-content">

    <div class="topbar">
        <h5><i class="bi bi-speedometer2 me-2"></i>Dashboard Kader Posyandu</h5>
        <span class="date"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?></span>
    </div>

    <div class="content-area">

        <!-- Statistik -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-info">
                        <div class="number"><?= $total_balita ?? 0 ?></div>
                        <div class="label">Total Balita</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-emoji-smile"></i></div>
                    <div class="stat-info">
                        <div class="number"><?= $total_normal ?? 0 ?></div>
                        <div class="label">Normal</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon yellow"><i class="bi bi-exclamation-triangle"></i></div>
                    <div class="stat-info">
                        <div class="number"><?= $total_beresiko ?? 0 ?></div>
                        <div class="label">Berisiko</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon red"><i class="bi bi-heartbreak"></i></div>
                    <div class="stat-info">
                        <div class="number"><?= $total_stunting ?? 0 ?></div>
                        <div class="label">Stunting</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifikasi laporan -->
        <?php if (!empty($total_laporan) && $total_laporan > 0): ?>
        <div class="alert mb-4"
             style="background:#e8f8ee;border:1px solid #b2dfcc;border-radius:10px;color:#1a6b3a;">
            <i class="bi bi-bell-fill me-2"></i>
            Ada <strong><?= $total_laporan ?> laporan baru</strong> dari kelurahan.
            <a href="index.php?page=kader&act=laporan" class="ms-2 fw-bold" style="color:#1a6b3a;">
                Lihat →
            </a>
        </div>
        <?php endif; ?>

        <!-- Tabel Balita Terbaru -->
        <div class="card-table">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6><i class="bi bi-clock-history me-2"></i>Balita Terbaru Diinput</h6>
                <a href="index.php?page=kader&act=balita"
                   class="btn btn-sm"
                   style="background:#1a6b3a;color:#fff;border-radius:8px;font-size:13px;">
                    Lihat Semua
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover" style="font-size:13px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Bayi</th>
                            <th>Nama Ortu</th>
                            <th>Jenis Kelamin</th>
                            <th>Umur</th>
                            <th>Status Gizi</th>
                            <th>Tahun</th>
                            <th>Tgl Input</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (!empty($balita_terbaru) && mysqli_num_rows($balita_terbaru) > 0):
                            while ($row = mysqli_fetch_assoc($balita_terbaru)):
                                $badge = match($row['status_gizi']) {
                                    'normal'   => 'badge-normal',
                                    'beresiko' => 'badge-beresiko',
                                    'stunting' => 'badge-stunting',
                                    default    => ''
                                };
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($row['nama_bayi']) ?></strong></td>
                            <td><?= htmlspecialchars($row['nama_ortu']) ?></td>
                            <td><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                            <td><?= $row['umur_bulan'] ?> bln</td>
                            <td>
                                <span class="badge-status <?= $badge ?>">
                                    <?= ucfirst($row['status_gizi']) ?>
                                </span>
                            </td>
                            <td><?= $row['tahun'] ?></td>
                            <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                Belum ada data balita
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php require_once ROOT . '/app/views/layouts/footer.php'; ?>