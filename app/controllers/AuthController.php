<?php
// ============================================
// FILE: app/controllers/AuthController.php
// ============================================

require_once ROOT . '/app/config/database.php';

$act = isset($_GET['act']) ? $_GET['act'] : 'login';

switch ($act) {

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = trim($_POST['username']);
            $password = md5(trim($_POST['password']));

            $result = mysqli_query($conn,
                "SELECT * FROM users WHERE username = '$username' AND password = '$password'"
            );

            if (mysqli_num_rows($result) === 1) {
                $user = mysqli_fetch_assoc($result);

                $_SESSION['user'] = [
                    'id'          => $user['id'],
                    'nama'        => $user['nama'],
                    'role'        => $user['role'],
                    'id_posyandu' => $user['id_posyandu'],
                ];

                switch ($user['role']) {
                    case 'kader':
                        header("Location: index.php?page=kader&act=dashboard");
                        break;
                    case 'kpm':
                        header("Location: index.php?page=kpm&act=dashboard");
                        break;
                    case 'kelurahan':
                        header("Location: index.php?page=kelurahan&act=dashboard");
                        break;
                }
                exit;

            } else {
                $error = "Username atau password salah!";
            }
        }

        require_once ROOT . '/app/views/auth/login.php';
        break;

    case 'logout':
        $_SESSION = [];
        session_destroy();
        header("Location: index.php?page=auth&act=login");
        exit;
        break;
}
?>