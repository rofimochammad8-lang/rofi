<?php
// ============================================
// FILE: app/controllers/StuntingController.php
// Fungsi: Menangani semua halaman role KPM
// Fitur: Dashboard, verifikasi data, lihat laporan, peta
// ============================================

require_once ROOT . '/app/config/database.php';

$act = isset($_GET['act']) ? $_GET['act'] : 'dashboard';

switch ($act) {

    // ----------------------------------------
    // DASHBOARD KPM
    // ----------------------------------------
    case 'dashboard':
        // Bersihkan data stunting orphan dulu
        mysqli_query($conn, "DELETE FROM stunting WHERE id_balita NOT IN (SELECT id FROM balita)");

        // Hitung total balita yang benar-benar ada
        $total_balita = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) as total FROM balita")
        )['total'];

        // Belum diverifikasi = balita yang belum ada di tabel stunting
        $total_pending = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) as total FROM balita b
                                 WHERE NOT EXISTS (
                                     SELECT 1 FROM stunting s WHERE s.id_balita = b.id
                                 )")
        )['total'];

        $total_terverifikasi = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) as total FROM stunting
                                 WHERE status_verifikasi = 'terverifikasi'")
        )['total'];

        $total_disetujui = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) as total FROM stunting
                                 WHERE status_verifikasi = 'disetujui'")
        )['total'];

        // Data balita yang belum diverifikasi (belum ada di stunting)
        $data_pending = mysqli_query($conn,
            "SELECT b.*, p.nama_posyandu, t.tahun
             FROM balita b
             JOIN posyandu p ON b.id_posyandu = p.id
             JOIN tahun t ON b.id_tahun = t.id
             WHERE NOT EXISTS (
                 SELECT 1 FROM stunting s WHERE s.id_balita = b.id
             )
             ORDER BY b.created_at DESC LIMIT 5"
        );

        require_once ROOT . '/app/views/kpm/dashboard.php';
        break;

    // ----------------------------------------
    // HALAMAN VERIFIKASI DATA STUNTING
    // ----------------------------------------
    case 'verifikasi':
        // Tampilkan balita yang belum ada di tabel stunting (belum diverifikasi)
        $data_stunting = mysqli_query($conn,
            "SELECT b.*, p.nama_posyandu, p.nama_dusun, t.tahun
             FROM balita b
             JOIN posyandu p ON b.id_posyandu = p.id
             JOIN tahun t ON b.id_tahun = t.id
             WHERE NOT EXISTS (
                 SELECT 1 FROM stunting s WHERE s.id_balita = b.id
             )
             ORDER BY b.created_at DESC"
        );

        require_once ROOT . '/app/views/kpm/verifikasi.php';
        break;

    // ----------------------------------------
    // PROSES VERIFIKASI (SETUJUI)
    // ----------------------------------------
    case 'setujui_verifikasi':
        // id di sini adalah id_balita (bukan id stunting)
        $id_balita = $_GET['id'];

        // Ambil id_tahun dari balita
        $balita_data = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT id_tahun FROM balita WHERE id = '$id_balita'")
        );
        $id_tahun = $balita_data['id_tahun'];

        // Insert ke tabel stunting dengan status terverifikasi
        $query = "INSERT INTO stunting (id_balita, status_verifikasi, id_tahun)
                  VALUES ('$id_balita', 'terverifikasi', '$id_tahun')";

        if (mysqli_query($conn, $query)) {
            header("Location: index.php?page=kpm&act=verifikasi&msg=verifikasi_sukses");
        } else {
            header("Location: index.php?page=kpm&act=verifikasi&msg=gagal");
        }
        exit;
        break;

    // ----------------------------------------
    // PROSES TOLAK VERIFIKASI
    // ----------------------------------------
    case 'tolak_verifikasi':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_balita = $_POST['id'];
            $catatan   = trim($_POST['catatan']);

            // Simpan catatan penolakan ke tabel balita sementara
            // dengan insert stunting status pending + catatan
            $balita_data = mysqli_fetch_assoc(
                mysqli_query($conn, "SELECT id_tahun FROM balita WHERE id = '$id_balita'")
            );
            $id_tahun = $balita_data['id_tahun'];

            $query = "INSERT INTO stunting (id_balita, status_verifikasi, catatan, id_tahun)
                      VALUES ('$id_balita', 'pending', '$catatan', '$id_tahun')";

            if (mysqli_query($conn, $query)) {
                header("Location: index.php?page=kpm&act=verifikasi&msg=tolak_sukses");
            } else {
                header("Location: index.php?page=kpm&act=verifikasi&msg=gagal");
            }
            exit;
        }
        break;

    // ----------------------------------------
    // LIHAT LAPORAN (KPM)
    // ----------------------------------------
    case 'laporan':
        $data_laporan = mysqli_query($conn,
            "SELECT l.*, p.nama_posyandu, t.tahun
             FROM laporan l
             JOIN posyandu p ON l.id_posyandu = p.id
             JOIN tahun t ON l.id_tahun = t.id
             ORDER BY l.created_at DESC"
        );

        require_once ROOT . '/app/views/kpm/laporan.php';
        break;

    default:
        require_once ROOT . '/app/views/kpm/dashboard.php';
        break;
}
?>