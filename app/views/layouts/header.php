<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIG Stunting — Desa Sumberwaru</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
            margin: 0;
        }

        /* ======== SIDEBAR ======== */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #1a6b3a 0%, #145530 100%);
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand .brand-icon {
            width: 42px;
            height: 42px;
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
        }

        .sidebar-brand h6 {
            color: #fff;
            font-weight: 700;
            font-size: 13px;
            margin: 0;
            line-height: 1.4;
        }

        .sidebar-brand p {
            color: rgba(255,255,255,0.6);
            font-size: 11px;
            margin: 0;
        }

        .sidebar-menu {
            padding: 16px 0;
            flex: 1;
            overflow-y: auto;
        }

        .menu-label {
            color: rgba(255,255,255,0.4);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 8px 20px 4px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left-color: #6ee49a;
        }

        .sidebar-menu a i {
            font-size: 16px;
            width: 20px;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            flex-shrink: 0;
        }

        .user-info .name {
            color: #fff;
            font-size: 13px;
            font-weight: 600;
        }

        .user-info .role-badge {
            font-size: 10px;
            background: rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.8);
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.6);
            font-size: 13px;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background: rgba(255,0,0,0.15);
            color: #ff8080;
        }

        /* ======== MAIN CONTENT ======== */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }

        .topbar {
            background: #fff;
            padding: 14px 28px;
            border-bottom: 1px solid #e8ecf0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .topbar h5 {
            margin: 0;
            font-weight: 700;
            color: #1a2e1a;
            font-size: 16px;
        }

        .topbar .date {
            font-size: 13px;
            color: #888;
        }

        .content-area {
            padding: 28px;
        }

        /* ======== STAT CARDS ======== */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
            transition: transform 0.2s;
        }

        .stat-card:hover { transform: translateY(-2px); }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-icon.green  { background: #e8f8ee; color: #1a6b3a; }
        .stat-icon.yellow { background: #fff8e1; color: #f5a623; }
        .stat-icon.red    { background: #ffeaea; color: #e53935; }
        .stat-icon.blue   { background: #e8f0fe; color: #1a73e8; }

        .stat-info .number {
            font-size: 26px;
            font-weight: 700;
            color: #1a2e1a;
            line-height: 1;
        }

        .stat-info .label {
            font-size: 13px;
            color: #888;
            margin-top: 4px;
        }

        /* ======== CARD TABLE ======== */
        .card-table {
            background: #fff;
            border-radius: 12px;
            padding: 20px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
        }

        .card-table h6 {
            font-weight: 700;
            color: #1a2e1a;
            margin-bottom: 0;
        }

        /* ======== TABLE ======== */
        .table thead th {
            background: #f8faf9;
            font-size: 12px;
            font-weight: 700;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            white-space: nowrap;
        }

        .table tbody td {
            font-size: 14px;
            vertical-align: middle;
            border-color: #f0f0f0;
        }

        /* ======== BADGE STATUS ======== */
        .badge-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-normal   { background: #e8f8ee; color: #1a6b3a; }
        .badge-beresiko { background: #fff8e1; color: #b8860b; }
        .badge-stunting { background: #ffeaea; color: #c0392b; }

        /* ======== FORM ======== */
        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: #444;
            margin-bottom: 4px;
        }

        .form-control {
            border-radius: 8px;
            border: 1.5px solid #ddd;
            font-size: 14px;
            padding: 8px 12px;
        }

        .form-control:focus {
            border-color: #2d9e5f;
            box-shadow: 0 0 0 3px rgba(45,158,95,0.15);
        }

        select.form-control { cursor: pointer; }
    </style>
</head>
<body>