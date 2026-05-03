<?php
require_once ROOT . '/app/views/layouts/header.php';

$bulan_list = [
    1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
    5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
    9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
];

$filter_tahun      = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$id_posyandu_kader = $_SESSION['user']['id_posyandu'] ?? null;
?>

<div class="sidebar">
    <?php require_once ROOT . '/app/views/layouts/sidebar.php'; ?>
</div>

<div class="main-content">

    <div class="topbar">
        <h5><i class="bi bi-pencil-square me-2"></i>Edit Data Balita</h5>
        <span class="date"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?></span>
    </div>

    <div class="content-area">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card-table">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6><i class="bi bi-person-gear me-2"></i>Form Edit Balita</h6>
                        <a href="index.php?page=kader&act=balita&tahun=<?= $filter_tahun ?>"
                           class="btn btn-sm"
                           style="background:#f0f0f0;color:#555;border-radius:8px;font-size:13px;">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                    <hr>

                    <form method="POST" action="index.php?page=kader&act=update_balita">
                        <input type="hidden" name="id"           value="<?= $balita['id'] ?>">
                        <input type="hidden" name="filter_tahun" value="<?= $filter_tahun ?>">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Nama Bayi</label>
                                <input type="text" name="nama_bayi" class="form-control"
                                       value="<?= htmlspecialchars($balita['nama_bayi']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nama Orang Tua</label>
                                <input type="text" name="nama_ortu" class="form-control"
                                       value="<?= htmlspecialchars($balita['nama_ortu']) ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" id="tgl_lahir"
                                       class="form-control" onchange="hitungUmur()"
                                       value="<?= $balita['tanggal_lahir'] ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Umur (bulan)</label>
                                <input type="number" name="umur_bulan" id="umur_bulan"
                                       class="form-control" min="0" max="60"
                                       value="<?= $balita['umur_bulan'] ?>" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="L" <?= $balita['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= $balita['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">BB (kg)</label>
                                <input type="number" step="0.1" name="berat_badan"
                                       class="form-control"
                                       value="<?= $balita['berat_badan'] ?>" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">TB (cm)</label>
                                <input type="number" step="0.1" name="tinggi_badan"
                                       class="form-control"
                                       value="<?= $balita['tinggi_badan'] ?>" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Lingkar Kepala (cm)</label>
                                <input type="number" step="0.1" name="lingkar_kepala"
                                       class="form-control"
                                       value="<?= $balita['lingkar_kepala'] ?>">
                                <small class="text-muted">Opsional</small>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Lingkar Lengan (cm)</label>
                                <input type="number" step="0.1" name="lingkar_lengan"
                                       class="form-control"
                                       value="<?= $balita['lingkar_lengan'] ?>">
                                <small class="text-muted">Opsional</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Bulan Pencatatan</label>
                                <select name="bulan_pencatatan" class="form-control" required>
                                    <?php foreach ($bulan_list as $num => $nama_bulan): ?>
                                    <option value="<?= $num ?>"
                                        <?= $balita['bulan_pencatatan'] == $num ? 'selected' : '' ?>>
                                        <?= $nama_bulan ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>

                        <!-- Info status otomatis -->
                        <div class="alert mb-3"
                             style="background:#e8f8ee;border:1px solid #b2dfcc;
                                    border-radius:8px;color:#1a6b3a;font-size:13px;">
                            <i class="bi bi-info-circle me-2"></i>
                            Status gizi akan dihitung <strong>otomatis</strong> berdasarkan
                            TB, BB, lingkar kepala, dan lingkar lengan yang diisi.
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn flex-fill"
                                    style="background:#1a6b3a;color:#fff;border-radius:8px;font-weight:600;">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="index.php?page=kader&act=balita&tahun=<?= $filter_tahun ?>"
                               class="btn flex-fill"
                               style="background:#f0f0f0;color:#555;border-radius:8px;font-weight:600;">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        </div>

                    </form>

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

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT . '/app/views/layouts/footer.php'; ?>