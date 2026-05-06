<?php
require_once ROOT . '/app/views/layouts/header.php';
$bulan_list = [
    1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'Mei', 6=>'Jun',
    7=>'Jul', 8=>'Ags', 9=>'Sep', 10=>'Okt', 11=>'Nov', 12=>'Des'
];
?>

<div class="sidebar">
    <?php require_once ROOT . '/app/views/layouts/sidebar.php'; ?>
</div>

<div class="main-content">

    <div class="topbar">
        <h5><i class="bi bi-speedometer2 me-2"></i>Dashboard KPM</h5>
        <span class="date"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?></span>
    </div>

    <div class="content-area">

        <!-- Statistik -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-info">
                        <div class="number"><?= $total_balita ?></div>
                        <div class="label">Total Balita</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon yellow"><i class="bi bi-hourglass-split"></i></div>
                    <div class="stat-info">
                        <div class="number"><?= $total_pending ?></div>
                        <div class="label">Belum Diverifikasi</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-patch-check"></i></div>
                    <div class="stat-info">
                        <div class="number"><?= $total_terverifikasi ?></div>
                        <div class="label">Terverifikasi</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifikasi -->
        <?php if ($total_pending > 0): ?>
        <div class="alert mb-4"
             style="background:#fff8e1;border:1px solid #ffe082;border-radius:10px;color:#b8860b;">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            Ada <strong><?= $total_pending ?> data balita</strong> belum diverifikasi.
            <a href="index.php?page=kpm&act=verifikasi" class="ms-2 fw-bold" style="color:#b8860b;">
                Verifikasi Sekarang →
            </a>
        </div>
        <?php endif; ?>

        <!-- Tabel Data Belum Diverifikasi -->
        <div class="card-table">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6><i class="bi bi-hourglass me-2"></i>Data Terbaru Belum Diverifikasi</h6>
                <a href="index.php?page=kpm&act=verifikasi"
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
                            <th>Posyandu</th>
                            <th>JK</th>
                            <th>Status Gizi</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if ($data_pending && mysqli_num_rows($data_pending) > 0):
                            while ($row = mysqli_fetch_assoc($data_pending)):
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
                            <td><?= htmlspecialchars($row['nama_posyandu']) ?></td>
                            <td><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                            <td>
                                <span class="badge-status <?= $badge ?>">
                                    <?= ucfirst($row['status_gizi']) ?>
                                </span>
                            </td>
                            <td><?= $bulan_list[(int)$row['bulan_pencatatan']] ?? '-' ?></td>
                            <td><?= $row['tahun'] ?></td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-check-circle fs-4 d-block mb-2 text-success"></i>
                                Semua data sudah diverifikasi
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
