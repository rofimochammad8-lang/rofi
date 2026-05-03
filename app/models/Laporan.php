<?php
// ============================================
// FILE: app/views/kader/laporan.php
// Fungsi: Halaman laporan masuk dari kelurahan
// ============================================
require_once ROOT . '/app/views/layouts/header.php';
?>

<div class="sidebar">
    <?php require_once ROOT . '/app/views/layouts/sidebar.php'; ?>
</div>

<div class="main-content">

    <div class="topbar">
        <h5><i class="bi bi-file-earmark-text me-2"></i>Laporan Masuk dari Kelurahan</h5>
        <span class="date"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?></span>
    </div>

    <div class="content-area">
        <div class="card-table">
            <h6><i class="bi bi-inbox me-2"></i>Daftar Laporan</h6>
            <hr>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Judul Laporan</th>
                            <th>Posyandu</th>
                            <th>Tahun</th>
                            <th>Isi Laporan</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (mysqli_num_rows($data_laporan) > 0):
                            while ($row = mysqli_fetch_assoc($data_laporan)):
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($row['judul']) ?></strong></td>
                            <td><?= htmlspecialchars($row['nama_posyandu']) ?></td>
                            <td><?= $row['tahun'] ?></td>
                            <td>
                                <!-- Tombol lihat isi laporan -->
                                <button class="btn btn-sm"
                                        style="background:#e8f0fe;color:#1a73e8;border-radius:6px;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalLaporan"
                                        data-judul="<?= htmlspecialchars($row['judul']) ?>"
                                        data-isi="<?= htmlspecialchars($row['isi']) ?>">
                                    <i class="bi bi-eye"></i> Lihat
                                </button>
                            </td>
                            <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                Belum ada laporan masuk
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Isi Laporan -->
<div class="modal fade" id="modalLaporan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header" style="background:#1a6b3a;border-radius:12px 12px 0 0;">
                <h6 class="modal-title text-white" id="modalJudul"></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="font-size:14px;line-height:1.8;" id="modalIsi">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm"
                        style="background:#f0f0f0;color:#555;border-radius:8px;"
                        data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
// Isi modal dengan data laporan yang diklik
document.getElementById('modalLaporan').addEventListener('show.bs.modal', function (e) {
    const btn   = e.relatedTarget;
    const judul = btn.getAttribute('data-judul');
    const isi   = btn.getAttribute('data-isi');
    document.getElementById('modalJudul').textContent = judul;
    document.getElementById('modalIsi').textContent   = isi;
});
</script>

<?php require_once ROOT . '/app/views/layouts/footer.php'; ?>