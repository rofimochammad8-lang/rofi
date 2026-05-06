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
        <h5><i class="bi bi-speedometer2 me-2"></i>Dashboard Kelurahan</h5>
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
                    <div class="stat-icon red"><i class="bi bi-heartbreak"></i></div>
                    <div class="stat-info">
                        <div class="number"><?= $total_stunting ?></div>
                        <div class="label">Kasus Stunting</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-file-earmark-check"></i></div>
                    <div class="stat-info">
                        <div class="number"><?= $total_laporan ?></div>
                        <div class="label">Laporan Dikirim</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Terverifikasi -->
        <div class="card-table">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6><i class="bi bi-clipboard2-pulse me-2"></i>Data Terbaru Menunggu Persetujuan</h6>
                <a href="index.php?page=kelurahan&act=stunting"
                   class="btn btn-sm"
                   style="background:#1a6b3a;color:#fff;border-radius:8px;font-size:13px;">
                    Kelola Semua
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover" style="font-size:13px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Bayi</th>
                            <th>Nama Ortu</th>
                            <th>JK</th>
                            <th>Status</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (mysqli_num_rows($data_terverifikasi) > 0):
                            while ($row = mysqli_fetch_assoc($data_terverifikasi)):
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
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-check-circle fs-4 d-block mb-2 text-success"></i>
                                Tidak ada data menunggu persetujuan
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
// Polling setiap 5 detik - reload hanya jika ada data baru
var jumlahMenunggu = <?= $total_menunggu ?>;
setInterval(function() {
    fetch('index.php?page=kelurahan&act=cek_stunting')
        .then(res => res.json())
        .then(data => {
            if (data.total !== jumlahMenunggu) {
                location.reload();
            }
        })
        .catch(err => {});
}, 5000);
</script>

<?php require_once ROOT . '/app/views/layouts/footer.php'; ?>
