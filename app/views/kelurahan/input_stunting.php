<?php
require_once ROOT . '/app/views/layouts/header.php';
?>

<div class="sidebar">
    <?php require_once ROOT . '/app/views/layouts/sidebar.php'; ?>
</div>

<div class="main-content">

    <div class="topbar">
        <h5><i class="bi bi-plus-circle me-2"></i>Input Data Stunting</h5>
        <span class="date"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?></span>
    </div>

    <div class="content-area">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card-table">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6><i class="bi bi-clipboard2-plus me-2"></i>Form Input Stunting</h6>
                        <a href="index.php?page=kelurahan&act=stunting"
                           class="btn btn-sm"
                           style="background:#f0f0f0;color:#555;border-radius:8px;font-size:13px;">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                    <hr>

                    <!-- Info -->
                    <div class="alert" style="background:#e8f0fe;border:1px solid #b3c8f5;
                         border-radius:8px;color:#1a73e8;font-size:13px;margin-bottom:16px;">
                        <i class="bi bi-info-circle me-2"></i>
                        Pilih balita yang akan diajukan verifikasi stunting ke KPM.
                        Data BB, TB, dan status gizi diambil otomatis dari data balita.
                    </div>

                    <form method="POST" action="index.php?page=kelurahan&act=simpan_stunting">

                        <div class="mb-3">
                            <label class="form-label">Pilih Balita</label>
                            <select name="id_balita" id="pilihBalita"
                                    class="form-control" onchange="tampilDetail()" required>
                                <option value="">-- Pilih Balita --</option>
                                <?php while ($b = mysqli_fetch_assoc($data_balita)): ?>
                                <option value="<?= $b['id'] ?>"
                                        data-bb="<?= $b['berat_badan'] ?>"
                                        data-tb="<?= $b['tinggi_badan'] ?>"
                                        data-umur="<?= $b['umur_bulan'] ?>"
                                        data-status="<?= $b['status_gizi'] ?>"
                                        data-tahun="<?= $b['tahun'] ?>">
                                    <?= htmlspecialchars($b['nama_bayi']) ?>
                                    (<?= htmlspecialchars($b['nama_ortu']) ?>) — <?= $b['tahun'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Detail Balita (muncul otomatis) -->
                        <div id="detailBalita" style="display:none;">
                            <div class="row g-2 mb-3"
                                 style="background:#f8faf9;border-radius:8px;padding:12px;">
                                <div class="col-6">
                                    <small class="text-muted">Berat Badan</small>
                                    <div style="font-weight:700;" id="detBB">-</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Tinggi Badan</small>
                                    <div style="font-weight:700;" id="detTB">-</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Umur</small>
                                    <div style="font-weight:700;" id="detUmur">-</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Status Gizi</small>
                                    <div style="font-weight:700;" id="detStatus">-</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Catatan <small class="text-muted">(opsional)</small></label>
                            <textarea name="catatan" class="form-control" rows="3"
                                      placeholder="Catatan tambahan untuk KPM..."></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn flex-fill"
                                    style="background:#1a6b3a;color:#fff;border-radius:8px;font-weight:600;">
                                <i class="bi bi-send me-2"></i>Kirim ke KPM
                            </button>
                            <a href="index.php?page=kelurahan&act=stunting"
                               class="btn flex-fill"
                               style="background:#f0f0f0;color:#555;border-radius:8px;font-weight:600;">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function tampilDetail() {
    const sel    = document.getElementById('pilihBalita');
    const opt    = sel.options[sel.selectedIndex];
    const detail = document.getElementById('detailBalita');

    if (sel.value) {
        document.getElementById('detBB').textContent     = opt.dataset.bb + ' kg';
        document.getElementById('detTB').textContent     = opt.dataset.tb + ' cm';
        document.getElementById('detUmur').textContent   = opt.dataset.umur + ' bulan';
        document.getElementById('detStatus').textContent = opt.dataset.status.charAt(0).toUpperCase()
                                                         + opt.dataset.status.slice(1);
        detail.style.display = 'block';
    } else {
        detail.style.display = 'none';
    }
}
</script>

<?php require_once ROOT . '/app/views/layouts/footer.php'; ?>