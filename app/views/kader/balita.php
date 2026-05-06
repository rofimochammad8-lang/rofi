<?php
require_once ROOT . '/app/views/layouts/header.php';

$bulan_list = [
    1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
    5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
    9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
];

// Pastikan variabel terdefinisi
$filter_tahun = $filter_tahun ?? '';
$tbl_tahun    = $tbl_tahun    ?? '';
$filter_aktif = !empty($filter_tahun);
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

        <!-- Notifikasi -->
        <?php if (isset($_GET['msg'])): ?>
        <?php
        $alerts = [
            'tambah_sukses'    => ['success', 'Data balita berhasil ditambahkan!'],
            'edit_sukses'      => ['success', 'Data balita berhasil diperbarui!'],
            'hapus_sukses'     => ['success', 'Data balita berhasil dihapus!'],
            'tidak_bisa_hapus' => ['warning', 'Tidak bisa hapus, data sudah ada di stunting!'],
            'invalid_input'    => ['warning', $_GET['error'] ?? 'Semua indikator wajib diisi dengan benar.'],
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

        <?php if (!empty($total_revisi) && $total_revisi > 0): ?>
        <div class="alert mb-4"
             style="background:#fff8e1;border:1px solid #ffe082;border-radius:10px;color:#b8860b;">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            Ada <strong><?= $total_revisi ?> data</strong> yang perlu direvisi. Periksa catatan pada tabel lalu edit data balita terkait.
        </div>
        <?php endif; ?>

        <!-- ===== FILTER TAHUN + FORM TAMBAH ===== -->
        <div class="card-table mb-4">
            <h6><i class="bi bi-funnel me-2"></i>Filter & Tambah Data Balita</h6>
            <p style="font-size:13px;color:#888;margin-bottom:14px;">
                Pilih tahun untuk menampilkan form tambah data balita.
            </p>

            <!-- Filter Tahun -->
            <form method="GET" action="index.php">
                <input type="hidden" name="page" value="kader">
                <input type="hidden" name="act"  value="balita">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Tahun Pencatatan</label>
                        <input type="number" name="tahun" class="form-control"
                               placeholder="Contoh: <?= date('Y') ?>"
                               min="2000" max="2099"
                               value="<?= $filter_tahun ?>">
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button type="submit" class="btn"
                                style="background:#1a6b3a;color:#fff;border-radius:8px;font-weight:600;">
                            <i class="bi bi-search me-1"></i>Tampilkan Form
                        </button>
                        <a href="index.php?page=kader&act=balita"
                           class="btn"
                           style="background:#f0f0f0;color:#555;border-radius:8px;font-weight:600;">
                            <i class="bi bi-x me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Form Tambah -->
            <?php if ($filter_aktif): ?>
            <hr class="my-4">
            <h6><i class="bi bi-person-plus me-2"></i>
                Tambah Balita — Tahun <?= $filter_tahun ?>
                <span style="font-size:12px;font-weight:400;color:#888;">
                    | Posyandu: <strong style="color:#1a6b3a;"><?= htmlspecialchars($posyandu_kader['nama_posyandu'] ?? '') ?></strong>
                </span>
            </h6>

            <form method="POST" action="index.php?page=kader&act=simpan_balita">
                <input type="hidden" name="filter_tahun" value="<?= $filter_tahun ?>">

                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">Nama Bayi</label>
                        <input type="text" name="nama_bayi" class="form-control"
                               placeholder="Nama lengkap bayi" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Nama Orang Tua</label>
                        <input type="text" name="nama_ortu" class="form-control"
                               placeholder="Nama ayah/ibu" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">NIK Orang Tua</label>
                        <input type="text" name="nik_ortu" class="form-control"
                               placeholder="Nomor Induk Kependudukan" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tgl_lahir"
                               class="form-control" onchange="hitungUmur()" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Umur (bulan)</label>
                        <input type="number" name="umur_bulan" id="umur_bulan"
                               class="form-control" min="0" max="60"
                               placeholder="Otomatis dari tgl lahir" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">BB (kg)</label>
                        <input type="number" step="0.1" name="berat_badan"
                               class="form-control" placeholder="8.5" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">TB (cm)</label>
                        <input type="number" step="0.1" name="tinggi_badan"
                               class="form-control" placeholder="75.0" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Lkr. Kepala (cm)</label>
                        <input type="number" step="0.1" name="lingkar_kepala"
                               class="form-control" placeholder="42.0" min="0.1" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Lkr. Lengan (cm)</label>
                        <input type="number" step="0.1" name="lingkar_lengan"
                               class="form-control" placeholder="14.0" min="0.1" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Bulan Pencatatan</label>
                        <select name="bulan_pencatatan" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <?php foreach ($bulan_list as $num => $nama_bulan): ?>
                            <option value="<?= $num ?>"><?= $nama_bulan ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn w-100"
                                style="background:#1a6b3a;color:#fff;border-radius:8px;font-weight:600;">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                    </div>

                </div>
            </form>

            <div class="alert mt-3 mb-0"
                 style="background:#e8f8ee;border:1px solid #b2dfcc;border-radius:8px;color:#1a6b3a;font-size:13px;">
                <i class="bi bi-info-circle me-2"></i>
                Status gizi dihitung dari TB, BB, lingkar kepala, dan lingkar lengan. Semua indikator wajib diisi.
            </div>

            <script>
            function hitungUmur() {
                const tgl = document.getElementById('tgl_lahir').value;
                if (!tgl) return;
                const lahir    = new Date(tgl);
                const sekarang = new Date();
                const bulan    = (sekarang.getFullYear() - lahir.getFullYear()) * 12
                               + (sekarang.getMonth() - lahir.getMonth());
                document.getElementById('umur_bulan').value = bulan >= 0 ? bulan : 0;
            }
            </script>

            <?php endif; ?>
        </div>

        <!-- ===== TABEL PERMANEN ===== -->
        <div class="card-table">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 style="margin:0;">
                    <i class="bi bi-table me-2"></i>Daftar Balita
                    <span style="font-size:12px;color:#888;font-weight:400;">
                        (<?= mysqli_num_rows($data_balita) ?> data)
                    </span>
                </h6>
            </div>

            <!-- Filter Tabel — tahun saja -->
            <form method="GET" action="index.php" class="mb-3">
                <input type="hidden" name="page"  value="kader">
                <input type="hidden" name="act"   value="balita">
                <input type="hidden" name="tahun" value="<?= $filter_tahun ?>">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:12px;">Filter Tahun</label>
                        <input type="number" name="tbl_tahun" class="form-control form-control-sm"
                               placeholder="Contoh: 2026" min="2000" max="2099"
                               value="<?= $tbl_tahun ?>">
                    </div>
                    <div class="col-auto d-flex gap-2">
                        <button type="submit" class="btn btn-sm"
                                style="background:#1a6b3a;color:#fff;border-radius:8px;">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="index.php?page=kader&act=balita&tahun=<?= $filter_tahun ?>"
                           class="btn btn-sm"
                           style="background:#f0f0f0;color:#555;border-radius:8px;">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <hr>
            <div class="table-responsive">
                <table class="table table-hover" style="font-size:13px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Bayi</th>
                            <th>Nama Ortu</th>
                            <th>NIK Ortu</th>
                            <th>Tgl Lahir</th>
                            <th>Umur</th>
                            <th>JK</th>
                            <th>BB</th>
                            <th>TB</th>
                            <th>Lkr. Kepala</th>
                            <th>Lkr. Lengan</th>
                            <th>Status</th>
                            <th>Status Verifikasi</th>
                            <th>Catatan KPM</th>
                            <th>Posyandu</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (mysqli_num_rows($data_balita) > 0):
                            while ($row = mysqli_fetch_assoc($data_balita)):
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
                            <td>
                                <?php
                                $statusVerifikasi = $row['status_verifikasi_revisi'] ?? '';
                                if ($statusVerifikasi === 'pending'):
                                ?>
                                <span class="badge-status badge-beresiko">Perlu Revisi</span>
                                <?php elseif ($statusVerifikasi === 'terverifikasi'): ?>
                                <span class="badge-status badge-normal">Terverifikasi KPM</span>
                                <?php elseif ($statusVerifikasi === 'disetujui'): ?>
                                <span class="badge-status badge-normal">Disetujui</span>
                                <?php else: ?>
                                -
                                <?php endif; ?>
                            </td>
                            <td style="min-width:220px;">
                                <?= !empty($row['catatan_revisi'])
                                    ? htmlspecialchars($row['catatan_revisi'])
                                    : '-' ?>
                            </td>
                            <td><?= htmlspecialchars($row['nama_posyandu']) ?></td>
                            <td><?= $bulan_list[(int)$row['bulan_pencatatan']] ?? '-' ?></td>
                            <td><?= $row['tahun'] ?></td>
                            <td>
                                <a href="index.php?page=kader&act=edit_balita&id=<?= $row['id'] ?>&tahun=<?= $filter_tahun ?>"
                                   class="btn btn-sm"
                                   style="background:#e8f0fe;color:#1a73e8;border-radius:6px;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="index.php?page=kader&act=hapus_balita&id=<?= $row['id'] ?>&tahun=<?= $filter_tahun ?>"
                                   class="btn btn-sm ms-1"
                                   style="background:#ffeaea;color:#c0392b;border-radius:6px;"
                                   onclick="return confirm('Yakin hapus data <?= htmlspecialchars($row['nama_bayi']) ?>?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="17" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                <?= !empty($tbl_tahun)
                                    ? 'Tidak ada data untuk tahun '.$tbl_tahun
                                    : 'Belum ada data balita.' ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php require_once ROOT . '/app/views/layouts/footer.php'; ?>
