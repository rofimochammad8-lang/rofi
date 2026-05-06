<?php
// ============================================
// FILE: app/controllers/BalitaController.php
// ============================================

require_once ROOT . '/app/config/database.php';
require_once ROOT . '/app/config/status_gizi.php';

function redirectBalitaFormError($message, $filter_tahun = '') {
    $redirect = "Location: index.php?page=kader&act=balita&msg=invalid_input&error=" . urlencode($message);
    if ($filter_tahun !== '') {
        $redirect .= "&tahun=" . urlencode($filter_tahun);
    }
    header($redirect);
    exit;
}

$act = isset($_GET['act']) ? $_GET['act'] : 'dashboard';

// Ambil id_posyandu dari session kader
$id_posyandu_kader = $_SESSION['user']['id_posyandu'] ?? null;

// Jika kader belum punya posyandu, redirect dengan pesan
if ($id_posyandu_kader === null && $act !== 'dashboard') {
    header("Location: index.php?page=kader&act=dashboard");
    exit;
}

switch ($act) {

    // ----------------------------------------
    // DASHBOARD
    // ----------------------------------------
    case 'dashboard':
        $total_balita = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) as total FROM balita WHERE id_posyandu = '$id_posyandu_kader'")
        )['total'];

        $total_normal = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) as total FROM balita WHERE status_gizi = 'normal' AND id_posyandu = '$id_posyandu_kader'")
        )['total'];

        $total_beresiko = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) as total FROM balita WHERE status_gizi = 'beresiko' AND id_posyandu = '$id_posyandu_kader'")
        )['total'];

        $total_stunting = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) as total FROM balita WHERE status_gizi = 'stunting' AND id_posyandu = '$id_posyandu_kader'")
        )['total'];

        $total_laporan = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE status = 'dikirim' AND id_posyandu = '$id_posyandu_kader'")
        )['total'];

        $balita_terbaru = mysqli_query($conn,
            "SELECT b.*, p.nama_posyandu, t.tahun
             FROM balita b
             JOIN posyandu p ON b.id_posyandu = p.id
             JOIN tahun t ON b.id_tahun = t.id
             WHERE b.id_posyandu = '$id_posyandu_kader'
             ORDER BY b.created_at DESC LIMIT 5"
        );

        require_once ROOT . '/app/views/kader/dashboard.php';
        break;

    // ----------------------------------------
    // LIHAT DATA BALITA
    // ----------------------------------------
    case 'balita':
        $filter_tahun      = isset($_GET['tahun'])     ? trim($_GET['tahun'])     : '';
        $tbl_tahun         = isset($_GET['tbl_tahun']) ? trim($_GET['tbl_tahun']) : '';

        // Ambil nama posyandu kader yang login
        $posyandu_kader = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT * FROM posyandu WHERE id = '$id_posyandu_kader'")
        );

        // Filter tabel hanya data posyandu kader yang login
        $where_tabel = "WHERE b.id_posyandu = '$id_posyandu_kader'";
        if ($tbl_tahun) {
            $where_tabel .= " AND CAST(t.tahun AS CHAR) = '" . mysqli_real_escape_string($conn, $tbl_tahun) . "'";
        }

        $data_balita = mysqli_query($conn,
            "SELECT b.*, p.nama_posyandu, t.tahun,
                    sp.id as stunting_pending_id,
                    sp.status_verifikasi as status_verifikasi_revisi,
                    sp.catatan as catatan_revisi
             FROM balita b
             JOIN posyandu p ON b.id_posyandu = p.id
             JOIN tahun t ON b.id_tahun = t.id
             LEFT JOIN (
                 SELECT s1.*
                 FROM stunting s1
                 INNER JOIN (
                     SELECT id_balita, MAX(id) AS latest_id
                     FROM stunting
                     WHERE status_verifikasi = 'pending'
                     GROUP BY id_balita
                 ) s2 ON s1.id = s2.latest_id
             ) sp ON sp.id_balita = b.id
             $where_tabel
             ORDER BY b.created_at DESC"
        );

        $total_revisi = mysqli_fetch_assoc(
            mysqli_query($conn,
                "SELECT COUNT(*) as total
                 FROM balita b
                 INNER JOIN (
                     SELECT id_balita, MAX(id) AS latest_id
                     FROM stunting
                     WHERE status_verifikasi = 'pending'
                     GROUP BY id_balita
                 ) sp ON sp.id_balita = b.id
                 WHERE b.id_posyandu = '$id_posyandu_kader'"
            )
        )['total'];

        $data_tahun = mysqli_query($conn, "SELECT * FROM tahun ORDER BY tahun DESC");

        require_once ROOT . '/app/views/kader/balita.php';
        break;

    // ----------------------------------------
    // SIMPAN DATA BALITA BARU
    // ----------------------------------------
    case 'simpan_balita':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_bayi        = trim($_POST['nama_bayi']);
            $nama_ortu        = trim($_POST['nama_ortu']);
            $nik_ortu         = trim($_POST['nik_ortu'] ?? '');
            $tgl_lahir        = $_POST['tanggal_lahir'];
            $umur_bulan       = (int)$_POST['umur_bulan'];
            $jk               = $_POST['jenis_kelamin'];
            $berat_badan      = (float)$_POST['berat_badan'];
            $tinggi_badan     = (float)$_POST['tinggi_badan'];
            $lingkar_kepala   = (isset($_POST['lingkar_kepala']) && $_POST['lingkar_kepala'] !== '') ? (float)$_POST['lingkar_kepala'] : null;
            $lingkar_lengan   = (isset($_POST['lingkar_lengan']) && $_POST['lingkar_lengan'] !== '') ? (float)$_POST['lingkar_lengan'] : null;
            $bulan_pencatatan = (int)$_POST['bulan_pencatatan'];
            $filter_tahun     = $_POST['filter_tahun'] ?? '';
            $tahun_input      = (int)$filter_tahun;

            if ($nik_ortu === '') {
                redirectBalitaFormError('NIK orang tua wajib diisi.', $filter_tahun);
            }

            if ($lingkar_kepala === null || $lingkar_kepala <= 0) {
                redirectBalitaFormError('Lingkar kepala wajib diisi dengan angka lebih dari 0.', $filter_tahun);
            }

            if ($lingkar_lengan === null || $lingkar_lengan <= 0) {
                redirectBalitaFormError('Lingkar lengan wajib diisi dengan angka lebih dari 0.', $filter_tahun);
            }

            // Hitung status gizi otomatis
            $status_gizi = hitungStatusGizi(
                $jk, $umur_bulan, $tinggi_badan,
                $berat_badan, $lingkar_lengan, $lingkar_kepala
            );

            if ($status_gizi === null) {
                redirectBalitaFormError('Status gizi tidak bisa dihitung. Pastikan semua indikator terisi lengkap.', $filter_tahun);
            }

            // Cari atau buat tahun otomatis
            $cek_tahun = mysqli_fetch_assoc(
                mysqli_query($conn, "SELECT id FROM tahun WHERE tahun = '$tahun_input'")
            );
            if ($cek_tahun) {
                $id_tahun = $cek_tahun['id'];
            } else {
                mysqli_query($conn, "INSERT INTO tahun (tahun) VALUES ('$tahun_input')");
                $id_tahun = mysqli_insert_id($conn);
            }

            $lk = $lingkar_kepala ? "'$lingkar_kepala'" : "NULL";
            $ll = $lingkar_lengan ? "'$lingkar_lengan'" : "NULL";

            $id_posyandu = $_SESSION['user']['id_posyandu'];

            $query = "INSERT INTO balita
                        (nama_bayi, nama_ortu, nik_ortu, tanggal_lahir, umur_bulan, jenis_kelamin,
                         berat_badan, tinggi_badan, lingkar_kepala, lingkar_lengan,
                         status_gizi, bulan_pencatatan, id_posyandu, id_tahun)
                      VALUES
                        ('$nama_bayi', '$nama_ortu', '$nik_ortu', '$tgl_lahir', '$umur_bulan', '$jk',
                         '$berat_badan', '$tinggi_badan', $lk, $ll,
                         '$status_gizi', '$bulan_pencatatan', '$id_posyandu', '$id_tahun')";

            $redirect = "index.php?page=kader&act=balita&msg=tambah_sukses&status=$status_gizi";
            if ($filter_tahun) $redirect .= "&tahun=$filter_tahun";

            if (mysqli_query($conn, $query)) {
                header("Location: $redirect");
            } else {
                header("Location: index.php?page=kader&act=balita&msg=gagal");
            }
            exit;
        }
        break;

    // ----------------------------------------
    // FORM EDIT DATA BALITA
    // ----------------------------------------
    case 'edit_balita':
        $id           = $_GET['id'];
        $filter_tahun = $_GET['tahun'] ?? '';

        $balita = mysqli_fetch_assoc(
            mysqli_query($conn,
                "SELECT b.*, t.tahun
                 FROM balita b
                 JOIN tahun t ON b.id_tahun = t.id
                 WHERE b.id = '$id'")
        );

        require_once ROOT . '/app/views/kader/edit_balita.php';
        break;

    // ----------------------------------------
    // UPDATE DATA BALITA
    // ----------------------------------------
    case 'update_balita':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id               = $_POST['id'];
            $nama_bayi        = trim($_POST['nama_bayi']);
            $nama_ortu        = trim($_POST['nama_ortu']);
            $nik_ortu         = trim($_POST['nik_ortu'] ?? '');
            $tgl_lahir        = $_POST['tanggal_lahir'];
            $umur_bulan       = (int)$_POST['umur_bulan'];
            $jk               = $_POST['jenis_kelamin'];
            $berat_badan      = (float)$_POST['berat_badan'];
            $tinggi_badan     = (float)$_POST['tinggi_badan'];
            $lingkar_kepala   = (isset($_POST['lingkar_kepala']) && $_POST['lingkar_kepala'] !== '') ? (float)$_POST['lingkar_kepala'] : null;
            $lingkar_lengan   = (isset($_POST['lingkar_lengan']) && $_POST['lingkar_lengan'] !== '') ? (float)$_POST['lingkar_lengan'] : null;
            $bulan_pencatatan = (int)$_POST['bulan_pencatatan'];
            $filter_tahun     = $_POST['filter_tahun'] ?? '';

            if ($nik_ortu === '') {
                $redirect = "Location: index.php?page=kader&act=edit_balita&id=$id&msg=invalid_input&error=" .
                    urlencode('NIK orang tua wajib diisi.');
                if ($filter_tahun !== '') {
                    $redirect .= "&tahun=" . urlencode($filter_tahun);
                }
                header($redirect);
                exit;
            }

            if ($lingkar_kepala === null || $lingkar_kepala <= 0) {
                $redirect = "Location: index.php?page=kader&act=edit_balita&id=$id&msg=invalid_input&error=" .
                    urlencode('Lingkar kepala wajib diisi dengan angka lebih dari 0.');
                if ($filter_tahun !== '') {
                    $redirect .= "&tahun=" . urlencode($filter_tahun);
                }
                header($redirect);
                exit;
            }

            if ($lingkar_lengan === null || $lingkar_lengan <= 0) {
                $redirect = "Location: index.php?page=kader&act=edit_balita&id=$id&msg=invalid_input&error=" .
                    urlencode('Lingkar lengan wajib diisi dengan angka lebih dari 0.');
                if ($filter_tahun !== '') {
                    $redirect .= "&tahun=" . urlencode($filter_tahun);
                }
                header($redirect);
                exit;
            }

            // Hitung status gizi otomatis
            $status_gizi = hitungStatusGizi(
                $jk, $umur_bulan, $tinggi_badan,
                $berat_badan, $lingkar_lengan, $lingkar_kepala
            );

            if ($status_gizi === null) {
                $redirect = "Location: index.php?page=kader&act=edit_balita&id=$id&msg=invalid_input&error=" .
                    urlencode('Status gizi tidak bisa dihitung. Pastikan semua indikator terisi lengkap.');
                if ($filter_tahun !== '') {
                    $redirect .= "&tahun=" . urlencode($filter_tahun);
                }
                header($redirect);
                exit;
            }

            $lk = $lingkar_kepala ? "'$lingkar_kepala'" : "NULL";
            $ll = $lingkar_lengan ? "'$lingkar_lengan'" : "NULL";

            $query = "UPDATE balita SET
                        nama_bayi='$nama_bayi', nama_ortu='$nama_ortu',
                        nik_ortu='$nik_ortu',
                        tanggal_lahir='$tgl_lahir', umur_bulan='$umur_bulan',
                        jenis_kelamin='$jk', berat_badan='$berat_badan',
                        tinggi_badan='$tinggi_badan', lingkar_kepala=$lk,
                        lingkar_lengan=$ll, status_gizi='$status_gizi',
                        bulan_pencatatan='$bulan_pencatatan'
                      WHERE id='$id'";

            $redirect = "index.php?page=kader&act=balita&msg=edit_sukses&status=$status_gizi";
            if ($filter_tahun) $redirect .= "&tahun=$filter_tahun";

            if (mysqli_query($conn, $query)) {
                mysqli_query($conn, "DELETE FROM stunting WHERE id_balita = '$id' AND status_verifikasi = 'pending'");
                header("Location: $redirect");
            } else {
                header("Location: index.php?page=kader&act=balita&msg=gagal");
            }
            exit;
        }
        break;

    // ----------------------------------------
    // HAPUS DATA BALITA
    // ----------------------------------------
    case 'hapus_balita':
        $id           = $_GET['id'];
        $filter_tahun = $_GET['tahun'] ?? '';

        $redirect = "index.php?page=kader&act=balita";
        if ($filter_tahun) $redirect .= "&tahun=$filter_tahun";

        // Hapus data stunting dulu jika ada (FK constraint)
        mysqli_query($conn, "DELETE FROM stunting WHERE id_balita = '$id'");
        // Baru hapus balita
        mysqli_query($conn, "DELETE FROM balita WHERE id = '$id'");

        header("Location: $redirect&msg=hapus_sukses");
        exit;
        break;

    // ----------------------------------------
    // LAPORAN MASUK DARI KELURAHAN
    // ----------------------------------------
    case 'laporan':
        $data_laporan = mysqli_query($conn,
            "SELECT l.*, p.nama_posyandu, t.tahun
             FROM laporan l
             JOIN posyandu p ON l.id_posyandu = p.id
             JOIN tahun t ON l.id_tahun = t.id
             WHERE l.id_posyandu = '$id_posyandu_kader'
             ORDER BY l.created_at DESC"
        );

        // Tandai laporan posyandu ini sudah dibaca
        mysqli_query($conn,
            "UPDATE laporan SET status = 'dibaca'
             WHERE status = 'dikirim' AND id_posyandu = '$id_posyandu_kader'"
        );

        require_once ROOT . '/app/views/kader/laporan.php';
        break;

    default:
        require_once ROOT . '/app/views/kader/dashboard.php';
        break;
}
?>
