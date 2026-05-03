<?php
// ============================================
// FILE: app/views/kelurahan/edit_posyandu.php
// ============================================
require_once ROOT . '/app/views/layouts/header.php';
?>

<div class="sidebar">
    <?php require_once ROOT . '/app/views/layouts/sidebar.php'; ?>
</div>

<div class="main-content">

    <div class="topbar">
        <h5><i class="bi bi-pencil-square me-2"></i>Edit Data Posyandu</h5>
        <span class="date"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?></span>
    </div>

    <div class="content-area">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card-table">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6><i class="bi bi-house-gear me-2"></i>Form Edit Posyandu</h6>
                        <a href="index.php?page=kelurahan&act=posyandu"
                           class="btn btn-sm"
                           style="background:#f0f0f0;color:#555;border-radius:8px;font-size:13px;">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                    <hr>

                    <form method="POST" action="index.php?page=kelurahan&act=update_posyandu">
                        <input type="hidden" name="id" value="<?= $posyandu['id'] ?>">

                        <div class="mb-3">
                            <label class="form-label">Nama Posyandu</label>
                            <input type="text" name="nama_posyandu" class="form-control"
                                   value="<?= htmlspecialchars($posyandu['nama_posyandu']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Dusun</label>
                            <input type="text" name="nama_dusun" class="form-control"
                                   value="<?= htmlspecialchars($posyandu['nama_dusun']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Koordinat Latitude</label>
                            <input type="text" name="koordinat_lat" class="form-control"
                                   value="<?= $posyandu['koordinat_lat'] ?>">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Koordinat Longitude</label>
                            <input type="text" name="koordinat_lng" class="form-control"
                                   value="<?= $posyandu['koordinat_lng'] ?>">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn flex-fill"
                                    style="background:#1a6b3a;color:#fff;border-radius:8px;font-weight:600;">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="index.php?page=kelurahan&act=posyandu"
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

<?php require_once ROOT . '/app/views/layouts/footer.php'; ?>