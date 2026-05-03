<?php
// ============================================
// FILE: app/views/kelurahan/posyandu.php
// Fungsi: Kelola data posyandu oleh kelurahan
// ============================================
require_once ROOT . '/app/views/layouts/header.php';
?>

<div class="sidebar">
    <?php require_once ROOT . '/app/views/layouts/sidebar.php'; ?>
</div>

<div class="main-content">

    <div class="topbar">
        <h5><i class="bi bi-house-heart me-2"></i>Kelola Data Posyandu</h5>
        <span class="date"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?></span>
    </div>

    <div class="content-area">

        <!-- Notifikasi -->
        <?php if (isset($_GET['msg'])): ?>
            <?php
            $alerts = [
                'tambah_sukses'             => ['success', 'Posyandu berhasil ditambahkan!'],
                'edit_sukses'               => ['success', 'Posyandu berhasil diperbarui!'],
                'hapus_sukses'              => ['success', 'Posyandu berhasil dihapus!'],
                'tidak_bisa_hapus_balita'   => ['warning', 'Posyandu tidak bisa dihapus karena masih memiliki data balita!'],
                'tidak_bisa_hapus_kader'    => ['warning', 'Posyandu tidak bisa dihapus karena masih ada akun kader yang terikat!'],
                'gagal'                     => ['danger',  'Terjadi kesalahan, coba lagi!'],
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

            <!-- Form Tambah -->
            <div class="col-md-4">
                <div class="card-table">
                    <h6><i class="bi bi-plus-circle me-2"></i>Tambah Posyandu</h6>
                    <hr>
                    <form method="POST" action="index.php?page=kelurahan&act=simpan_posyandu">

                        <div class="mb-3">
                            <label class="form-label">Nama Posyandu</label>
                            <input type="text" name="nama_posyandu" class="form-control"
                                   placeholder="Contoh: Posyandu Mawar" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Dusun</label>
                            <input type="text" name="nama_dusun" class="form-control"
                                   placeholder="Contoh: Dusun Krajan" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Koordinat Latitude</label>
                            <input type="text" name="koordinat_lat" class="form-control"
                                   placeholder="Contoh: -7.123456">
                            <small class="text-muted">Untuk tampilan peta</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Koordinat Longitude</label>
                            <input type="text" name="koordinat_lng" class="form-control"
                                   placeholder="Contoh: 113.456789">
                        </div>

                        <button type="submit" class="btn w-100"
                                style="background:#1a6b3a;color:#fff;border-radius:8px;font-weight:600;">
                            <i class="bi bi-save me-2"></i>Simpan
                        </button>

                    </form>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="col-md-8">
                <div class="card-table">
                    <h6><i class="bi bi-table me-2"></i>Daftar Posyandu</h6>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Posyandu</th>
                                    <th>Nama Dusun</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                if (mysqli_num_rows($data_posyandu) > 0):
                                    while ($row = mysqli_fetch_assoc($data_posyandu)):
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= htmlspecialchars($row['nama_posyandu']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['nama_dusun']) ?></td>
                                    <td><?= $row['koordinat_lat'] ?: '-' ?></td>
                                    <td><?= $row['koordinat_lng'] ?: '-' ?></td>
                                    <td>
                                        <a href="index.php?page=kelurahan&act=edit_posyandu&id=<?= $row['id'] ?>"
                                           class="btn btn-sm"
                                           style="background:#e8f0fe;color:#1a73e8;border-radius:6px;">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="index.php?page=kelurahan&act=hapus_posyandu&id=<?= $row['id'] ?>"
                                           class="btn btn-sm ms-1"
                                           style="background:#ffeaea;color:#c0392b;border-radius:6px;"
                                           onclick="return confirm('Yakin hapus posyandu ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                        Belum ada data posyandu
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

<?php require_once ROOT . '/app/views/layouts/footer.php'; ?>