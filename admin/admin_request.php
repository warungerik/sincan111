<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../includes/koneksi.php';

// Proses Aksi (Update Status atau Hapus)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id > 0) {
        if ($action == 'update_status' && isset($_GET['status'])) {
            $status = $_GET['status'];
            $allowed_statuses = ['pending', 'dilihat', 'ditambahkan', 'ditolak'];
            if (in_array($status, $allowed_statuses)) {
                $stmt = mysqli_prepare($koneksi, "UPDATE request_produk SET status = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "si", $status, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        } elseif ($action == 'delete') {
            $stmt = mysqli_prepare($koneksi, "DELETE FROM request_produk WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    header('Location: admin_request.php');
    exit;
}

$query_request = "SELECT * FROM request_produk ORDER BY tanggal_request DESC";
$result_request = mysqli_query($koneksi, $query_request);

$total = mysqli_num_rows($result_request);

// Count per status
$count_query = "SELECT status, COUNT(*) as c FROM request_produk GROUP BY status";
$count_result = mysqli_query($koneksi, $count_query);
$counts = ['pending' => 0, 'dilihat' => 0, 'ditambahkan' => 0, 'ditolak' => 0];
while ($c = mysqli_fetch_assoc($count_result)) {
    $counts[$c['status']] = $c['c'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Request Produk</title>
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
            grid-template-columns: repeat(4, 1fr);
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
        .stat-icon-box.red    { background: var(--danger-bg); color: var(--danger); }

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
            cursor: pointer;
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
            min-width: 900px;
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

        thead th.th-actions { text-align: center; }

        tbody tr {
            border-bottom: 1px solid var(--border2);
            transition: background 0.1s;
        }

        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--surface2); }

        td {
            padding: 14px 18px;
            font-size: 13.5px;
            vertical-align: middle;
        }

        .id-cell {
            font-family: var(--mono);
            font-size: 11.5px;
            color: var(--muted);
        }

        .date-cell {
            font-family: var(--mono);
            font-size: 12px;
            color: var(--muted);
            white-space: nowrap;
        }

        .sender-name {
            font-weight: 700;
            font-size: 14px;
        }

        .sender-icon {
            color: var(--accent);
            margin-right: 6px;
            font-size: 12px;
        }

        .contact-cell {
            font-family: var(--mono);
            font-size: 12px;
            color: var(--muted);
        }

        .product-name-cell {
            font-weight: 700;
            color: var(--text);
        }

        .desc-cell {
            max-width: 280px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            font-family: var(--mono);
            white-space: nowrap;
        }

        .status-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-pending { background: var(--warn-bg); color: var(--warn); border: 1px solid rgba(180,83,9,0.2); }
        .status-pending::before { background: var(--warn); animation: pulse 2s infinite; }

        .status-dilihat { background: var(--info-bg); color: var(--info); border: 1px solid rgba(37,99,168,0.2); }
        .status-dilihat::before { background: var(--info); }

        .status-ditambahkan { background: var(--success-bg); color: var(--success); border: 1px solid rgba(45,122,79,0.2); }
        .status-ditambahkan::before { background: var(--success); }

        .status-ditolak { background: var(--danger-bg); color: var(--danger); border: 1px solid rgba(185,28,28,0.2); }
        .status-ditolak::before { background: var(--danger); }

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
            position: relative;
        }

        .btn-icon.seen   { background: var(--info-bg); color: var(--info); border-color: rgba(37,99,168,0.15); }
        .btn-icon.seen:hover   { background: var(--info); color: white; }

        .btn-icon.add    { background: var(--success-bg); color: var(--success); border-color: rgba(45,122,79,0.15); }
        .btn-icon.add:hover    { background: var(--success); color: white; }

        .btn-icon.reject { background: var(--warn-bg); color: var(--warn); border-color: rgba(180,83,9,0.15); }
        .btn-icon.reject:hover { background: var(--warn); color: white; }

        .btn-icon.del    { background: var(--danger-bg); color: var(--danger); border-color: rgba(185,28,28,0.15); }
        .btn-icon.del:hover    { background: var(--danger); color: white; }

        .divider-action {
            width: 1px;
            height: 20px;
            background: var(--border);
        }

        /* Tooltip */
        .btn-icon::after {
            content: attr(data-tip);
            position: absolute;
            bottom: calc(100% + 6px);
            left: 50%;
            transform: translateX(-50%);
            background: var(--text);
            color: var(--surface);
            font-size: 10px;
            font-family: var(--mono);
            padding: 4px 8px;
            border-radius: 6px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.15s;
            z-index: 10;
        }
        .btn-icon:hover::after { opacity: 1; }

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

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.4; }
        }

        .stat-row  { animation: fadeUp 0.4s ease both; }
        .cat-filter-wrap { animation: fadeUp 0.4s 0.05s ease both; }
        .table-card { animation: fadeUp 0.4s 0.1s ease both; }

        /* ═══════════════════════════════
           RESPONSIVE
        ═══════════════════════════════ */
        @media (max-width: 1024px) {
            :root { --sidebar-w: 210px; }
            .stat-row { grid-template-columns: repeat(2, 1fr); }
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
        }

        @media (max-width: 480px) {
            .stat-row { grid-template-columns: 1fr; }

            .page-title { font-size: 20px; }
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
        <a href="index.php" class="nav-link">
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
        <a href="admin_request.php" class="nav-link active">
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
                <span class="crumb-current">Request Produk</span>
            </div>
        </div>
    </header>

    <div class="content">

        <!-- Page title -->
        <div class="page-title-row">
            <h1 class="page-title">Request <span>Produk</span></h1>
        </div>

        <!-- Stats -->
        <div class="stat-row">
            <div class="stat-card">
                <div class="stat-icon-box amber"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <div class="stat-num"><?= $counts['pending'] ?></div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-box blue"><i class="fas fa-eye"></i></div>
                <div class="stat-info">
                    <div class="stat-num"><?= $counts['dilihat'] ?></div>
                    <div class="stat-label">Dilihat</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-box green"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <div class="stat-num"><?= $counts['ditambahkan'] ?></div>
                    <div class="stat-label">Ditambahkan</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-box red"><i class="fas fa-times-circle"></i></div>
                <div class="stat-info">
                    <div class="stat-num"><?= $counts['ditolak'] ?></div>
                    <div class="stat-label">Ditolak</div>
                </div>
            </div>
        </div>

        <!-- Status Filter -->
        <div class="cat-filter-wrap">
            <div class="cat-filter-label">Filter Status</div>
            <div class="cat-scroll" id="cat-scroll">
                <button class="cat-btn active" onclick="filterTable('all', this)">
                    <i class="fas fa-th"></i> Semua
                </button>
                <button class="cat-btn" onclick="filterTable('pending', this)">
                    <i class="fas fa-clock"></i> Pending
                </button>
                <button class="cat-btn" onclick="filterTable('dilihat', this)">
                    <i class="fas fa-eye"></i> Dilihat
                </button>
                <button class="cat-btn" onclick="filterTable('ditambahkan', this)">
                    <i class="fas fa-check-circle"></i> Ditambahkan
                </button>
                <button class="cat-btn" onclick="filterTable('ditolak', this)">
                    <i class="fas fa-times-circle"></i> Ditolak
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-card">
            <div class="table-inner">
                <table id="requestTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Pengirim</th>
                            <th>Kontak</th>
                            <th>Nama Produk</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th class="th-actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($total > 0) {
                            mysqli_data_seek($result_request, 0);
                            while ($row = mysqli_fetch_assoc($result_request)) {
                                $s = htmlspecialchars($row['status']);
                                echo '<tr data-status="' . $s . '">';
                                echo '<td class="id-cell">#' . $row['id'] . '</td>';
                                echo '<td class="date-cell">' . date('d M Y', strtotime($row['tanggal_request'])) . '<br><span style="opacity:.5;font-size:11px;">' . date('H:i', strtotime($row['tanggal_request'])) . '</span></td>';
                                echo '<td><span class="sender-name"><i class="fas fa-user sender-icon"></i>' . htmlspecialchars($row['nama_pengirim']) . '</span></td>';
                                echo '<td class="contact-cell">' . htmlspecialchars($row['kontak_pengirim']) . '</td>';
                                echo '<td class="product-name-cell">' . htmlspecialchars($row['nama_produk']) . '</td>';
                                echo '<td class="desc-cell">' . nl2br(htmlspecialchars($row['deskripsi'])) . '</td>';
                                echo '<td><span class="status-badge status-' . $s . '">' . ucfirst($s) . '</span></td>';
                                echo '<td class="actions-cell">';
                                echo '<div class="action-group">';
                                if ($s != 'dilihat')     echo '<a href="?action=update_status&id=' . $row['id'] . '&status=dilihat" class="btn-icon seen" data-tip="Tandai Dilihat"><i class="fas fa-eye"></i></a>';
                                if ($s != 'ditambahkan') echo '<a href="?action=update_status&id=' . $row['id'] . '&status=ditambahkan" class="btn-icon add" data-tip="Ditambahkan"><i class="fas fa-check"></i></a>';
                                if ($s != 'ditolak')     echo '<a href="?action=update_status&id=' . $row['id'] . '&status=ditolak" class="btn-icon reject" data-tip="Tolak"><i class="fas fa-ban"></i></a>';
                                echo '<div class="divider-action"></div>';
                                echo '<a href="?action=delete&id=' . $row['id'] . '" class="btn-icon del" data-tip="Hapus" onclick="return confirm(\'Hapus request ini?\')"><i class="fas fa-trash"></i></a>';
                                echo '</div>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="8"><div class="empty-state"><div class="empty-icon"><i class="fas fa-inbox"></i></div><p>Belum ada request produk yang masuk.</p></div></td></tr>';
                        }
                        ?>
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

    // ── Filter Table ──
    function filterTable(status, btn) {
        document.querySelectorAll('.cat-btn').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
        const rows = document.querySelectorAll('#requestTable tbody tr[data-status]');
        
        // Fade effect
        const tbody = document.querySelector('#requestTable tbody');
        tbody.style.transition = 'opacity 0.15s ease';
        tbody.style.opacity = '0.35';
        
        setTimeout(() => {
            rows.forEach(row => {
                row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
            });
            tbody.style.opacity = '1';
        }, 150);
    }
</script>
</body>
</html>