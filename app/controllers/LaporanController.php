<?php
// ============================================
// FILE: app/controllers/LaporanController.php
// Fungsi: Menangani semua halaman role Kelurahan
// Fitur: Dashboard, posyandu, stunting, laporan
// ============================================

require_once ROOT . '/app/config/database.php';

$act = isset($_GET['act']) ? $_GET['act'] : 'dashboard';

switch ($act) {

    // ----------------------------------------
    // DASHBOARD KELURAHAN
    // ----------------------------------------
    case 'dashboard':
        // Hapus dulu data stunting orphan jika ada
        mysqli_query($conn, "DELETE FROM stunting WHERE id_balita NOT IN (SELECT id FROM balita)");

        $total_balita = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) as total FROM balita")
        )['total'];

        $total_menunggu = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) as total FROM stunting
                                 WHERE status_verifikasi = 'terverifikasi'")
        )['total'];

        $total_stunting = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) as total FROM balita
                                 WHERE status_gizi = 'stunting'")
        )['total'];

        $total_laporan = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan")
        )['total'];

        $data_terverifikasi = mysqli_query($conn,
            "SELECT s.*, b.nama_bayi, b.nama_ortu, b.jenis_kelamin,
                    b.status_gizi, b.bulan_pencatatan, t.tahun
             FROM stunting s
             JOIN balita b ON s.id_balita = b.id
             JOIN tahun t ON b.id_tahun = t.id
             WHERE s.status_verifikasi = 'terverifikasi'
             ORDER BY s.created_at DESC LIMIT 5"
        );

        require_once ROOT . '/app/views/kelurahan/dashboard.php';
        break;

    // ----------------------------------------
    // KELOLA DATA POSYANDU
    // ----------------------------------------
    case 'posyandu':
        $data_posyandu = mysqli_query($conn,
            "SELECT * FROM posyandu ORDER BY nama_posyandu"
        );
        require_once ROOT . '/app/views/kelurahan/posyandu.php';
        break;

    case 'simpan_posyandu':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_posyandu = trim($_POST['nama_posyandu']);
            $nama_dusun    = trim($_POST['nama_dusun']);
            $lat           = trim($_POST['koordinat_lat']);
            $lng           = trim($_POST['koordinat_lng']);

            $query = "INSERT INTO posyandu (nama_posyandu, nama_dusun, koordinat_lat, koordinat_lng)
                      VALUES ('$nama_posyandu', '$nama_dusun', '$lat', '$lng')";

            if (mysqli_query($conn, $query)) {
                header("Location: index.php?page=kelurahan&act=posyandu&msg=tambah_sukses");
            } else {
                header("Location: index.php?page=kelurahan&act=posyandu&msg=gagal");
            }
            exit;
        }
        break;

    case 'edit_posyandu':
        $id = $_GET['id'];
        $posyandu = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT * FROM posyandu WHERE id = '$id'")
        );
        require_once ROOT . '/app/views/kelurahan/edit_posyandu.php';
        break;

    case 'update_posyandu':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id            = $_POST['id'];
            $nama_posyandu = trim($_POST['nama_posyandu']);
            $nama_dusun    = trim($_POST['nama_dusun']);
            $lat           = trim($_POST['koordinat_lat']);
            $lng           = trim($_POST['koordinat_lng']);

            $query = "UPDATE posyandu SET
                        nama_posyandu='$nama_posyandu', nama_dusun='$nama_dusun',
                        koordinat_lat='$lat', koordinat_lng='$lng'
                      WHERE id='$id'";

            if (mysqli_query($conn, $query)) {
                header("Location: index.php?page=kelurahan&act=posyandu&msg=edit_sukses");
            } else {
                header("Location: index.php?page=kelurahan&act=posyandu&msg=gagal");
            }
            exit;
        }
        break;

    case 'hapus_posyandu':
        $id = $_GET['id'];

        // Cek apakah ada balita atau kader yang terikat
        $cek_balita = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) as total FROM balita WHERE id_posyandu = '$id'")
        )['total'];

        $cek_kader = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE id_posyandu = '$id'")
        )['total'];

        if ($cek_balita > 0) {
            header("Location: index.php?page=kelurahan&act=posyandu&msg=tidak_bisa_hapus_balita");
        } elseif ($cek_kader > 0) {
            header("Location: index.php?page=kelurahan&act=posyandu&msg=tidak_bisa_hapus_kader");
        } else {
            // Hapus laporan terkait dulu
            mysqli_query($conn, "DELETE FROM laporan WHERE id_posyandu = '$id'");
            // Baru hapus posyandu
            mysqli_query($conn, "DELETE FROM posyandu WHERE id = '$id'");
            header("Location: index.php?page=kelurahan&act=posyandu&msg=hapus_sukses");
        }
        exit;
        break;

    // ----------------------------------------
    // KELOLA DATA STUNTING
    // ----------------------------------------
    case 'stunting':
        $data_stunting = mysqli_query($conn,
            "SELECT s.*, b.nama_bayi, b.nama_ortu, b.tanggal_lahir,
                    b.jenis_kelamin, b.berat_badan, b.tinggi_badan,
                    b.lingkar_kepala, b.lingkar_lengan,
                    b.status_gizi, b.bulan_pencatatan, b.umur_bulan,
                    p.nama_posyandu, t.tahun
             FROM stunting s
             JOIN balita b ON s.id_balita = b.id
             JOIN posyandu p ON b.id_posyandu = p.id
             JOIN tahun t ON b.id_tahun = t.id
             WHERE s.status_verifikasi = 'terverifikasi'
             ORDER BY s.created_at DESC"
        );

        require_once ROOT . '/app/views/kelurahan/stunting.php';
        break;

    // SETUJUI DATA STUNTING
    case 'setujui_stunting':
        $id = $_GET['id'];
        $query = "UPDATE stunting SET status_verifikasi = 'disetujui' WHERE id = '$id'";

        if (mysqli_query($conn, $query)) {
            header("Location: index.php?page=kelurahan&act=stunting&msg=setujui_sukses");
        } else {
            header("Location: index.php?page=kelurahan&act=stunting&msg=gagal");
        }
        exit;
        break;

    // TOLAK DATA STUNTING (kembalikan ke pending)
    case 'tolak_stunting':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id      = $_POST['id'];
            $catatan = trim($_POST['catatan']);

            $query = "UPDATE stunting SET
                        status_verifikasi = 'pending',
                        catatan = '$catatan'
                      WHERE id = '$id'";

            if (mysqli_query($conn, $query)) {
                header("Location: index.php?page=kelurahan&act=stunting&msg=tolak_sukses");
            } else {
                header("Location: index.php?page=kelurahan&act=stunting&msg=gagal");
            }
            exit;
        }
        break;

    // ----------------------------------------
    // KELOLA LAPORAN
    // ----------------------------------------
    case 'laporan':
        $bulan_list = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $data_laporan = mysqli_query($conn,
            "SELECT l.*, p.nama_posyandu, t.tahun
             FROM laporan l
             JOIN posyandu p ON l.id_posyandu = p.id
             JOIN tahun t ON l.id_tahun = t.id
             ORDER BY l.created_at DESC"
        );
        $data_posyandu = mysqli_query($conn, "SELECT * FROM posyandu ORDER BY nama_posyandu");
        $data_tahun    = mysqli_query($conn, "SELECT * FROM tahun ORDER BY tahun DESC");

        require_once ROOT . '/app/views/kelurahan/laporan.php';
        break;

    case 'simpan_laporan':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $judul       = trim($_POST['judul']);
            $isi         = trim($_POST['isi']);
            $id_posyandu = $_POST['id_posyandu'];
            $id_tahun    = $_POST['id_tahun'];
            $bulan       = (int) ($_POST['bulan_pencatatan'] ?? 0);

            $query = "INSERT INTO laporan (judul, isi, id_posyandu, id_tahun, bulan_pencatatan, status)
                      VALUES ('$judul', '$isi', '$id_posyandu', '$id_tahun', '$bulan', 'dikirim')";

            if (mysqli_query($conn, $query)) {
                header("Location: index.php?page=kelurahan&act=laporan&msg=kirim_sukses");
            } else {
                header("Location: index.php?page=kelurahan&act=laporan&msg=gagal");
            }
            exit;
        }
        break;

    case 'hapus_laporan':
        $id = $_GET['id'];
        mysqli_query($conn, "DELETE FROM laporan WHERE id = '$id'");
        header("Location: index.php?page=kelurahan&act=laporan&msg=hapus_sukses");
        exit;
        break;

    // GENERATE LAPORAN OTOMATIS
    case 'generate_laporan':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_posyandu = $_POST['id_posyandu'];
            $id_tahun    = $_POST['id_tahun'];
            $bulan       = (int) ($_POST['bulan_pencatatan'] ?? 0);
            $bulan_list = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $nama_bulan = $bulan_list[$bulan] ?? '';

            $info_posyandu = mysqli_fetch_assoc(
                mysqli_query($conn, "SELECT * FROM posyandu WHERE id = '$id_posyandu'")
            );
            $info_tahun = mysqli_fetch_assoc(
                mysqli_query($conn, "SELECT * FROM tahun WHERE id = '$id_tahun'")
            );

            // Ambil rekap dari tabel balita langsung
            $rekap = mysqli_query($conn,
                "SELECT status_gizi, COUNT(*) as total
                 FROM balita
                 WHERE id_posyandu = '$id_posyandu'
                 AND id_tahun = '$id_tahun'
                 AND bulan_pencatatan = '$bulan'
                 GROUP BY status_gizi"
            );

            $normal = 0; $beresiko = 0; $stunting_ct = 0;
            if ($rekap) {
                while ($r = mysqli_fetch_assoc($rekap)) {
                    if ($r['status_gizi'] === 'normal')   $normal      = $r['total'];
                    if ($r['status_gizi'] === 'beresiko') $beresiko    = $r['total'];
                    if ($r['status_gizi'] === 'stunting') $stunting_ct = $r['total'];
                }
            }

            $total         = $normal + $beresiko + $stunting_ct;
            $nama_posyandu = $info_posyandu['nama_posyandu'];
            $nama_dusun    = $info_posyandu['nama_dusun'];
            $tahun         = $info_tahun['tahun'];

            $isi  = "LAPORAN DATA STUNTING\n";
            $isi .= "Posyandu : $nama_posyandu ($nama_dusun)\n";
            $isi .= "Bulan    : $nama_bulan\n";
            $isi .= "Tahun    : $tahun\n";
            $isi .= "========================================\n\n";
            $isi .= "REKAPITULASI DATA:\n";
            $isi .= "- Total Balita  : $total anak\n";
            $isi .= "- Normal        : $normal anak\n";
            $isi .= "- Berisiko      : $beresiko anak\n";
            $isi .= "- Stunting      : $stunting_ct anak\n\n";
            $isi .= "INSTRUKSI PENANGANAN:\n";

            if ($stunting_ct >= 4) {
                $isi .= "PERHATIAN TINGGI: Wilayah MERAH.\n";
                $isi .= "Lakukan pendampingan intensif $stunting_ct balita stunting.\n";
                $isi .= "Koordinasikan dengan puskesmas segera.\n";
            } elseif ($beresiko >= 2) {
                $isi .= "PERHATIAN: Wilayah KUNING.\n";
                $isi .= "Pantau $beresiko balita berisiko rutin setiap bulan.\n";
                $isi .= "Berikan edukasi gizi kepada orang tua.\n";
            } else {
                $isi .= "Wilayah HIJAU (Normal).\n";
                $isi .= "Pertahankan program posyandu yang berjalan.\n";
                $isi .= "Tetap lakukan pemantauan rutin.\n";
            }

            $judul = "Laporan Stunting - $nama_posyandu - $nama_bulan $tahun";
            $query = "INSERT INTO laporan (judul, isi, id_posyandu, id_tahun, bulan_pencatatan, status)
                      VALUES ('$judul', '$isi', '$id_posyandu', '$id_tahun', '$bulan', 'dikirim')";

            if (mysqli_query($conn, $query)) {
                header("Location: index.php?page=kelurahan&act=laporan&msg=kirim_sukses");
            } else {
                header("Location: index.php?page=kelurahan&act=laporan&msg=gagal");
            }
            exit;
        }
        break;

    // ----------------------------------------
    // CETAK LAPORAN
    // ----------------------------------------
    case 'cetak':
        $id = $_GET['id'];

        // Ambil data laporan
        $laporan = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT l.*, p.nama_posyandu, p.nama_dusun, t.tahun
             FROM laporan l
             JOIN posyandu p ON l.id_posyandu = p.id
             JOIN tahun t ON l.id_tahun = t.id
             WHERE l.id = '$id'"
        ));

        if (!$laporan) {
            header("Location: index.php?page=kelurahan&act=laporan&msg=gagal");
            exit;
        }

        $id_tahun_lap = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT id FROM tahun WHERE tahun = '{$laporan['tahun']}'"
        ))['id'];

        // Rekap dari tabel balita berdasarkan posyandu & tahun laporan
        $id_posyandu_lap = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT id FROM posyandu WHERE nama_posyandu = '{$laporan['nama_posyandu']}'")
        )['id'] ?? 0;

        $rekap_query = mysqli_query($conn,
            "SELECT
                COUNT(CASE WHEN status_gizi = 'normal'   THEN 1 END) as normal,
                COUNT(CASE WHEN status_gizi = 'beresiko' THEN 1 END) as beresiko,
                COUNT(CASE WHEN status_gizi = 'stunting' THEN 1 END) as stunting,
                COUNT(*) as total
             FROM balita
             WHERE id_tahun = '$id_tahun_lap'
             AND id_posyandu = '$id_posyandu_lap'
             AND bulan_pencatatan = '{$laporan['bulan_pencatatan']}'"
        );
        $rekap = mysqli_fetch_assoc($rekap_query);
        $rekap['total']    = $rekap['total']    ?? 0;
        $rekap['normal']   = $rekap['normal']   ?? 0;
        $rekap['beresiko'] = $rekap['beresiko'] ?? 0;
        $rekap['stunting'] = $rekap['stunting'] ?? 0;

        // Ambil daftar balita per posyandu & tahun (yang bukan normal)
        $data_balita_stunting = mysqli_query($conn,
            "SELECT nama_bayi, nama_ortu, jenis_kelamin,
                    tanggal_lahir, umur_bulan,
                    berat_badan, tinggi_badan, status_gizi
             FROM balita
             WHERE id_tahun = '$id_tahun_lap'
             AND id_posyandu = '$id_posyandu_lap'
             AND bulan_pencatatan = '{$laporan['bulan_pencatatan']}'
             AND status_gizi != 'normal'
             ORDER BY status_gizi DESC"
        );

        require_once ROOT . '/app/views/kelurahan/cetak_laporan.php';
        break;

    // ----------------------------------------
    // AJAX: Cek jumlah data stunting terverifikasi
    // ----------------------------------------
    case 'cek_stunting':
        header('Content-Type: application/json');
        $total = mysqli_fetch_assoc(
            mysqli_query($conn,
                "SELECT COUNT(*) as total FROM stunting
                 WHERE status_verifikasi = 'terverifikasi'"
            )
        )['total'];
        echo json_encode(['total' => (int)$total]);
        exit;
        break;

    default:
        require_once ROOT . '/app/views/kelurahan/dashboard.php';
        break;
}
?>
