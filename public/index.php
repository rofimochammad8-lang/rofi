<?php
// ============================================
// FILE: public/index.php
// ============================================

session_start();

define('ROOT', dirname(__DIR__));

$page = isset($_GET['page']) ? $_GET['page'] : 'auth';
$act  = isset($_GET['act'])  ? $_GET['act']  : 'login';

// Cek login
if (!isset($_SESSION['user']) && $page !== 'auth') {
    header("Location: index.php?page=auth&act=login");
    exit;
}

// Routing
switch ($page) {

    case 'auth':
        require_once ROOT . '/app/controllers/AuthController.php';
        break;

    case 'kader':
        if ($_SESSION['user']['role'] !== 'kader') {
            header("Location: index.php?page=auth&act=login");
            exit;
        }
        require_once ROOT . '/app/controllers/BalitaController.php';
        break;

    case 'kpm':
        if ($_SESSION['user']['role'] !== 'kpm') {
            header("Location: index.php?page=auth&act=login");
            exit;
        }
        require_once ROOT . '/app/controllers/StuntingController.php';
        break;

    case 'kelurahan':
        if ($_SESSION['user']['role'] !== 'kelurahan') {
            header("Location: index.php?page=auth&act=login");
            exit;
        }
        require_once ROOT . '/app/controllers/LaporanController.php';
        break;

    case 'peta':
        if (!isset($_SESSION['user']) ||
            !in_array($_SESSION['user']['role'], ['kpm', 'kelurahan'])) {
            header("Location: index.php?page=auth&act=login");
            exit;
        }
        require_once ROOT . '/app/controllers/PetaController.php';
        break;

    default:
        echo "<h2 style='font-family:sans-serif;text-align:center;
                         margin-top:100px;color:#888;'>
                404 - Halaman tidak ditemukan
              </h2>";
        break;
}
?>