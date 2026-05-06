<?php
// ============================================
// FILE: app/config/status_gizi.php
// Fungsi: Hitung status gizi otomatis
// ============================================

function hitungStatusGizi($jk, $umur_bulan, $tb, $bb, $lila = null, $lika = null) {
    $status_tb = hitungStatusTB($jk, $umur_bulan, $tb);
    $status_bb = hitungStatusBB($jk, $umur_bulan, $bb);

    if ($lila === null || $lila <= 0 || $lika === null || $lika <= 0) {
        return null;
    }

    $status_lila = hitungStatusLila($umur_bulan, $lila);
    $status_lika = hitungStatusLika($jk, $umur_bulan, $lika);

    return hitungStatusMayoritas([
        $status_tb,
        $status_bb,
        $status_lila,
        $status_lika,
    ]);
}

// ============================================
// TINGGI/PANJANG BADAN MENURUT UMUR
// Dokumen acuan: sangat pendek/pendek/normal/tinggi
// Tinggi di atas batas normal dipetakan ke "beresiko"
// ============================================
function hitungStatusTB($jk, $umur_bulan, $tb) {
    $aturan = [
        'L' => [
            [12, 61.0, 66.0, 81.0],
            [24, 71.0, 76.0, 92.0],
            [36, 81.0, 86.0, 101.0],
            [60, 91.0, 96.0, 112.0],
        ],
        'P' => [
            [12, 60.0, 65.0, 79.0],
            [24, 69.0, 74.0, 90.0],
            [36, 79.0, 84.0, 99.0],
            [60, 89.0, 94.0, 110.0],
        ],
    ];

    return hitungStatusDenganRentang($aturan, $jk, $umur_bulan, $tb);
}

// ============================================
// BERAT BADAN MENURUT UMUR
// Dokumen acuan: sangat kurang/kurang/normal/resiko lebih
// Nilai di atas batas normal dipetakan ke "beresiko"
// ============================================
function hitungStatusBB($jk, $umur_bulan, $bb) {
    $aturan = [
        'L' => [
            [12, 6.0, 7.0, 10.5],
            [24, 8.5, 9.5, 13.0],
            [36, 10.5, 11.5, 14.5],
            [60, 12.5, 13.5, 18.5],
        ],
        'P' => [
            [12, 5.5, 6.5, 10.0],
            [24, 7.5, 8.5, 12.0],
            [36, 9.5, 10.5, 14.0],
            [60, 11.5, 12.5, 18.0],
        ],
    ];

    return hitungStatusDenganRentang($aturan, $jk, $umur_bulan, $bb);
}

// ============================================
// LINGKAR LENGAN ATAS (LILA)
// Aturan lama dipertahankan karena dokumen pengganti tidak konsisten
// ============================================
function hitungStatusLila($umur_bulan, $lila) {
    if ($umur_bulan <= 6) {
        if ($lila >= 11.5) {
            return 'normal';
        }
        if ($lila >= 10.5) {
            return 'beresiko';
        }
        return 'stunting';
    }

    if ($umur_bulan <= 12) {
        if ($lila >= 12.0) {
            return 'normal';
        }
        if ($lila >= 11.0) {
            return 'beresiko';
        }
        return 'stunting';
    }

    if ($lila >= 12.5) {
        return 'normal';
    }
    if ($lila >= 11.5) {
        return 'beresiko';
    }
    return 'stunting';
}

// ============================================
// LINGKAR KEPALA MENURUT UMUR
// Dokumen acuan: mikrosefali/normal/makrosefali
// Makrosefali dipetakan ke "beresiko"
// ============================================
function hitungStatusLika($jk, $umur_bulan, $lika) {
    $aturan = [
        'L' => [
            [12, 39.0, 41.0, 48.0],
            [24, 44.0, 46.0, 50.0],
            [60, 46.0, 48.0, 52.0],
        ],
        'P' => [
            [12, 38.0, 40.0, 47.0],
            [24, 43.0, 45.0, 49.0],
            [60, 45.0, 47.0, 51.0],
        ],
    ];

    return hitungStatusDenganRentang($aturan, $jk, $umur_bulan, $lika);
}

function hitungStatusDenganRentang(array $aturan, $jk, $umur_bulan, $nilai) {
    $jk = ($jk === 'P') ? 'P' : 'L';
    $buckets = $aturan[$jk] ?? $aturan['L'];

    foreach ($buckets as [$maks_umur, $batas_risiko_bawah, $batas_normal_bawah, $batas_normal_atas]) {
        if ($umur_bulan <= $maks_umur) {
            if ($nilai < $batas_risiko_bawah) {
                return 'stunting';
            }
            if ($nilai < $batas_normal_bawah) {
                return 'beresiko';
            }
            if ($nilai <= $batas_normal_atas) {
                return 'normal';
            }
            return 'beresiko';
        }
    }

    return 'beresiko';
}

function hitungStatusMayoritas(array $semua_status) {
    $semua_status = array_values(array_filter(
        $semua_status,
        static fn($status) => in_array($status, ['normal', 'beresiko', 'stunting'], true)
    ));

    if ($semua_status === []) {
        return null;
    }

    $jumlah = array_count_values($semua_status);
    $maksimum = max($jumlah);
    $pemenang = array_keys($jumlah, $maksimum, true);

    if (count($pemenang) !== 1) {
        return 'beresiko';
    }

    return $pemenang[0];
}
?>
