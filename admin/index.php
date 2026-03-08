<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../includes/koneksi.php'; 

$kategori_filter_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : null;

$query = "SELECT * FROM produk";
if ($kategori_filter_id) {
    $query .= " WHERE kategori_id = ?";
}
$query .= " ORDER BY id DESC";

$stmt = $koneksi->prepare($query);
if ($kategori_filter_id) {
    $stmt->bind_param("i", $kategori_filter_id);
}
$stmt->execute();
$result = $stmt->get_result();

// Count stats
$total_produk   = $koneksi->query("SELECT COUNT(*) as c FROM produk")->fetch_assoc()['c'];
$total_kategori = $koneksi->query("SELECT COUNT(*) as c FROM kategori")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Produk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --bg:        #f7f5f2;
            --surface:   #ffffff;
            --surface2:  #faf9f7;
            --border:    #e8e4de;
            --border2:   #f0ede8;
            --text:      #1a1714;
            --muted:     #8c8279;
            --muted2:    #b5afa7;
            --accent:    #c17f3e;
            --accent-bg: rgba(193,127,62,0.08);
            --accent-border: rgba(193,127,62,0.25);
            --success:   #2d7a4f;
            --success-bg: rgba(45,122,79,0.08);
            --info:      #2563a8;
            --info-bg:   rgba(37,99,168,0.08);
            --warn:      #b45309;
            --warn-bg:   rgba(180,83,9,0.08);
            --danger:    #b91c1c;
            --danger-bg: rgba(185,28,28,0.08);
            --sidebar-w: 240px;
            --font: 'Plus Jakarta Sans', sans-serif;
            --mono: 'JetBrains Mono', monospace;
            --radius: 14px;
            --shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
            --shadow-lg: 0 2px 8px rgba(0,0,0,0.08), 0 12px 32px rgba(0,0,0,0.06);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        /* ═══════════════════════════════
           SIDEBAR
        ═══════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform 0.3s cubic-bezier(.4,0,.2,1);
        }

        .sidebar-brand {
            padding: 28px 24px 24px;
            border-bottom: 1px solid var(--border2);
        }

        .sidebar-brand .logo-mark {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent), #e09b55);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(193,127,62,0.3);
        }

        .sidebar-brand .brand-name {
            font-weight: 800;
            font-size: 15px;
            letter-spacing: -0.01em;
        }

        .sidebar-brand .brand-sub {
            font-size: 11px;
            color: var(--muted);
            font-weight: 500;
            margin-top: 2px;
        }

        .sidebar-nav {
            padding: 16px 12px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted2);
            padding: 0 12px;
            margin-bottom: 6px;
            margin-top: 16px;
        }

        .nav-section-label:first-child { margin-top: 0; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--muted);
            font-size: 13.5px;
            font-weight: 600;
            transition: all 0.15s;
            margin-bottom: 2px;
        }

        .nav-link .nav-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            background: transparent;
            transition: all 0.15s;
            flex-shrink: 0;
        }

        .nav-link:hover {
            color: var(--text);
            background: var(--surface2);
        }

        .nav-link:hover .nav-icon {
            background: var(--bg);
        }

        .nav-link.active {
            color: var(--accent);
            background: var(--accent-bg);
        }

        .nav-link.active .nav-icon {
            background: var(--accent-bg);
            color: var(--accent);
        }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border2);
        }

        .nav-link.logout {
            color: var(--danger);
        }

        .nav-link.logout:hover {
            background: var(--danger-bg);
        }

        .nav-link.logout .nav-icon { color: var(--danger); }

        /* Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.3);
            z-index: 99;
            backdrop-filter: blur(2px);
        }

        /* ═══════════════════════════════
           MAIN
        ═══════════════════════════════ */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* Topbar */
        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .menu-toggle {
            display: none;
            width: 36px;
            height: 36px;
            border: 1px solid var(--border);
            border-radius: 9px;
            background: var(--surface);
            cursor: pointer;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            font-size: 14px;
            transition: all 0.15s;
        }

        .menu-toggle:hover { background: var(--bg); color: var(--text); }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--muted);
        }

        .breadcrumb .crumb-current {
            color: var(--text);
            font-weight: 700;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar-badge {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 99px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
            white-space: nowrap;
        }

        .topbar-badge span {
            color: var(--text);
            font-family: var(--mono);
        }

        /* Content area */
        .content {
            padding: 28px;
            flex: 1;
        }

        /* ── Page Title ── */
        .page-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .page-title {
            font-size: clamp(22px, 3vw, 28px);
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .page-title span {
            color: var(--accent);
        }

        /* ── Stat Cards ── */
        .stat-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px 22px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--shadow);
            transition: box-shadow 0.2s, transform 0.2s;
        }

        .stat-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-1px);
        }

        .stat-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }

        .stat-icon-box.amber  { background: var(--warn-bg); color: var(--warn); }
        .stat-icon-box.green  { background: var(--success-bg); color: var(--success); }
        .stat-icon-box.blue   { background: var(--info-bg); color: var(--info); }

        .stat-info .stat-num {
            font-size: 26px;
            font-weight: 800;
            line-height: 1;
            font-family: var(--mono);
        }

        .stat-info .stat-label {
            font-size: 12px;
            color: var(--muted);
            font-weight: 600;
            margin-top: 3px;
        }

        /* ── Category Filter ── */
        .cat-filter-wrap {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 20px;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
        }

        .cat-filter-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted2);
            margin-bottom: 12px;
        }

        .cat-scroll {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 4px;
            cursor: grab;
            scrollbar-width: none;
        }

        .cat-scroll::-webkit-scrollbar { display: none; }

        .cat-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 99px;
            border: 1px solid var(--border);
            background: var(--bg);
            color: var(--muted);
            text-decoration: none;
            font-size: 12.5px;
            font-weight: 600;
            white-space: nowrap;
            transition: all 0.15s;
            flex-shrink: 0;
        }

        .cat-btn:hover {
            border-color: var(--accent-border);
            color: var(--accent);
            background: var(--accent-bg);
        }

        .cat-btn.active {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
            box-shadow: 0 3px 10px rgba(193,127,62,0.25);
        }

        /* ── Toolbar ── */
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .toolbar-left { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: var(--accent);
            color: white;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 13.5px;
            border: none;
            cursor: pointer;
            transition: all 0.15s;
            box-shadow: 0 2px 8px rgba(193,127,62,0.25);
        }

        .btn-primary:hover {
            background: #a96d31;
            box-shadow: 0 4px 14px rgba(193,127,62,0.35);
            transform: translateY(-1px);
        }

        .btn-danger-outline {
            display: none;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: var(--danger-bg);
            color: var(--danger);
            border-radius: 10px;
            font-weight: 700;
            font-size: 13.5px;
            border: 1px solid rgba(185,28,28,0.2);
            cursor: pointer;
            transition: all 0.15s;
            font-family: var(--font);
        }

        .btn-danger-outline:hover {
            background: var(--danger);
            color: white;
        }

        /* Search */
        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 10px 14px 10px 38px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-family: var(--font);
            font-size: 13px;
            color: var(--text);
            width: 220px;
            transition: all 0.15s;
            outline: none;
        }

        .search-box input::placeholder { color: var(--muted2); }

        .search-box input:focus {
            border-color: var(--accent-border);
            box-shadow: 0 0 0 3px var(--accent-bg);
            width: 260px;
        }

        .search-box .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted2);
            font-size: 13px;
            pointer-events: none;
        }

        /* ── Table ── */
        .table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .table-inner { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 620px;
        }

        thead th {
            padding: 13px 18px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            background: var(--surface2);
            border-bottom: 1px solid var(--border);
            text-align: left;
            white-space: nowrap;
        }

        thead th.th-check { width: 48px; text-align: center; }
        thead th.th-img   { width: 80px; }
        thead th.th-actions { width: 130px; text-align: center; }

        tbody tr {
            border-bottom: 1px solid var(--border2);
            transition: background 0.1s;
        }

        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--surface2); }
        tbody tr.selected { background: var(--accent-bg); }

        td {
            padding: 14px 18px;
            font-size: 13.5px;
            vertical-align: middle;
        }

        td.td-check { text-align: center; }

        /* Custom checkbox */
        input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 17px;
            height: 17px;
            border: 2px solid var(--border);
            border-radius: 5px;
            background: var(--surface);
            cursor: pointer;
            position: relative;
            transition: all 0.15s;
        }

        input[type="checkbox"]:checked {
            background: var(--accent);
            border-color: var(--accent);
        }

        input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            top: 1px; left: 4px;
            width: 5px; height: 9px;
            border: 2px solid white;
            border-top: none;
            border-left: none;
            transform: rotate(45deg);
        }

        .product-img {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid var(--border);
            display: block;
        }

        .product-name {
            font-weight: 700;
            font-size: 14px;
            color: var(--text);
        }

        .product-sub {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
        }

        .price-cell {
            font-family: var(--mono);
            font-size: 13px;
            font-weight: 600;
            color: var(--success);
        }

        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 600;
            font-family: var(--mono);
        }

        .stock-badge.has-stock  { background: var(--success-bg); color: var(--success); }
        .stock-badge.unlimited  { background: var(--info-bg); color: var(--info); }

        /* Action buttons */
        .actions-cell { text-align: center; }

        .action-group {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.15s;
            border: 1px solid transparent;
            cursor: pointer;
            background: none;
        }

        .btn-icon.edit   { background: var(--info-bg); color: var(--info); border-color: rgba(37,99,168,0.15); }
        .btn-icon.edit:hover   { background: var(--info); color: white; }

        .btn-icon.keys   { background: var(--warn-bg); color: var(--warn); border-color: rgba(180,83,9,0.15); }
        .btn-icon.keys:hover   { background: var(--warn); color: white; }

        .btn-icon.del    { background: var(--danger-bg); color: var(--danger); border-color: rgba(185,28,28,0.15); }
        .btn-icon.del:hover    { background: var(--danger); color: white; }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 72px 20px;
            color: var(--muted);
        }

        .empty-icon {
            font-size: 40px;
            opacity: 0.2;
            margin-bottom: 12px;
        }

        .empty-state p { font-size: 14px; }

        /* ── Animations ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .stat-row  { animation: fadeUp 0.4s ease both; }
        .cat-filter-wrap { animation: fadeUp 0.4s 0.05s ease both; }
        .toolbar   { animation: fadeUp 0.4s 0.1s ease both; }
        .table-card { animation: fadeUp 0.4s 0.15s ease both; }

        /* ═══════════════════════════════
           RESPONSIVE
        ═══════════════════════════════ */
        @media (max-width: 1024px) {
            :root { --sidebar-w: 210px; }
        }

        @media (max-width: 768px) {
            body { display: block; }

            .sidebar {
                transform: translateX(calc(-1 * var(--sidebar-w)));
            }

            .sidebar.open {
                transform: translateX(0);
                box-shadow: var(--shadow-lg);
            }

            .sidebar-overlay.visible { display: block; }

            .main { margin-left: 0; }

            .menu-toggle { display: inline-flex; }

            .content { padding: 16px; }

            .stat-row { grid-template-columns: 1fr 1fr; }

            .topbar { padding: 0 16px; }

            .topbar-badge { display: none; }

            .search-box input { width: 160px; }
            .search-box input:focus { width: 190px; }
        }

        @media (max-width: 480px) {
            .stat-row { grid-template-columns: 1fr; }

            .page-title { font-size: 20px; }

            .toolbar { flex-direction: column; align-items: stretch; }
            .toolbar-left { justify-content: space-between; }

            .search-box { width: 100%; }
            .search-box input { width: 100%; }
            .search-box input:focus { width: 100%; }
        }
    </style>
</head>
<body>

<!-- Overlay -->
<div class="sidebar-overlay" id="overlay"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-mark"><i class="fas fa-store"></i></div>
        <div class="brand-name">DASHBOARD ADMIN</div>
        <div class="brand-sub">Panel Manajemen</div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Utama</div>
        <a href="index.php" class="nav-link active">
            <span class="nav-icon"><i class="fas fa-box-open"></i></span>
            Produk
        </a>
        <a href="kategori.php" class="nav-link">
            <span class="nav-icon"><i class="fas fa-tags"></i></span>
            Kategori
        </a>
        <a href="voucher.php" class="nav-link">
            <span class="nav-icon"><i class="fas fa-ticket-alt"></i></span>
            Voucher
        </a>

        <div class="nav-section-label">Laporan & Lainnya</div>
        <a href="testimoni_admin.php" class="nav-link">
            <span class="nav-icon"><i class="fas fa-comment-dots"></i></span>
            Testimoni
        </a>
        <a href="pesanan_sukses.php" class="nav-link">
            <span class="nav-icon"><i class="fas fa-file-invoice"></i></span>
            Laporan
        </a>
        <a href="admin_request.php" class="nav-link">
            <span class="nav-icon"><i class="fas fa-inbox"></i></span>
            Request
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="logout.php" class="nav-link logout">
            <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>
            Logout
        </a>
    </div>
</aside>

<!-- Main -->
<div class="main">

    <!-- Topbar -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="breadcrumb">
                <span>Admin</span>
                <i class="fas fa-chevron-right" style="font-size:9px;"></i>
                <span class="crumb-current">Produk</span>
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-badge">Total Produk: <span><?= $total_produk ?></span></div>
            <div class="topbar-badge">Kategori: <span><?= $total_kategori ?></span></div>
        </div>
    </header>

    <div class="content">

        <!-- Page title -->
        <div class="page-title-row">
            <h1 class="page-title">Manajemen <span>Produk</span></h1>
        </div>

        <!-- Stats -->
        <div class="stat-row">
            <div class="stat-card">
                <div class="stat-icon-box amber"><i class="fas fa-box"></i></div>
                <div class="stat-info">
                    <div class="stat-num"><?= $total_produk ?></div>
                    <div class="stat-label">Total Produk</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-box green"><i class="fas fa-tags"></i></div>
                <div class="stat-info">
                    <div class="stat-num"><?= $total_kategori ?></div>
                    <div class="stat-label">Kategori Aktif</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-box blue"><i class="fas fa-inbox"></i></div>
                <div class="stat-info">
                    <?php
                    $req_pending = $koneksi->query("SELECT COUNT(*) as c FROM request_produk WHERE status='pending'")->fetch_assoc()['c'];
                    ?>
                    <div class="stat-num"><?= $req_pending ?></div>
                    <div class="stat-label">Request Pending</div>
                </div>
            </div>
        </div>

        <!-- Category Filter -->
        <div class="cat-filter-wrap">
            <div class="cat-filter-label">Filter Kategori</div>
            <div class="cat-scroll" id="cat-scroll">
                <a href="index.php" class="cat-btn <?= !$kategori_filter_id ? 'active' : '' ?>">
                    <i class="fas fa-th"></i> Semua
                </a>
                <?php
                $kategori_list = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                while ($kat = mysqli_fetch_assoc($kategori_list)):
                ?>
                <a href="index.php?kategori=<?= $kat['id'] ?>" class="cat-btn <?= ($kategori_filter_id == $kat['id']) ? 'active' : '' ?>">
                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                </a>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="toolbar">
            <div class="toolbar-left">
                <a href="edit.php" class="btn-primary"><i class="fas fa-plus"></i> Tambah Produk</a>
                <button type="button" id="btn-bulk-delete" class="btn-danger-outline">
                    <i class="fas fa-trash-alt"></i> Hapus Terpilih (<span id="count-selected">0</span>)
                </button>
            </div>
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="search-input" placeholder="Cari produk...">
            </div>
        </div>

        <!-- Table -->
        <div class="table-card">
            <div class="table-inner">
                <table>
                    <thead>
                        <tr>
                            <th class="th-check"><input type="checkbox" id="check-all"></th>
                            <th class="th-img">Foto</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th class="th-actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body">
                        <?php if ($result->num_rows > 0): while ($row = $result->fetch_assoc()): ?>
                        <tr data-name="<?= strtolower(htmlspecialchars($row['nama_produk'])) ?>">
                            <td class="td-check">
                                <input type="checkbox" class="product-checkbox" value="<?= $row['id'] ?>">
                            </td>
                            <td>
                                <img src="../assets/images/<?= htmlspecialchars($row['gambar']) ?>" class="product-img" alt="<?= htmlspecialchars($row['nama_produk']) ?>">
                            </td>
                            <td>
                                <div class="product-name"><?= htmlspecialchars($row['nama_produk']) ?></div>
                                <div class="product-sub">ID #<?= $row['id'] ?></div>
                            </td>
                            <td class="price-cell">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($row['cek_stok'] == 1): ?>
                                    <span class="stock-badge has-stock"><i class="fas fa-cubes" style="font-size:10px;"></i><?= $row['stok'] ?></span>
                                <?php else: ?>
                                    <span class="stock-badge unlimited"><i class="fas fa-infinity" style="font-size:10px;"></i> Bebas</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions-cell">
                                <div class="action-group">
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-icon edit" title="Edit"><i class="fas fa-pen"></i></a>
                                    <?php if ($row['cek_stok'] == 1): ?>
                                        <a href="kelola_keys.php?id=<?= $row['id'] ?>" class="btn-icon keys" title="Kelola Keys"><i class="fas fa-key"></i></a>
                                    <?php endif; ?>
                                    <a href="proses.php?aksi=hapus&id=<?= $row['id'] ?>" class="btn-icon del" title="Hapus" onclick="return confirm('Yakin hapus produk ini?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="fas fa-box-open"></i></div>
                                    <p>Belum ada produk. <a href="edit.php" style="color:var(--accent);font-weight:700;">Tambah sekarang →</a></p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    // ── Sidebar Mobile ──
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.getElementById('overlay');
    const menuToggle = document.getElementById('menuToggle');

    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('visible'); }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('visible'); }

    menuToggle.addEventListener('click', () => sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
    overlay.addEventListener('click', closeSidebar);

    // ── Category scroll drag ──
    const catScroll = document.getElementById('cat-scroll');
    let isDown = false, startX, scrollLeft;
    catScroll.addEventListener('mousedown', e => { isDown = true; startX = e.pageX - catScroll.offsetLeft; scrollLeft = catScroll.scrollLeft; });
    catScroll.addEventListener('mouseleave', () => isDown = false);
    catScroll.addEventListener('mouseup', () => isDown = false);
    catScroll.addEventListener('mousemove', e => { if (!isDown) return; e.preventDefault(); catScroll.scrollLeft = scrollLeft - (e.pageX - catScroll.offsetLeft - startX); });

    // ── Search ──
    document.getElementById('search-input').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#product-table-body tr[data-name]').forEach(tr => {
            tr.style.display = tr.dataset.name.includes(q) ? '' : 'none';
        });
    });

    // ── Bulk Delete ──
    const checkAll  = document.getElementById('check-all');
    const bulkBtn   = document.getElementById('btn-bulk-delete');
    const countSpan = document.getElementById('count-selected');
    const tableEl   = document.querySelector('table');

    function updateBulkUI() {
        const checked = document.querySelectorAll('.product-checkbox:checked');
        countSpan.innerText = checked.length;
        bulkBtn.style.display = checked.length > 0 ? 'inline-flex' : 'none';
        document.querySelectorAll('#product-table-body tr[data-name]').forEach(tr => {
            const cb = tr.querySelector('.product-checkbox');
            tr.classList.toggle('selected', cb && cb.checked);
        });
    }

    checkAll.addEventListener('change', function() {
        document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = this.checked);
        updateBulkUI();
    });

    // Event delegation — works even after AJAX innerHTML replacement
    tableEl.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-checkbox')) {
            updateBulkUI();
            const allCbs     = document.querySelectorAll('.product-checkbox');
            const checkedCbs = document.querySelectorAll('.product-checkbox:checked');
            checkAll.checked = allCbs.length > 0 && allCbs.length === checkedCbs.length;
        }
    });

    bulkBtn.addEventListener('click', function() {
        const ids = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
        if (!confirm(`Hapus ${ids.length} produk terpilih? Tindakan ini tidak bisa dibatalkan.`)) return;
        const fd = new FormData();
        fd.append('ids', JSON.stringify(ids));
        fetch('proses_bulk_delete.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(d => { if (d.status === 'success') location.reload(); else alert('Gagal: ' + d.message); });
    });

    // ── AJAX Category Filter ──
    document.querySelectorAll('.cat-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const tableBody = document.getElementById('product-table-body');
            const tableInner = tableBody.closest('.table-inner') || tableBody.closest('table');

            // Fade out
            tableBody.style.transition = 'opacity 0.15s ease';
            tableBody.style.opacity = '0.35';

            fetch(this.href.replace('index.php', 'get_admin_products.php'))
                .then(r => r.text())
                .then(html => {
                    tableBody.innerHTML = html;
                    // Force reflow then fade in
                    requestAnimationFrame(() => {
                        tableBody.style.opacity = '1';
                    });
                    checkAll.checked = false;
                    updateBulkUI();
                })
                .catch(() => {
                    tableBody.style.opacity = '1';
                });
        });
    });
</script>
</body>
</html>