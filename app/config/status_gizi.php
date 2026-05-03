<?php
// ============================================
// FILE: app/config/status_gizi.php
// Fungsi: Hitung status gizi otomatis
// ============================================

function hitungStatusGizi($jk, $umur_bulan, $tb, $bb, $lila = null, $lika = null) {

    $status_tb = hitungStatusTB($jk, $umur_bulan, $tb);
    $status_bb = hitungStatusBB($jk, $umur_bulan, $bb);

    // Lila & Lika hanya dihitung jika diisi dan nilainya > 0
    $status_lila = ($lila !== null && $lila > 0) ? hitungStatusLila($umur_bulan, $lila) : null;
    $status_lika = ($lika !== null && $lika > 0) ? hitungStatusLika($umur_bulan, $lika) : null;

    // Kumpulkan semua status yang ada
    $semua = array_filter(
        [$status_tb, $status_bb, $status_lila, $status_lika],
        fn($s) => $s !== null
    );

    if (in_array('stunting',  $semua)) return 'stunting';
    if (in_array('beresiko',  $semua)) return 'beresiko';
    return 'normal';
}

// ============================================
// TINGGI BADAN
// Laki-laki: 0-12bln(66-81), 1-2thn(76-92), 2-3thn(86-101), 3-5thn(96-112)
// Perempuan: 0-12bln(65-79), 1-2thn(74-90), 2-3thn(84-99),  3-5thn(94-110)
// ============================================
function hitungStatusTB($jk, $umur_bulan, $tb) {
    if ($jk === 'L') {
        if ($umur_bulan <= 12) {
            if ($tb >= 66) return 'normal';
            if ($tb >= 61) return 'beresiko';
            return 'stunting';
        } elseif ($umur_bulan <= 24) {
            if ($tb >= 76) return 'normal';
            if ($tb >= 71) return 'beresiko';
            return 'stunting';
        } elseif ($umur_bulan <= 36) {
            if ($tb >= 86) return 'normal';
            if ($tb >= 81) return 'beresiko';
            return 'stunting';
        } else {
            if ($tb >= 96) return 'normal';
            if ($tb >= 91) return 'beresiko';
            return 'stunting';
        }
    } else {
        if ($umur_bulan <= 12) {
            if ($tb >= 65) return 'normal';
            if ($tb >= 60) return 'beresiko';
            return 'stunting';
        } elseif ($umur_bulan <= 24) {
            if ($tb >= 74) return 'normal';
            if ($tb >= 69) return 'beresiko';
            return 'stunting';
        } elseif ($umur_bulan <= 36) {
            if ($tb >= 84) return 'normal';
            if ($tb >= 79) return 'beresiko';
            return 'stunting';
        } else {
            if ($tb >= 94) return 'normal';
            if ($tb >= 89) return 'beresiko';
            return 'stunting';
        }
    }
}

// ============================================
// BERAT BADAN
// Laki-laki: 0-12bln(7-10.5), 1-2thn(9.5-13), 2-3thn(11.5-14.5), 3-5thn(13.5-18.5)
// Perempuan: 0-12bln(6.5-10), 1-2thn(8.5-12), 2-3thn(10.5-14),   3-5thn(12.5-18)
// ============================================
function hitungStatusBB($jk, $umur_bulan, $bb) {
    if ($jk === 'L') {
        if ($umur_bulan <= 12) {
            if ($bb >= 7)    return 'normal';
            if ($bb >= 6)    return 'beresiko';
            return 'stunting';
        } elseif ($umur_bulan <= 24) {
            if ($bb >= 9.5)  return 'normal';
            if ($bb >= 8.5)  return 'beresiko';
            return 'stunting';
        } elseif ($umur_bulan <= 36) {
            if ($bb >= 11.5) return 'normal';
            if ($bb >= 10.5) return 'beresiko';
            return 'stunting';
        } else {
            if ($bb >= 13.5) return 'normal';
            if ($bb >= 12.5) return 'beresiko';
            return 'stunting';
        }
    } else {
        if ($umur_bulan <= 12) {
            if ($bb >= 6.5)  return 'normal';
            if ($bb >= 5.5)  return 'beresiko';
            return 'stunting';
        } elseif ($umur_bulan <= 24) {
            if ($bb >= 8.5)  return 'normal';
            if ($bb >= 7.5)  return 'beresiko';
            return 'stunting';
        } elseif ($umur_bulan <= 36) {
            if ($bb >= 10.5) return 'normal';
            if ($bb >= 9.5)  return 'beresiko';
            return 'stunting';
        } else {
            if ($bb >= 12.5) return 'normal';
            if ($bb >= 11.5) return 'beresiko';
            return 'stunting';
        }
    }
}

// ============================================
// LINGKAR LENGAN ATAS (LILA)
// 0-6bln(>=11.5 normal, 10.5-<11.5 beresiko, <10.5 stunting)
// 6-12bln(>=12 normal, 11-<12 beresiko, <11 stunting)
// 1-5thn(>=12.5 normal, 11.5-<12.5 beresiko, <11.5 stunting)
// ============================================
function hitungStatusLila($umur_bulan, $lila) {
    if ($umur_bulan <= 6) {
        if ($lila >= 11.5) return 'normal';
        if ($lila >= 10.5) return 'beresiko';
        return 'stunting';
    } elseif ($umur_bulan <= 12) {
        if ($lila >= 12)   return 'normal';
        if ($lila >= 11)   return 'beresiko';
        return 'stunting';
    } else {
        if ($lila >= 12.5) return 'normal';
        if ($lila >= 11.5) return 'beresiko';
        return 'stunting';
    }
}

// ============================================
// LINGKAR KEPALA (LIKA)
// 0-1thn: L(41-48 normal, 39-<41 beresiko, <39 stunting)
//         P(40-47 normal, 38-<40 beresiko, <38 stunting)
// 1-2thn: L(46-50 normal, 44-<46 beresiko, <44 stunting)
//         P(45-49 normal, 43-<45 beresiko, <43 stunting)
// 2-5thn: L(48-52 normal, 46-<48 beresiko, <46 stunting)
//         P(47-51 normal, 45-<47 beresiko, <45 stunting)
// ============================================
function hitungStatusLika($umur_bulan, $lika) {
    if ($umur_bulan <= 12) {
        if ($lika >= 41) return 'normal';
        if ($lika >= 39) return 'beresiko';
        return 'stunting';
    } elseif ($umur_bulan <= 24) {
        if ($lika >= 46) return 'normal';
        if ($lika >= 44) return 'beresiko';
        return 'stunting';
    } else {
        if ($lika >= 48) return 'normal';
        if ($lika >= 46) return 'beresiko';
        return 'stunting';
    }
}
?>