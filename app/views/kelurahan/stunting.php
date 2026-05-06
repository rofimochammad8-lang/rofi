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
        <h5><i class="bi bi-clipboard2-pulse me-2"></i>Kelola Data Stunting</h5>
        <span class="date"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?></span>
    </div>

    <div class="content-area">

        <?php if (isset($_GET['msg'])): ?>
        <?php
        $alerts = [
            'setujui_sukses' => ['success', 'Data berhasil disetujui!'],
            'tolak_sukses'   => ['warning', 'Data dikembalikan ke KPM!'],
            'gagal'          => ['danger',  'Terjadi kesalahan, coba lagi!'],
        ];
        $msg = $_GET['msg'];
        if (isset($alerts[$msg])):
            [$type, $text] = $alerts[$msg];
        ?>
        <div class="alert alert-<?= $type ?> alert-dismissible fade show"
             style="border-radius:10px;font-size:14px;">
            <i class="bi bi-<?= $type === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
            <?= $text ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <div class="card-table">
            <h6 class="mb-3"><i class="bi bi-clipboard2-pulse me-2"></i>Data Balita</h6>
            <div class="table-responsive">
                <table class="table table-hover" style="font-size:13px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Bayi</th>
                            <th>Nama Ortu</th>
                            <th>Posyandu Pengirim</th>
                            <th>Tgl Lahir</th>
                            <th>Umur</th>
                            <th>JK</th>
                            <th>BB</th>
                            <th>TB</th>
                            <th>Status Gizi</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (mysqli_num_rows($data_stunting) > 0):
                            while ($row = mysqli_fetch_assoc($data_stunting)):
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
                            <td>
                                <span style="font-weight:600;color:#1a6b3a;">
                                    <?= htmlspecialchars($row['nama_posyandu']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_lahir'])) ?></td>
                            <td><?= $row['umur_bulan'] ?> bln</td>
                            <td><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                            <td><?= $row['berat_badan'] ?> kg</td>
                            <td><?= $row['tinggi_badan'] ?> cm</td>
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
                            <td colspan="12" class="text-center text-muted py-4">
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
var jumlahSekarang = <?= mysqli_num_rows($data_stunting) ?>;
setInterval(function() {
    fetch('index.php?page=kelurahan&act=cek_stunting')
        .then(res => res.json())
        .then(data => {
            if (data.total !== jumlahSekarang) {
                location.reload();
            }
        })
        .catch(err => {});
}, 5000);
</script>

<?php require_once ROOT . '/app/views/layouts/footer.php'; ?>
