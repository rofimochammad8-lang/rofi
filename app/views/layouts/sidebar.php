<?php
// ============================================
// FILE: app/views/layouts/sidebar.php
// Fungsi: Sidebar navigasi sesuai role user
// ============================================

$role = $_SESSION['user']['role'];
$nama = $_SESSION['user']['nama'];
?>

<div class="sidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i class="bi bi-geo-alt-fill text-white fs-5"></i>
        </div>
        <h6>SIG Visualisasi<br>Stunting</h6>
        <p>Desa Sumberwaru</p>
    </div>

    <!-- Menu sesuai role -->
    <div class="sidebar-menu">

        <?php if ($role === 'kader'):
            // Ambil nama posyandu kader
            $pos = mysqli_fetch_assoc(
                mysqli_query($conn,
                    "SELECT nama_posyandu FROM posyandu WHERE id = '{$_SESSION['user']['id_posyandu']}'"
                )
            );
        ?>
            <div class="menu-label">Menu Kader</div>
            <div style="padding:6px 20px 10px;font-size:12px;color:rgba(255,255,255,0.5);">
                <i class="bi bi-house-heart me-1"></i>
                <?= htmlspecialchars($pos['nama_posyandu'] ?? '') ?>
            </div>
            <a href="index.php?page=kader&act=dashboard"
               class="<?= ($act === 'dashboard') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="index.php?page=kader&act=balita"
               class="<?= ($act === 'balita' || $act === 'edit_balita') ? 'active' : '' ?>">
                <i class="bi bi-people"></i> Data Balita
            </a>

            <a href="index.php?page=kader&act=laporan"
               class="<?= ($act === 'laporan') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-text"></i> Laporan Masuk
            </a>

        <?php elseif ($role === 'kpm'): ?>
            <div class="menu-label">Menu KPM</div>
            <a href="index.php?page=kpm&act=dashboard"
               class="<?= ($act === 'dashboard') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="index.php?page=kpm&act=verifikasi"
               class="<?= ($act === 'verifikasi') ? 'active' : '' ?>">
                <i class="bi bi-patch-check"></i> Verifikasi Data
            </a>
            <a href="index.php?page=peta&act=index"
               class="<?= (isset($page) && $page === 'peta') ? 'active' : '' ?>">
                <i class="bi bi-map"></i> Peta Stunting
            </a>

        <?php elseif ($role === 'kelurahan'): ?>
            <div class="menu-label">Menu Kelurahan</div>
            <a href="index.php?page=kelurahan&act=dashboard"
               class="<?= ($act === 'dashboard') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="index.php?page=kelurahan&act=posyandu"
               class="<?= ($act === 'posyandu' || $act === 'edit_posyandu') ? 'active' : '' ?>">
                <i class="bi bi-house-heart"></i> Data Posyandu
            </a>
            <a href="index.php?page=kelurahan&act=stunting"
               class="<?= ($act === 'stunting') ? 'active' : '' ?>">
                <i class="bi bi-clipboard2-pulse"></i> Kelola Stunting
            </a>
            <a href="index.php?page=peta&act=index"
               class="<?= (isset($page) && $page === 'peta') ? 'active' : '' ?>">
                <i class="bi bi-map"></i> Peta Stunting
            </a>
            <a href="index.php?page=kelurahan&act=laporan"
               class="<?= ($act === 'laporan') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-plus"></i> Kelola Laporan
            </a>
        <?php endif; ?>

    </div>

    <!-- User Info & Logout -->
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <div class="name"><?= htmlspecialchars($nama) ?></div>
                <span class="role-badge"><?= ucfirst($role) ?></span>
            </div>
        </div>
        <a href="index.php?page=auth&act=logout" class="btn-logout">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
    </div>

</div>