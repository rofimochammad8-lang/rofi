<?php
// ============================================
// FILE: app/views/kelurahan/cetak_laporan.php
// Fungsi: Halaman cetak laporan (print friendly)
// ============================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Stunting</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            color: #000;
            background: #f0f0f0;
        }
        .halaman {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 20mm 25mm;
            background: #fff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .kop {
            display: flex;
            align-items: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .kop-logo {
            width: 65px; height: 65px;
            background: #1a6b3a;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-right: 16px; flex-shrink: 0;
            font-size: 28px; color: white; font-weight: bold;
        }
        .kop-teks h2 { font-size: 13pt; text-transform: uppercase; }
        .kop-teks p  { font-size: 10pt; margin-top: 2px; color: #333; }
        .judul-laporan {
            text-align: center; margin: 18px 0 14px;
        }
        .judul-laporan h3 {
            font-size: 13pt; font-weight: bold;
            text-transform: uppercase; text-decoration: underline;
        }
        .info-laporan table { width: 100%; font-size: 11pt; border-collapse: collapse; }
        .info-laporan td { padding: 3px 0; vertical-align: top; }
        .info-laporan td:first-child { width: 160px; font-weight: bold; }
        .info-laporan td:nth-child(2) { width: 10px; }
        .rekap-box {
            border: 1px solid #ccc; border-radius: 4px;
            padding: 10px 14px; margin: 14px 0; background: #f9f9f9;
        }
        .rekap-box h4 {
            font-size: 11pt; margin-bottom: 8px;
            border-bottom: 1px solid #ddd; padding-bottom: 5px;
        }
        .rekap-box table { width: 100%; }
        .rekap-box td { padding: 2px 0; font-size: 11pt; }
        .tabel-data {
            width: 100%; border-collapse: collapse; margin: 14px 0; font-size: 10pt;
        }
        .tabel-data th {
            background: #1a6b3a; color: white;
            padding: 7px; text-align: center; border: 1px solid #000;
        }
        .tabel-data td {
            padding: 5px 7px; border: 1px solid #888; vertical-align: middle;
        }
        .tabel-data tr:nth-child(even) td { background: #f5f5f5; }
        .center { text-align: center; }
        .status-normal   { color: #1a6b3a; font-weight: bold; }
        .status-beresiko { color: #b8860b; font-weight: bold; }
        .status-stunting { color: #c0392b; font-weight: bold; }
        .isi-laporan { margin: 10px 0; line-height: 1.9; font-size: 11pt; white-space: pre-line; }
        .ttd { display: flex; justify-content: flex-end; margin-top: 40px; }
        .ttd-box { text-align: center; width: 220px; }
        .ttd-box .jabatan { font-size: 11pt; font-weight: bold; margin-bottom: 65px; }
        .ttd-box .nama { font-size: 11pt; font-weight: bold; border-top: 1px solid #000; padding-top: 4px; }
        .no-print {
            position: fixed; top: 16px; right: 20px; z-index: 999; display: flex; gap: 8px;
        }
        .btn-cetak {
            background: #1a6b3a; color: white; border: none;
            border-radius: 8px; padding: 10px 20px;
            font-size: 14px; font-weight: bold; cursor: pointer;
        }
        .btn-kembali {
            background: #555; color: white; border: none;
            border-radius: 8px; padding: 10px 20px;
            font-size: 14px; font-weight: bold; cursor: pointer;
            text-decoration: none; display: inline-block;
        }
        @media print {
            .no-print  { display: none !important; }
            body       { background: #fff; }
            .halaman   { margin: 0; box-shadow: none; padding: 15mm 20mm; }
        }
    </style>
</head>
<body>

<!-- Tombol Aksi -->
<div class="no-print">
    <a href="index.php?page=kelurahan&act=laporan" class="btn-kembali">← Kembali</a>
    <button class="btn-cetak" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
</div>

<div class="halaman">

    <!-- Kop Surat -->
    <div class="kop">
        <div class="kop-logo">☘</div>
        <div class="kop-teks">
            <h2>Pemerintah Desa Sumberwaru</h2>
            <p>Sistem Informasi Geografis Visualisasi Stunting</p>
            <p>Desa Sumberwaru, Kecamatan Banyuputih, Kabupaten Situbondo</p>
        </div>
    </div>

    <!-- Judul -->
    <div class="judul-laporan">
        <h3>Laporan Data Stunting</h3>
    </div>

    <!-- Info Laporan -->
    <div class="info-laporan">
        <table>
            <tr>
                <td>Judul Laporan</td><td>:</td>
                <td><?= htmlspecialchars($laporan['judul']) ?></td>
            </tr>
            <tr>
                <td>Posyandu</td><td>:</td>
                <td><?= htmlspecialchars($laporan['nama_posyandu']) ?> — <?= htmlspecialchars($laporan['nama_dusun']) ?></td>
            </tr>
            <tr>
                <td>Tahun</td><td>:</td>
                <td><?= $laporan['tahun'] ?></td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td><td>:</td>
                <td><?= date('d F Y') ?></td>
            </tr>
            <tr>
                <td>Dicetak Oleh</td><td>:</td>
                <td><?= htmlspecialchars($_SESSION['user']['nama']) ?></td>
            </tr>
        </table>
    </div>

    <!-- Rekap Statistik -->
    <div class="rekap-box">
        <h4>Rekapitulasi Data Stunting</h4>
        <table>
            <tr>
                <td style="width:200px;">Total Balita Tercatat</td>
                <td>: <strong><?= $rekap['total'] ?> anak</strong></td>
            </tr>
            <tr>
                <td style="color:#1a6b3a;">Status Normal</td>
                <td>: <strong><?= $rekap['normal'] ?> anak</strong></td>
            </tr>
            <tr>
                <td style="color:#b8860b;">Status Berisiko</td>
                <td>: <strong><?= $rekap['beresiko'] ?> anak</strong></td>
            </tr>
            <tr>
                <td style="color:#c0392b;">Status Stunting</td>
                <td>: <strong><?= $rekap['stunting'] ?> anak</strong></td>
            </tr>
        </table>
    </div>

    <!-- Tabel Balita -->
    <?php if (mysqli_num_rows($data_balita_stunting) > 0): ?>
    <table class="tabel-data">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Bayi</th>
                <th>Nama Ortu</th>
                <th>JK</th>
                <th>Umur</th>
                <th>TB</th>
                <th>BB</th>
                <th>Status Gizi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($data_balita_stunting)):
                $cls = 'status-' . $row['status_gizi'];
            ?>
            <tr>
                <td class="center"><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_bayi']) ?></td>
                <td><?= htmlspecialchars($row['nama_ortu']) ?></td>
                <td class="center"><?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                <td class="center"><?= $row['umur_bulan'] ?> bln</td>
                <td class="center"><?= $row['tinggi_badan'] ?> cm</td>
                <td class="center"><?= $row['berat_badan'] ?> kg</td>
                <td class="center <?= $cls ?>"><?= ucfirst($row['status_gizi']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="font-size:11pt;color:#888;margin:12px 0;font-style:italic;">
        * Belum ada data balita stunting untuk posyandu dan tahun ini.
    </p>
    <?php endif; ?>

    <!-- Isi Laporan -->
    <div class="rekap-box">
        <h4>Isi Laporan & Instruksi Penanganan</h4>
        <div class="isi-laporan"><?= htmlspecialchars($laporan['isi']) ?></div>
    </div>

    <!-- Tanda Tangan -->
    <div class="ttd">
        <div class="ttd-box">
            <p style="font-size:11pt;margin-bottom:4px;">Sumberwaru, <?= date('d F Y') ?></p>
            <div class="jabatan">Petugas Kelurahan</div>
            <div class="nama"><?= htmlspecialchars($_SESSION['user']['nama']) ?></div>
        </div>
    </div>

</div>

</body>
</html>