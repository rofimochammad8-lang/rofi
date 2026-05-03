<?php
// ============================================
// FILE: app/controllers/PetaController.php
// Fungsi: Menangani halaman peta stunting
// Diakses oleh: KPM dan Kelurahan
// ============================================

require_once ROOT . '/app/config/database.php';

$act = isset($_GET['act']) ? $_GET['act'] : 'index';

switch ($act) {

    // ----------------------------------------
    // HALAMAN PETA UTAMA
    // ----------------------------------------
    case 'index':
        // Ambil semua tahun untuk filter dropdown
        $semua_tahun = mysqli_query($conn, "SELECT * FROM tahun ORDER BY tahun DESC");

        // Total semua posyandu (selalu tetap)
        $total_semua_posyandu = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) as total FROM posyandu")
        )['total'];

        // ID tahun aktif dari URL
        $id_tahun_aktif = isset($_GET['id_tahun']) ? $_GET['id_tahun'] : '';

        require_once ROOT . '/app/views/peta/index.php';
        break;

    // ----------------------------------------
    // API: Kirim data posyandu + jumlah stunting
    // Dipanggil oleh JavaScript (AJAX)
    // ----------------------------------------
    case 'data_peta':
        header('Content-Type: application/json');

        $id_tahun = isset($_GET['id_tahun']) ? $_GET['id_tahun'] : '';

        // Filter tahun jika dipilih
        $filter_tahun_sql = $id_tahun
            ? "AND b.id_tahun = '" . mysqli_real_escape_string($conn, $id_tahun) . "'"
            : '';

        // Ambil data tiap posyandu beserta jumlah per status gizi
        $join_tahun = $id_tahun
            ? "AND b.id_tahun = '" . mysqli_real_escape_string($conn, $id_tahun) . "'"
            : "";

        $query = "SELECT
                    p.id,
                    p.nama_posyandu,
                    p.nama_dusun,
                    p.koordinat_lat,
                    p.koordinat_lng,
                    SUM(CASE WHEN b.status_gizi = 'stunting' THEN 1 ELSE 0 END) as jml_stunting,
                    SUM(CASE WHEN b.status_gizi = 'beresiko' THEN 1 ELSE 0 END) as jml_beresiko,
                    SUM(CASE WHEN b.status_gizi = 'normal'   THEN 1 ELSE 0 END) as jml_normal,
                    SUM(CASE WHEN b.id IS NOT NULL THEN 1 ELSE 0 END) as total_balita
                  FROM posyandu p
                  LEFT JOIN balita b ON p.id = b.id_posyandu $join_tahun
                  GROUP BY p.id, p.nama_posyandu, p.nama_dusun, p.koordinat_lat, p.koordinat_lng
                  ORDER BY p.nama_posyandu";

        $result = mysqli_query($conn, $query);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Warna berdasarkan jumlah stunting per posyandu
            $jml = (int)$row['jml_stunting'];

            if ($jml >= 4) {
                $kategori = 'merah';
                $warna    = '#e53935';
            } elseif ($jml >= 2) {
                $kategori = 'kuning';
                $warna    = '#FBC02D';
            } else {
                $kategori = 'hijau';
                $warna    = '#43A047';
            }

            // Skip posyandu jika tidak ada data balita
            if ((int)$row['total_balita'] === 0) continue;

            $data[] = [
                'id'            => $row['id'],
                'nama_posyandu' => $row['nama_posyandu'],
                'nama_dusun'    => $row['nama_dusun'],
                'lat'           => (float)$row['koordinat_lat'],
                'lng'           => (float)$row['koordinat_lng'],
                'jml_stunting'  => (int)$row['jml_stunting'],
                'jml_beresiko'  => (int)$row['jml_beresiko'],
                'jml_normal'    => (int)$row['jml_normal'],
                'total_balita'  => (int)$row['total_balita'],
                'kategori'      => $kategori,
                'warna'         => $warna,
            ];
        }

        echo json_encode($data);
        exit;
        break;

    // DEBUG - cek data balita
    case 'debug':
        header('Content-Type: application/json');
        $balita = mysqli_query($conn,
            "SELECT b.id, b.nama_bayi, b.id_posyandu, b.status_gizi,
                    p.nama_posyandu
             FROM balita b
             LEFT JOIN posyandu p ON b.id_posyandu = p.id
             LIMIT 10"
        );
        $rows = [];
        while ($r = mysqli_fetch_assoc($balita)) {
            $rows[] = $r;
        }
        echo json_encode($rows);
        exit;
        break;

    default:
        require_once ROOT . '/app/views/peta/index.php';
        break;
}
?>