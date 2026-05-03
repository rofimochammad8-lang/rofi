<?php
// ============================================
// FILE: app/views/kader/balita.php
// Fungsi: Halaman lihat & tambah data balita
// ============================================
require_once ROOT . '/app/views/layouts/header.php';
?>

<div class="sidebar">
    <?php require_once ROOT . '/app/views/layouts/sidebar.php'; ?>
</div>

<div class="main-content">

    <div class="topbar">
        <h5><i class="bi bi-people me-2"></i>Data Balita</h5>
        <span class="date"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?></span>
    </div>

    <div class="content-area">

        <!-- Notifikasi pesan -->
        <?php if (isset($_GET['msg'])): ?>
            <?php
            $msg = $_GET['msg'];
            $alerts = [
                'tambah_sukses'    => ['success', 'Data balita berhasil ditambahkan!'],
                'edit_sukses'      => ['success', 'Data balita berhasil diperbarui!'],
                'hapus_sukses'     => ['success', 'Data balita berhasil dihapus!'],
                'tidak_bisa_hapus' => ['warning', 'Data tidak bisa dihapus karena sudah memiliki data stunting!'],
                'gagal'            => ['danger',  'Terjadi kesalahan, coba lagi!'],
            ];
            if (isset($alerts[$msg])):
                [$type, $text] = $alerts[$msg];
            ?>
            <div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert"
                 style="border-radius:10px;font-size:14px;">
                <i class="bi bi-<?= $type === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
                <?= $text ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="row g-4">

            <!-- Form Tambah Balita -->
            <div class="col-md-4">
                <div class="card-table">
                    <h6><i class="bi bi-person-plus me-2"></i>Tambah Data Balita</h6>
                    <hr>
                    <form method="POST" action="index.php?page=kader&act=simpan_balita">

                        <div class="mb-3">
                            <label class="form-label">NIK Balita</label>
                            <input type="text" name="nik" class="form-control"
                                   placeholder="Nomor Induk Kependudukan" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Balita</label>
                            <input type="text" name="nama" class="form-control"
                                   placeholder="Nama lengkap balita" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control" required>
                                <option value="">-- Pilih --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Ibu</label>
                            <input type="text" name="nama_ibu" class="form-control"
                                   placeholder="Nama ibu kandung" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Posyandu</label>
                            <select name="id_posyandu" class="form-control" required>
                                <option value="">-- Pilih Posyandu --</option>
                                <?php while ($p = mysqli_fetch_assoc($data_posyandu)): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['nama_posyandu']) ?> - <?= $p['nama_dusun'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Tahun</label>
                            <select name="id_tahun" class="form-control" required>
                                <option value="">-- Pilih Tahun --</option>
                                <?php while ($t = mysqli_fetch_assoc($data_tahun)): ?>
                                <option value="<?= $t['id'] ?>"><?= $t['tahun'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn w-100"
                                style="background:#1a6b3a;color:#fff;border-radius:8px;font-weight:600;">
                            <i class="bi bi-save me-2"></i>Simpan Data
                        </button>

                    </form>
                </div>
            </div>

            <!-- Tabel Data Balita -->
            <div class="col-md-8">
                <div class="card-table">
                    <h6><i class="bi bi-table me-2"></i>Daftar Balita</h6>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>NIK</th>
                                    <th>Nama Balita</th>
                                    <th>JK</th>
                                    <th>Tgl Lahir</th>
                                    <th>Nama Ibu</th>
                                    <th>Posyandu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                if (mysqli_num_rows($data_balita) > 0):
                                    while ($row = mysqli_fetch_assoc($data_balita)):
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nik']) ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= $row['jenis_kelamin'] === 'L' ? '👦' : '👧' ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal_lahir'])) ?></td>
                                    <td><?= htmlspecialchars($row['nama_ibu']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_posyandu']) ?></td>
                                    <td>
                                        <!-- Tombol Edit -->
                                        <a href="index.php?page=kader&act=edit_balita&id=<?= $row['id'] ?>"
                                           class="btn btn-sm"
                                           style="background:#e8f0fe;color:#1a73e8;border-radius:6px;">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <!-- Tombol Hapus -->
                                        <a href="index.php?page=kader&act=hapus_balita&id=<?= $row['id'] ?>"
                                           class="btn btn-sm ms-1"
                                           style="background:#ffeaea;color:#c0392b;border-radius:6px;"
                                           onclick="return confirm('Yakin hapus data <?= htmlspecialchars($row['nama']) ?>?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                        Belum ada data balita
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