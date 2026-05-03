<?php
// ============================================
// FILE: app/views/kader/tahun.php
// Fungsi: Halaman input & kelola data tahun
// ============================================
require_once ROOT . '/app/views/layouts/header.php';
?>

<div class="sidebar">
    <?php require_once ROOT . '/app/views/layouts/sidebar.php'; ?>
</div>

<div class="main-content">

    <div class="topbar">
        <h5><i class="bi bi-calendar me-2"></i>Data Tahun</h5>
        <span class="date"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?></span>
    </div>

    <div class="content-area">

        <!-- Notifikasi -->
        <?php if (isset($_GET['msg'])): ?>
            <?php
            $alerts = [
                'tambah_sukses'    => ['success', 'Tahun berhasil ditambahkan!'],
                'hapus_sukses'     => ['success', 'Tahun berhasil dihapus!'],
                'tidak_bisa_hapus' => ['warning', 'Tahun tidak bisa dihapus karena masih digunakan data balita!'],
                'gagal'            => ['danger',  'Terjadi kesalahan, coba lagi!'],
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

        <!-- Info -->
        <div class="alert" style="background:#e8f0fe;border:1px solid #b3c8f5;
             border-radius:10px;color:#1a73e8;font-size:13px;margin-bottom:20px;">
            <i class="bi bi-info-circle me-2"></i>
            Tambahkan tahun pencatatan sebelum menginput data balita.
            Tahun yang sudah digunakan tidak bisa dihapus.
        </div>

        <div class="row g-4 justify-content-center">

            <!-- Form Tambah Tahun -->
            <div class="col-md-4">
                <div class="card-table">
                    <h6><i class="bi bi-plus-circle me-2"></i>Tambah Tahun</h6>
                    <hr>
                    <form method="POST" action="index.php?page=kader&act=simpan_tahun">

                        <div class="mb-4">
                            <label class="form-label">Tahun Pencatatan</label>
                            <input type="number" name="tahun" class="form-control"
                                   placeholder="Contoh: <?= date('Y') ?>"
                                   min="2000" max="2099"
                                   value="<?= date('Y') ?>"
                                   required>
                            <small class="text-muted">
                                Tahun ini akan digunakan sebagai kategori pencatatan data balita
                            </small>
                        </div>

                        <button type="submit" class="btn w-100"
                                style="background:#1a6b3a;color:#fff;border-radius:8px;font-weight:600;">
                            <i class="bi bi-save me-2"></i>Simpan Tahun
                        </button>

                    </form>
                </div>
            </div>

            <!-- Tabel Daftar Tahun -->
            <div class="col-md-5">
                <div class="card-table">
                    <h6><i class="bi bi-table me-2"></i>Daftar Tahun Tersedia</h6>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tahun</th>
                                    <th>Jumlah Balita</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                if (mysqli_num_rows($data_tahun) > 0):
                                    // Reset pointer
                                    mysqli_data_seek($data_tahun, 0);
                                    while ($row = mysqli_fetch_assoc($data_tahun)):
                                        // Hitung jumlah balita per tahun
                                        $jml = mysqli_fetch_assoc(
                                            mysqli_query($conn,
                                                "SELECT COUNT(*) as total FROM balita WHERE id_tahun = '{$row['id']}'"
                                            )
                                        )['total'];
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <span style="font-weight:700;font-size:18px;color:#1a6b3a;">
                                            <?= $row['tahun'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-status <?= $jml > 0 ? 'badge-normal' : '' ?>"
                                              style="<?= $jml == 0 ? 'color:#888' : '' ?>">
                                            <?= $jml ?> balita
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($jml == 0): ?>
                                        <a href="index.php?page=kader&act=hapus_tahun&id=<?= $row['id'] ?>"
                                           class="btn btn-sm"
                                           style="background:#ffeaea;color:#c0392b;border-radius:6px;"
                                           onclick="return confirm('Yakin hapus tahun <?= $row['tahun'] ?>?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                        <?php else: ?>
                                        <span style="font-size:12px;color:#aaa;">
                                            <i class="bi bi-lock"></i> Digunakan
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-calendar-x fs-4 d-block mb-2"></i>
                                        Belum ada data tahun.<br>
                                        <small>Tambahkan tahun di form sebelah kiri.</small>
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