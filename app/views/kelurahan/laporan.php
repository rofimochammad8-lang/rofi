<?php
// ============================================
// FILE: app/views/kelurahan/laporan.php
// Fungsi: Kelola & kirim laporan ke kader
// ============================================
require_once ROOT . '/app/views/layouts/header.php';
?>

<div class="sidebar">
    <?php require_once ROOT . '/app/views/layouts/sidebar.php'; ?>
</div>

<div class="main-content">

    <div class="topbar">
        <h5><i class="bi bi-file-earmark-plus me-2"></i>Kelola Laporan</h5>
        <span class="date"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?></span>
    </div>

    <div class="content-area">

        <!-- Notifikasi -->
        <?php if (isset($_GET['msg'])): ?>
            <?php
            $alerts = [
                'kirim_sukses'  => ['success', 'Laporan berhasil dikirim ke Kader Posyandu!'],
                'hapus_sukses'  => ['success', 'Laporan berhasil dihapus!'],
                'gagal'         => ['danger',  'Terjadi kesalahan, coba lagi!'],
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

        <div class="row g-4">

            <!-- Form Buat Laporan -->
            <div class="col-md-4">
                <div class="card-table">
                    <h6><i class="bi bi-send me-2"></i>Buat & Kirim Laporan</h6>
                    <hr>

                    <!-- Tombol Generate Otomatis -->
                    <div class="mb-3">
                        <button type="button" class="btn w-100 mb-2"
                                style="background:#e8f0fe;color:#1a73e8;border-radius:8px;font-size:13px;"
                                data-bs-toggle="modal" data-bs-target="#modalGenerate">
                            <i class="bi bi-magic me-2"></i>Generate Laporan Otomatis
                        </button>
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Generate otomatis berdasarkan data stunting yang sudah disetujui
                        </small>
                    </div>

                    <hr>
                    <p style="font-size:13px;color:#888;">— atau buat manual —</p>

                    <form method="POST" action="index.php?page=kelurahan&act=simpan_laporan">

                        <div class="mb-3">
                            <label class="form-label">Judul Laporan</label>
                            <input type="text" name="judul" class="form-control"
                                   placeholder="Contoh: Laporan Stunting Triwulan I" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Posyandu Tujuan</label>
                            <select name="id_posyandu" class="form-control" required>
                                <option value="">-- Pilih Posyandu --</option>
                                <?php while ($p = mysqli_fetch_assoc($data_posyandu)): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['nama_posyandu']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tahun</label>
                            <select name="id_tahun" class="form-control" required>
                                <option value="">-- Pilih Tahun --</option>
                                <?php while ($t = mysqli_fetch_assoc($data_tahun)): ?>
                                <option value="<?= $t['id'] ?>"><?= $t['tahun'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Isi Laporan / Penanganan</label>
                            <textarea name="isi" class="form-control" rows="5"
                                      placeholder="Tuliskan isi laporan dan instruksi penanganan untuk kader..."
                                      required></textarea>
                        </div>

                        <button type="submit" class="btn w-100"
                                style="background:#1a6b3a;color:#fff;border-radius:8px;font-weight:600;">
                            <i class="bi bi-send me-2"></i>Kirim ke Kader
                        </button>

                    </form>
                </div>
            </div>

            <!-- Tabel Laporan -->
            <div class="col-md-8">
                <div class="card-table">
                    <h6><i class="bi bi-journal-text me-2"></i>Riwayat Laporan Dikirim</h6>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Judul</th>
                                    <th>Posyandu</th>
                                    <th>Tahun</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
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
                                        <?php if ($row['status'] === 'dibaca'): ?>
                                            <span class="badge-status badge-normal">✓ Dibaca</span>
                                        <?php else: ?>
                                            <span class="badge-status badge-beresiko">⏳ Belum Dibaca</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <!-- Tombol Lihat -->
                                        <button class="btn btn-sm mb-1"
                                                style="background:#e8f0fe;color:#1a73e8;border-radius:6px;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalLihat"
                                                data-judul="<?= htmlspecialchars($row['judul']) ?>"
                                                data-isi="<?= htmlspecialchars($row['isi']) ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <!-- Tombol Cetak -->
                                        <a href="index.php?page=kelurahan&act=cetak&id=<?= $row['id'] ?>"
                                           target="_blank"
                                           class="btn btn-sm mb-1"
                                           style="background:#e8f8ee;color:#1a6b3a;border-radius:6px;">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        <!-- Tombol Hapus -->
                                        <a href="index.php?page=kelurahan&act=hapus_laporan&id=<?= $row['id'] ?>"
                                           class="btn btn-sm"
                                           style="background:#ffeaea;color:#c0392b;border-radius:6px;"
                                           onclick="return confirm('Yakin hapus laporan ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                        Belum ada laporan dikirim
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Lihat Isi Laporan -->
<div class="modal fade" id="modalLihat" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header" style="background:#1a6b3a;border-radius:12px 12px 0 0;">
                <h6 class="modal-title text-white" id="lihatJudul"></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="font-size:14px;line-height:1.8;" id="lihatIsi"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm"
                        style="background:#f0f0f0;color:#555;border-radius:8px;"
                        data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Generate Otomatis -->
<div class="modal fade" id="modalGenerate" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px;">
            <div class="modal-header" style="background:#1a73e8;border-radius:12px 12px 0 0;">
                <h6 class="modal-title text-white">
                    <i class="bi bi-magic me-2"></i>Generate Laporan Otomatis
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?page=kelurahan&act=generate_laporan">
                <div class="modal-body">
                    <p style="font-size:14px;color:#555;">
                        Sistem akan otomatis membuat isi laporan berdasarkan data stunting
                        yang sudah disetujui untuk posyandu dan tahun yang dipilih.
                    </p>
                    <div class="mb-3">
                        <label class="form-label">Pilih Posyandu</label>
                        <select name="id_posyandu" class="form-control" required>
                            <option value="">-- Pilih Posyandu --</option>
                            <?php
                            // Reset pointer posyandu
                            mysqli_data_seek($data_posyandu, 0);
                            while ($p = mysqli_fetch_assoc($data_posyandu)):
                            ?>
                            <option value="<?= $p['id'] ?>">
                                <?= htmlspecialchars($p['nama_posyandu']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Tahun</label>
                        <select name="id_tahun" class="form-control" required>
                            <option value="">-- Pilih Tahun --</option>
                            <?php
                            mysqli_data_seek($data_tahun, 0);
                            while ($t = mysqli_fetch_assoc($data_tahun)):
                            ?>
                            <option value="<?= $t['id'] ?>"><?= $t['tahun'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm"
                            style="background:#f0f0f0;color:#555;border-radius:8px;"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm"
                            style="background:#1a73e8;color:#fff;border-radius:8px;">
                        <i class="bi bi-magic me-1"></i>Generate & Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('modalLihat').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('lihatJudul').textContent = btn.getAttribute('data-judul');
    document.getElementById('lihatIsi').textContent   = btn.getAttribute('data-isi');
});
</script>

<?php require_once ROOT . '/app/views/layouts/footer.php'; ?>