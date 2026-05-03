<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SIG Stunting Desa Sumberwaru</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #1a6b3a 0%, #2d9e5f 50%, #1a6b3a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 24px;
        }

        .login-logo .icon-wrap {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #1a6b3a, #2d9e5f);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }

        .login-logo i {
            font-size: 32px;
            color: white;
        }

        .login-logo h5 {
            font-weight: 700;
            color: #1a6b3a;
            margin: 0;
            font-size: 16px;
        }

        .login-logo p {
            color: #888;
            font-size: 13px;
            margin: 4px 0 0;
        }

        .form-label {
            font-weight: 600;
            font-size: 14px;
            color: #333;
        }

        .form-control {
            border-radius: 8px;
            border: 1.5px solid #ddd;
            padding: 10px 14px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #2d9e5f;
            box-shadow: 0 0 0 3px rgba(45,158,95,0.15);
        }

        .input-group-text {
            background: #f8f9fa;
            border: 1.5px solid #ddd;
            border-radius: 8px 0 0 8px;
            color: #666;
        }

        .btn-login {
            background: linear-gradient(135deg, #1a6b3a, #2d9e5f);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: 0.5px;
            width: 100%;
            color: white;
            transition: opacity 0.2s;
        }

        .btn-login:hover {
            opacity: 0.9;
            color: white;
        }

        .alert-danger {
            border-radius: 8px;
            font-size: 14px;
            border: none;
            background: #fff0f0;
            color: #c0392b;
        }

        .role-info {
            background: #f0faf4;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 20px;
            font-size: 12px;
            color: #555;
        }

        .role-info span {
            display: block;
            margin-bottom: 3px;
        }

        .role-info b {
            color: #1a6b3a;
        }
    </style>
</head>
<body>

<div class="login-card">

    <!-- Logo & Judul -->
    <div class="login-logo">
        <div class="icon-wrap">
            <i class="bi bi-geo-alt-fill"></i>
        </div>
        <h5>SIG Visualisasi Stunting</h5>
        <p>Desa Sumberwaru</p>
    </div>

    <!-- Pesan Error -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger mb-3">
            <i class="bi bi-exclamation-circle me-2"></i><?= $error ?>
        </div>
    <?php endif; ?>

    <!-- Form Login -->
    <form method="POST" action="index.php?page=auth&act=login">

        <div class="mb-3">
            <label class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text"
                       name="username"
                       class="form-control"
                       placeholder="Masukkan username"
                       value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                       required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password"
                       name="password"
                       class="form-control"
                       placeholder="Masukkan password"
                       required>
            </div>
        </div>

        <button type="submit" class="btn btn-login">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
        </button>

    </form>



</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>