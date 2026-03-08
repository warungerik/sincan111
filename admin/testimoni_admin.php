<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header("Location: login.php"); exit(); }
include '../includes/koneksi.php';

$result   = mysqli_query($koneksi,"SELECT * FROM testimoni ORDER BY FIELD(status,'pending') DESC, tanggal_submit DESC");
$total    = mysqli_num_rows($result);
$pending  = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as c FROM testimoni WHERE status='pending'"))['c'];
$approved = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as c FROM testimoni WHERE status='approved'"))['c'];
mysqli_data_seek($result, 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimoni — AdminStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root{--bg:#f7f5f2;--surface:#fff;--surface2:#faf9f7;--border:#e8e4de;--border2:#f0ede8;--text:#1a1714;--muted:#8c8279;--muted2:#b5afa7;--accent:#c17f3e;--accent-bg:rgba(193,127,62,0.08);--accent-border:rgba(193,127,62,0.25);--success:#2d7a4f;--success-bg:rgba(45,122,79,0.08);--warn:#b45309;--warn-bg:rgba(180,83,9,0.08);--danger:#b91c1c;--danger-bg:rgba(185,28,28,0.08);--sidebar-w:240px;--font:'Plus Jakarta Sans',sans-serif;--mono:'JetBrains Mono',monospace;--radius:14px;--shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.04);--shadow-lg:0 2px 8px rgba(0,0,0,.08),0 12px 32px rgba(0,0,0,.06)}
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:var(--font);background:var(--bg);color:var(--text);min-height:100vh;display:flex}
        .sidebar{width:var(--sidebar-w);background:var(--surface);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:100;transition:transform .3s cubic-bezier(.4,0,.2,1)}
        .sidebar-brand{padding:28px 24px 24px;border-bottom:1px solid var(--border2)}
        .logo-mark{width:36px;height:36px;background:linear-gradient(135deg,var(--accent),#e09b55);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;margin-bottom:10px;box-shadow:0 4px 12px rgba(193,127,62,.3)}
        .brand-name{font-weight:800;font-size:15px;letter-spacing:-.01em}.brand-sub{font-size:11px;color:var(--muted);font-weight:500;margin-top:2px}
        .sidebar-nav{padding:16px 12px;flex:1;overflow-y:auto}
        .nav-section-label{font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted2);padding:0 12px;margin-bottom:6px;margin-top:16px}.nav-section-label:first-child{margin-top:0}
        .nav-link{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;text-decoration:none;color:var(--muted);font-size:13.5px;font-weight:600;transition:all .15s;margin-bottom:2px}
        .nav-link .nav-icon{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:13px;background:transparent;transition:all .15s;flex-shrink:0}
        .nav-link:hover{color:var(--text);background:var(--surface2)}.nav-link:hover .nav-icon{background:var(--bg)}
        .nav-link.active{color:var(--accent);background:var(--accent-bg)}.nav-link.active .nav-icon{background:var(--accent-bg);color:var(--accent)}
        .sidebar-footer{padding:16px 12px;border-top:1px solid var(--border2)}
        .nav-link.logout{color:var(--danger)}.nav-link.logout:hover{background:var(--danger-bg)}
        .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.3);z-index:99;backdrop-filter:blur(2px)}
        .main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-width:0}
        .topbar{background:var(--surface);border-bottom:1px solid var(--border);padding:0 28px;height:64px;display:flex;align-items:center;justify-content:space-between;gap:16px;position:sticky;top:0;z-index:50}
        .topbar-left{display:flex;align-items:center;gap:12px}
        .menu-toggle{display:none;width:36px;height:36px;border:1px solid var(--border);border-radius:9px;background:var(--surface);cursor:pointer;align-items:center;justify-content:center;color:var(--muted);font-size:14px;transition:all .15s}
        .menu-toggle:hover{background:var(--bg);color:var(--text)}
        .breadcrumb{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--muted)}.breadcrumb .crumb-current{color:var(--text);font-weight:700}
        .topbar-badge{background:var(--surface2);border:1px solid var(--border);border-radius:99px;padding:6px 14px;font-size:12px;font-weight:600;color:var(--muted);white-space:nowrap}
        .topbar-badge span{color:var(--text);font-family:var(--mono)}
        .content{padding:28px;flex:1}
        .page-title{font-size:clamp(20px,3vw,26px);font-weight:800;letter-spacing:-.02em;margin-bottom:4px}.page-title span{color:var(--accent)}
        .page-subtitle{font-size:13px;color:var(--muted);margin-bottom:24px;margin-top:4px}
        .stat-row{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px;animation:fadeUp .4s ease both}
        .stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:14px;box-shadow:var(--shadow);transition:transform .2s,box-shadow .2s}
        .stat-card:hover{transform:translateY(-1px);box-shadow:var(--shadow-lg)}
        .stat-icon-box{width:40px;height:40px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
        .stat-icon-box.amber{background:var(--warn-bg);color:var(--warn)}.stat-icon-box.green{background:var(--success-bg);color:var(--success)}.stat-icon-box.blue{background:rgba(37,99,168,.08);color:#2563a8}
        .stat-num{font-size:24px;font-weight:800;line-height:1;font-family:var(--mono)}.stat-label{font-size:12px;color:var(--muted);font-weight:600;margin-top:3px}
        .table-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;animation:fadeUp .4s .08s ease both}
        .table-header{padding:16px 22px;border-bottom:1px solid var(--border2);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
        .table-header h2{font-size:15px;font-weight:700}
        /* Filter tabs */
        .filter-tabs{display:flex;gap:6px}
        .filter-tab{padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:1px solid transparent;background:transparent;color:var(--muted);font-family:var(--font);transition:all .15s}
        .filter-tab:hover,.filter-tab.active{background:var(--surface2);border-color:var(--border);color:var(--text)}
        .table-inner{overflow-x:auto}
        table{width:100%;border-collapse:collapse;min-width:700px}
        thead th{padding:11px 18px;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);background:var(--surface2);border-bottom:1px solid var(--border);text-align:left;white-space:nowrap}
        tbody tr{border-bottom:1px solid var(--border2);transition:background .1s}
        tbody tr:last-child{border-bottom:none}
        tbody tr:hover{background:var(--surface2)}
        td{padding:13px 18px;font-size:13.5px;vertical-align:middle}
        .customer-name{font-weight:700}
        .rating-stars{color:#f59e0b;font-size:13px;letter-spacing:1px;white-space:nowrap}
        .rating-num{font-family:var(--mono);font-size:11px;color:var(--muted);margin-left:4px}
        .testi-text{font-size:13px;color:var(--muted);max-width:300px;line-height:1.5}
        .date-cell{font-family:var(--mono);font-size:12px;color:var(--muted);white-space:nowrap}
        .status-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:700;font-family:var(--mono);white-space:nowrap}
        .status-badge::before{content:'';width:5px;height:5px;border-radius:50%;flex-shrink:0}
        .status-pending{background:var(--warn-bg);color:var(--warn);border:1px solid rgba(180,83,9,.2)}.status-pending::before{background:var(--warn);animation:pulse 2s infinite}
        .status-approved{background:var(--success-bg);color:var(--success);border:1px solid rgba(45,122,79,.2)}.status-approved::before{background:var(--success)}
        .action-group{display:inline-flex;align-items:center;gap:6px}
        .btn-icon{width:32px;height:32px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:12px;transition:all .15s;border:1px solid transparent;cursor:pointer}
        .btn-icon.approve{background:var(--success-bg);color:var(--success);border-color:rgba(45,122,79,.15)}.btn-icon.approve:hover{background:var(--success);color:#fff}
        .btn-icon.del{background:var(--danger-bg);color:var(--danger);border-color:rgba(185,28,28,.15)}.btn-icon.del:hover{background:var(--danger);color:#fff}
        .empty-state{text-align:center;padding:64px 20px;color:var(--muted)}
        .empty-icon{font-size:38px;opacity:.2;margin-bottom:12px}.empty-state p{font-size:13px}
        @keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
        @keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
        @media(max-width:1024px){:root{--sidebar-w:210px}}
        @media(max-width:768px){body{display:block}.sidebar{transform:translateX(calc(-1 * var(--sidebar-w)))}.sidebar.open{transform:translateX(0);box-shadow:var(--shadow-lg)}.sidebar-overlay.visible{display:block}.main{margin-left:0}.menu-toggle{display:inline-flex}.content{padding:16px}.topbar{padding:0 16px}.stat-row{grid-template-columns:1fr 1fr}.topbar-badge{display:none}.filter-tabs{display:none}}
        @media(max-width:480px){.stat-row{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="sidebar-overlay" id="overlay"></div>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand"><div class="logo-mark"><i class="fas fa-store"></i></div><div class="brand-name">DASHBOARD ADMIN</div><div class="brand-sub">Panel Manajemen</div></div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Utama</div>
        <a href="index.php" class="nav-link"><span class="nav-icon"><i class="fas fa-box-open"></i></span>Produk</a>
        <a href="kategori.php" class="nav-link"><span class="nav-icon"><i class="fas fa-tags"></i></span>Kategori</a>
        <a href="voucher.php" class="nav-link"><span class="nav-icon"><i class="fas fa-ticket-alt"></i></span>Voucher</a>
        <div class="nav-section-label">Laporan & Lainnya</div>
        <a href="testimoni_admin.php" class="nav-link active"><span class="nav-icon"><i class="fas fa-comment-dots"></i></span>Testimoni</a>
        <a href="pesanan_sukses.php" class="nav-link"><span class="nav-icon"><i class="fas fa-file-invoice"></i></span>Laporan</a>
        <a href="admin_request.php" class="nav-link"><span class="nav-icon"><i class="fas fa-inbox"></i></span>Request</a>
    </nav>
    <div class="sidebar-footer"><a href="logout.php" class="nav-link logout"><span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>Logout</a></div>
</aside>
<div class="main">
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
            <div class="breadcrumb"><span>Admin</span><i class="fas fa-chevron-right" style="font-size:9px;"></i><span class="crumb-current">Testimoni</span></div>
        </div>
        <div class="topbar-badge">Total: <span><?= $total ?></span></div>
    </header>
    <div class="content">
        <h1 class="page-title">Kelola <span>Testimoni</span></h1>
        <p class="page-subtitle">Review dan moderasi ulasan dari pelanggan.</p>

        <div class="stat-row">
            <div class="stat-card"><div class="stat-icon-box blue"><i class="fas fa-comment-dots"></i></div><div><div class="stat-num"><?= $total ?></div><div class="stat-label">Total Testimoni</div></div></div>
            <div class="stat-card"><div class="stat-icon-box amber"><i class="fas fa-clock"></i></div><div><div class="stat-num"><?= $pending ?></div><div class="stat-label">Menunggu Moderasi</div></div></div>
            <div class="stat-card"><div class="stat-icon-box green"><i class="fas fa-check-circle"></i></div><div><div class="stat-num"><?= $approved ?></div><div class="stat-label">Disetujui</div></div></div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h2><i class="fas fa-list" style="color:var(--accent);font-size:12px;margin-right:4px;"></i>Semua Testimoni</h2>
                <div class="filter-tabs">
                    <button class="filter-tab active" onclick="filterTbl('all',this)">Semua</button>
                    <button class="filter-tab" onclick="filterTbl('pending',this)">Pending</button>
                    <button class="filter-tab" onclick="filterTbl('approved',this)">Approved</button>
                </div>
            </div>
            <div class="table-inner">
                <table>
                    <thead><tr><th>Nama</th><th>Rating</th><th>Testimoni</th><th>Tanggal</th><th>Status</th><th style="text-align:center;">Aksi</th></tr></thead>
                    <tbody id="testi-tbody">
                        <?php if ($total > 0): while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr data-status="<?= $row['status'] ?>">
                            <td class="customer-name"><?= htmlspecialchars($row['nama']) ?></td>
                            <td>
                                <span class="rating-stars"><?= str_repeat('★', (int)$row['rating']) . str_repeat('☆', 5-(int)$row['rating']) ?></span>
                                <span class="rating-num"><?= $row['rating'] ?>/5</span>
                            </td>
                            <td class="testi-text"><?= nl2br(htmlspecialchars($row['testimoni'])) ?></td>
                            <td class="date-cell"><?= date('d M Y', strtotime($row['tanggal_submit'])) ?><br><span style="font-size:11px;"><?= date('H:i', strtotime($row['tanggal_submit'])) ?></span></td>
                            <td><span class="status-badge status-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                            <td style="text-align:center;">
                                <div class="action-group">
                                    <?php if ($row['status'] == 'pending'): ?>
                                    <a href="proses_testimoni_admin.php?aksi=approve&id=<?= $row['id'] ?>" class="btn-icon approve" title="Setujui"><i class="fas fa-check"></i></a>
                                    <?php endif; ?>
                                    <a href="proses_testimoni_admin.php?aksi=hapus&id=<?= $row['id'] ?>" class="btn-icon del" title="Hapus" onclick="return confirm('Hapus testimoni ini?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="6"><div class="empty-state"><div class="empty-icon"><i class="fas fa-comment-dots"></i></div><p>Belum ada testimoni yang masuk.</p></div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    const sidebar=document.getElementById('sidebar'),overlay=document.getElementById('overlay');
    document.getElementById('menuToggle').addEventListener('click',()=>sidebar.classList.contains('open')?closeMenu():openMenu());
    overlay.addEventListener('click',closeMenu);
    function openMenu(){sidebar.classList.add('open');overlay.classList.add('visible')}
    function closeMenu(){sidebar.classList.remove('open');overlay.classList.remove('visible')}

    function filterTbl(status, btn) {
        document.querySelectorAll('.filter-tab').forEach(t=>t.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('#testi-tbody tr[data-status]').forEach(tr=>{
            tr.style.display=(status==='all'||tr.dataset.status===status)?'':'none';
        });
    }
</script>
</body>
</html>