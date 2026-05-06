<?php
require_once ROOT . '/app/views/layouts/header.php';
$bulan_list = [
    1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
    5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
    9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
];
?>

<div class="sidebar">
    <?php require_once ROOT . '/app/views/layouts/sidebar.php'; ?>
</div>

<div class="main-content">

    <div class="topbar">
        <h5><i class="bi bi-patch-check me-2"></i>Verifikasi Data Balita</h5>
        <span class="date"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?></span>
    </div>

    <div class="content-area">

        <?php if (isset($_GET['msg'])): ?>
        <?php
        $alerts = [
            'verifikasi_sukses' => ['success', 'Data berhasil diverifikasi dan dikirim ke kelurahan!'],
            'tolak_sukses'      => ['warning', 'Data dikembalikan dengan catatan!'],
            'gagal'             => ['danger',  'Terjadi kesalahan, coba lagi!'],
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
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6><i class="bi bi-hourglass me-2"></i>Data Balita Menunggu Verifikasi</h6>
                <span style="font-size:13px;color:#888;">
                    Total: <b><?= mysqli_num_rows($data_stunting) ?></b> data
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover" style="font-size:13px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Bayi</th>
                            <th>Nama Ortu</th>
                            <th>NIK Ortu</th>
                            <th>Posyandu</th>
                            <th>Tgl Lahir</th>
                            <th>Umur</th>
                            <th>JK</th>
                            <th>BB</th>
                            <th>TB</th>
                            <th>Lkr. Kepala</th>
                            <th>Lkr. Lengan</th>
                            <th>Status Gizi</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Aksi</th>
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
                            <td><?= htmlspecialchars($row['nik_ortu'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['nama_posyandu']) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_lahir'])) ?></td>
                            <td><?= $row['umur_bulan'] ?> bln</td>
                            <td><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                            <td><?= $row['berat_badan'] ?> kg</td>
                            <td><?= $row['tinggi_badan'] ?> cm</td>
                            <td><?= $row['lingkar_kepala'] ? $row['lingkar_kepala'].' cm' : '-' ?></td>
                            <td><?= $row['lingkar_lengan'] ? $row['lingkar_lengan'].' cm' : '-' ?></td>
                            <td>
                                <span class="badge-status <?= $badge ?>">
                                    <?= ucfirst($row['status_gizi']) ?>
                                </span>
                            </td>
                            <td><?= $bulan_list[(int)$row['bulan_pencatatan']] ?? '-' ?></td>
                            <td><?= $row['tahun'] ?></td>
                            <td style="white-space:nowrap;">
                                <!-- Verifikasi -->
                                <a href="index.php?page=kpm&act=setujui_verifikasi&id=<?= $row['id'] ?>"
                                   class="btn btn-sm mb-1"
                                   style="background:#e8f8ee;color:#1a6b3a;border-radius:6px;"
                                   onclick="return confirm('Verifikasi data <?= htmlspecialchars($row['nama_bayi']) ?>?')">
                                    <i class="bi bi-check-lg"></i> Verifikasi
                                </a>
                                <!-- Tolak -->
                                <button class="btn btn-sm"
                                        style="background:#ffeaea;color:#c0392b;border-radius:6px;"
                                        data-bs-toggle="modal" data-bs-target="#modalTolak"
                                        data-id="<?= $row['id'] ?>"
                                        data-nama="<?= htmlspecialchars($row['nama_bayi']) ?>">
                                    <i class="bi bi-x-lg"></i> Tolak
                                </button>
                            </td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="16" class="text-center text-muted py-4">
                                <i class="bi bi-check-circle fs-4 d-block mb-2 text-success"></i>
                                Semua data balita sudah diverifikasi
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal Tolak -->
<div class="modal fade" id="modalTolak" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header" style="background:#c0392b;border-radius:12px 12px 0 0;">
                <h6 class="modal-title text-white">Tolak Verifikasi</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?page=kpm&act=tolak_verifikasi">
                <div class="modal-body">
                    <input type="hidden" name="id" id="tolakId">
                    <p style="font-size:14px;">Data: <strong id="tolakNama"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Alasan / Catatan</label>
                        <textarea name="catatan" class="form-control" rows="3"
                                  placeholder="Tuliskan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm"
                            style="background:#f0f0f0;color:#555;border-radius:8px;"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm"
                            style="background:#c0392b;color:#fff;border-radius:8px;">
                        <i class="bi bi-x-circle me-1"></i>Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('modalTolak').addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('tolakId').value         = btn.getAttribute('data-id');
    document.getElementById('tolakNama').textContent = btn.getAttribute('data-nama');
});
</script>

<?php require_once ROOT . '/app/views/layouts/footer.php'; ?>
