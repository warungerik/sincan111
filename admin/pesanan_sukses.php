<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
include '../includes/koneksi.php';

$results_per_page = 10;
$page   = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$filter = "(t.status_pembayaran='success' OR t.status_pembayaran='settlement')";
if (!empty($search)) $filter .= " AND (t.nama_pelanggan LIKE '%$search%' OR t.order_id LIKE '%$search%')";

$total_results = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) AS total FROM transaksi t WHERE $filter"))['total'];
$total_pages   = max(1, ceil($total_results / $results_per_page));
$offset        = ($page - 1) * $results_per_page;

$total_revenue = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT SUM(harga) as r FROM transaksi t WHERE "."(t.status_pembayaran='success' OR t.status_pembayaran='settlement')"))['r'] ?? 0;
$total_orders  = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as c FROM transaksi t WHERE "."(t.status_pembayaran='success' OR t.status_pembayaran='settlement')"))['c'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pesanan — AdminStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root{--bg:#f7f5f2;--surface:#fff;--surface2:#faf9f7;--border:#e8e4de;--border2:#f0ede8;--text:#1a1714;--muted:#8c8279;--muted2:#b5afa7;--accent:#c17f3e;--accent-bg:rgba(193,127,62,0.08);--accent-border:rgba(193,127,62,0.25);--success:#2d7a4f;--success-bg:rgba(45,122,79,0.08);--info:#2563a8;--info-bg:rgba(37,99,168,0.08);--warn:#b45309;--warn-bg:rgba(180,83,9,0.08);--danger:#b91c1c;--danger-bg:rgba(185,28,28,0.08);--sidebar-w:240px;--font:'Plus Jakarta Sans',sans-serif;--mono:'JetBrains Mono',monospace;--radius:14px;--shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.04);--shadow-lg:0 2px 8px rgba(0,0,0,.08),0 12px 32px rgba(0,0,0,.06)}
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
        .content{padding:28px;flex:1}
        .page-title{font-size:clamp(20px,3vw,26px);font-weight:800;letter-spacing:-.02em;margin-bottom:4px}.page-title span{color:var(--accent)}
        .page-subtitle{font-size:13px;color:var(--muted);margin-bottom:24px;margin-top:4px}
        /* Stats */
        .stat-row{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px;animation:fadeUp .4s ease both}
        .stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:14px;box-shadow:var(--shadow);transition:transform .2s,box-shadow .2s}
        .stat-card:hover{transform:translateY(-1px);box-shadow:var(--shadow-lg)}
        .stat-icon-box{width:40px;height:40px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
        .stat-icon-box.green{background:var(--success-bg);color:var(--success)}.stat-icon-box.amber{background:var(--warn-bg);color:var(--warn)}.stat-icon-box.blue{background:var(--info-bg);color:var(--info)}
        .stat-num{font-size:22px;font-weight:800;line-height:1;font-family:var(--mono)}.stat-label{font-size:12px;color:var(--muted);font-weight:600;margin-top:3px}
        /* Cards */
        .section-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;margin-bottom:20px;animation:fadeUp .4s ease both}
        .section-card:nth-child(2){animation-delay:.05s}.section-card:nth-child(3){animation-delay:.1s}
        .card-header{padding:16px 22px;border-bottom:1px solid var(--border2);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
        .card-header h2{font-size:15px;font-weight:700;display:flex;align-items:center;gap:8px}
        /* Search */
        .search-form{display:flex;gap:8px;align-items:center}
        .search-wrap{position:relative}
        .search-wrap input{padding:8px 12px 8px 34px;background:var(--bg);border:1px solid var(--border);border-radius:8px;font-family:var(--font);font-size:12.5px;color:var(--text);width:220px;outline:none;transition:all .15s}
        .search-wrap input::placeholder{color:var(--muted2)}
        .search-wrap input:focus{border-color:var(--accent-border);box-shadow:0 0 0 3px var(--accent-bg);background:var(--surface);width:260px}
        .search-wrap .si{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted2);font-size:12px;pointer-events:none}
        .btn-sm{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;font-size:12.5px;font-weight:700;border:none;cursor:pointer;font-family:var(--font);text-decoration:none;transition:all .15s}
        .btn-sm.primary{background:var(--accent);color:#fff}.btn-sm.primary:hover{background:#a96d31}
        .btn-sm.neutral{background:var(--surface2);border:1px solid var(--border);color:var(--muted)}.btn-sm.neutral:hover{color:var(--text);background:var(--bg)}
        /* Tables */
        .table-inner{overflow-x:auto}
        table{width:100%;border-collapse:collapse;min-width:600px}
        thead th{padding:11px 18px;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);background:var(--surface2);border-bottom:1px solid var(--border);text-align:left;white-space:nowrap}
        tbody tr{border-bottom:1px solid var(--border2);transition:background .1s}
        tbody tr:last-child{border-bottom:none}
        tbody tr:hover{background:var(--surface2)}
        td{padding:13px 18px;font-size:13.5px;vertical-align:middle}
        .month-cell{font-weight:700}.revenue-cell{font-family:var(--mono);font-weight:700;color:var(--success)}
        .order-id{font-family:var(--mono);font-size:11.5px;color:var(--muted)}
        .customer-name{font-weight:700}.customer-wa{font-size:11.5px;color:var(--muted);margin-top:2px}
        .cat-chip{display:inline-block;padding:3px 10px;border-radius:6px;background:var(--info-bg);color:var(--info);border:1px solid rgba(37,99,168,.15);font-size:11px;font-weight:700}
        .key-box{font-family:var(--mono);font-size:11.5px;background:var(--bg);border:1px solid var(--border);padding:5px 9px;border-radius:7px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:inline-flex;align-items:center;gap:6px;cursor:pointer;transition:all .15s;position:relative;user-select:none}
        .key-box:hover{background:var(--accent-bg);border-color:var(--accent-border);color:var(--accent)}
        .key-box .copy-icon{font-size:10px;opacity:.5;transition:opacity .15s}
        .key-box:hover .copy-icon{opacity:1}
        .key-box:active{transform:scale(.98)}
        .price-cell{font-family:var(--mono);font-weight:700;color:var(--success)}
        /* Copy Toast Notification */
        .copy-toast{position:fixed;bottom:24px;right:24px;background:var(--success);color:#fff;padding:12px 18px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:var(--shadow-lg);display:flex;align-items:center;gap:8px;opacity:0;transform:translateY(10px);transition:all .3s;pointer-events:none;z-index:1000}
        .copy-toast.show{opacity:1;transform:translateY(0)}
        /* Pagination */
        .pagination-wrapper{padding:16px 22px;border-top:1px solid var(--border2);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
        .pagination-info{font-size:12.5px;color:var(--muted);font-family:var(--mono)}
        .pagination-links{display:flex;gap:8px}
        .page-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;text-decoration:none;font-size:12.5px;font-weight:700;background:var(--surface2);border:1px solid var(--border);color:var(--muted);transition:all .15s}
        .page-btn:hover:not(.disabled){background:var(--accent);border-color:var(--accent);color:#fff}
        .page-btn.disabled{opacity:.4;pointer-events:none}
        .empty-state{text-align:center;padding:60px 20px;color:var(--muted)}
        .empty-icon{font-size:38px;opacity:.2;margin-bottom:12px}
        .empty-state p{font-size:13px}
        @keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
        @media(max-width:1024px){:root{--sidebar-w:210px}}
        @media(max-width:768px){body{display:block}.sidebar{transform:translateX(calc(-1 * var(--sidebar-w)))}.sidebar.open{transform:translateX(0);box-shadow:var(--shadow-lg)}.sidebar-overlay.visible{display:block}.main{margin-left:0}.menu-toggle{display:inline-flex}.content{padding:16px}.topbar{padding:0 16px}.stat-row{grid-template-columns:1fr 1fr}}
        @media(max-width:480px){.stat-row{grid-template-columns:1fr}.search-wrap input{width:150px}.search-wrap input:focus{width:180px}}
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
        <a href="testimoni_admin.php" class="nav-link"><span class="nav-icon"><i class="fas fa-comment-dots"></i></span>Testimoni</a>
        <a href="pesanan_sukses.php" class="nav-link active"><span class="nav-icon"><i class="fas fa-file-invoice"></i></span>Laporan</a>
        <a href="admin_request.php" class="nav-link"><span class="nav-icon"><i class="fas fa-inbox"></i></span>Request</a>
    </nav>
    <div class="sidebar-footer"><a href="logout.php" class="nav-link logout"><span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>Logout</a></div>
</aside>
<div class="main">
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
            <div class="breadcrumb"><span>Admin</span><i class="fas fa-chevron-right" style="font-size:9px;"></i><span class="crumb-current">Laporan</span></div>
        </div>
    </header>
    <div class="content">
        <h1 class="page-title">Laporan <span>Pesanan</span></h1>
        <p class="page-subtitle">Ringkasan dan rincian semua transaksi sukses.</p>

        <div class="stat-row">
            <div class="stat-card"><div class="stat-icon-box green"><i class="fas fa-check-circle"></i></div><div><div class="stat-num"><?= $total_orders ?></div><div class="stat-label">Total Pesanan</div></div></div>
            <div class="stat-card"><div class="stat-icon-box amber"><i class="fas fa-coins"></i></div><div><div class="stat-num" style="font-size:17px;">Rp <?= number_format($total_revenue,0,',','.') ?></div><div class="stat-label">Total Pendapatan</div></div></div>
            <div class="stat-card"><div class="stat-icon-box blue"><i class="fas fa-chart-bar"></i></div><div><div class="stat-num" style="font-size:17px;">Rp <?= $total_orders > 0 ? number_format($total_revenue/$total_orders,0,',','.') : '0' ?></div><div class="stat-label">Rata-rata per Pesanan</div></div></div>
        </div>

        <!-- Ringkasan Bulanan -->
        <div class="section-card">
            <div class="card-header"><h2><i class="fas fa-chart-line" style="color:var(--accent);font-size:13px;"></i>Ringkasan Bulanan</h2></div>
            <div class="table-inner">
                <table>
                    <thead><tr><th>Bulan & Tahun</th><th>Total Pendapatan</th></tr></thead>
                    <tbody>
                        <?php
                        $res_sum = mysqli_query($koneksi,"SELECT DATE_FORMAT(t.tanggal_transaksi,'%M %Y') AS bln, SUM(t.harga) AS total FROM transaksi t WHERE (t.status_pembayaran='success' OR t.status_pembayaran='settlement') GROUP BY YEAR(t.tanggal_transaksi),MONTH(t.tanggal_transaksi) ORDER BY YEAR(t.tanggal_transaksi) DESC,MONTH(t.tanggal_transaksi) DESC");
                        if (mysqli_num_rows($res_sum) > 0): while ($rs = mysqli_fetch_assoc($res_sum)): ?>
                        <tr><td class="month-cell"><?= $rs['bln'] ?></td><td class="revenue-cell">Rp <?= number_format($rs['total'],0,',','.') ?></td></tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="2"><div class="empty-state"><div class="empty-icon"><i class="fas fa-chart-line"></i></div><p>Belum ada data penjualan.</p></div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detail Pesanan -->
        <div class="section-card">
            <div class="card-header">
                <h2><i class="fas fa-list-ul" style="color:var(--accent);font-size:13px;"></i>Rincian Pesanan</h2>
                <form action="" method="GET" class="search-form">
                    <div class="search-wrap">
                        <i class="fas fa-search si"></i>
                        <input type="text" name="search" placeholder="Cari nama / ID pesanan..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <button type="submit" class="btn-sm primary"><i class="fas fa-search"></i></button>
                    <?php if (!empty($search)): ?>
                    <a href="pesanan_sukses.php" class="btn-sm neutral" title="Reset"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="table-inner">
                <table>
                    <thead><tr><th>Tanggal</th><th>ID Pesanan</th><th>Pelanggan</th><th>Kategori</th><th>Produk</th><th>Harga</th><th>Kunci Terjual</th></tr></thead>
                    <tbody>
                        <?php
                        $qr = "SELECT t.order_id,t.nama_pelanggan,t.wa_pelanggan,t.harga,t.key_terjual,t.tanggal_transaksi,p.nama_produk,k.nama_kategori FROM transaksi t JOIN produk p ON t.produk_id=p.id LEFT JOIN kategori k ON p.kategori_id=k.id WHERE $filter ORDER BY t.tanggal_transaksi DESC LIMIT $results_per_page OFFSET $offset";
                        $res = mysqli_query($koneksi, $qr);
                        if (mysqli_num_rows($res) > 0): while ($row = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td style="white-space:nowrap;font-size:12.5px;color:var(--muted);font-family:var(--mono);"><?= date("d M Y", strtotime($row['tanggal_transaksi'])) ?><br><span style="font-size:11px;"><?= date("H:i", strtotime($row['tanggal_transaksi'])) ?></span></td>
                            <td><span class="order-id"><?= htmlspecialchars($row['order_id']) ?></span></td>
                            <td><div class="customer-name"><?= htmlspecialchars($row['nama_pelanggan']) ?></div><div class="customer-wa"><?= htmlspecialchars($row['wa_pelanggan']) ?></div></td>
                            <td><?php if (!empty($row['nama_kategori'])): ?><span class="cat-chip"><?= htmlspecialchars($row['nama_kategori']) ?></span><?php else: ?><span style="color:var(--muted2)">—</span><?php endif; ?></td>
                            <td style="font-weight:600;"><?= htmlspecialchars($row['nama_produk']) ?></td>
                            <td class="price-cell">Rp <?= number_format($row['harga'],0,',','.') ?></td>
                            <td>
                                <span class="key-box" onclick="copyToClipboard(this, '<?= htmlspecialchars($row['key_terjual'], ENT_QUOTES) ?>')" title="Klik untuk menyalin">
                                    <span class="key-text"><?= htmlspecialchars($row['key_terjual']) ?></span>
                                    <i class="fas fa-copy copy-icon"></i>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><i class="fas fa-search"></i></div><p>Tidak ditemukan pesanan<?= !empty($search) ? ' untuk pencarian "<strong>'.htmlspecialchars($search).'</strong>"' : '.' ?></p></div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($total_results > 0): ?>
            <div class="pagination-wrapper">
                <div class="pagination-info">Menampilkan <?= min($total_results,$offset+1) ?>–<?= min($total_results,$offset+$results_per_page) ?> dari <?= $total_results ?> pesanan</div>
                <div class="pagination-links">
                    <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>" class="page-btn <?= $page<=1?'disabled':'' ?>"><i class="fas fa-arrow-left"></i> Prev</a>
                    <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>" class="page-btn <?= $page>=$total_pages?'disabled':'' ?>">Next <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="copy-toast" id="copyToast">
    <i class="fas fa-check-circle"></i>
    <span>Kunci berhasil disalin!</span>
</div>

<script>
    const sidebar=document.getElementById('sidebar'),overlay=document.getElementById('overlay');
    document.getElementById('menuToggle').addEventListener('click',()=>sidebar.classList.contains('open')?closeMenu():openMenu());
    overlay.addEventListener('click',closeMenu);
    function openMenu(){sidebar.classList.add('open');overlay.classList.add('visible')}
    function closeMenu(){sidebar.classList.remove('open');overlay.classList.remove('visible')}

    // Copy to clipboard function
    function copyToClipboard(element, text) {
        // Gunakan Clipboard API modern
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(() => {
                showCopyToast();
            }).catch(err => {
                // Fallback jika gagal
                fallbackCopy(text);
            });
        } else {
            // Fallback untuk browser lama
            fallbackCopy(text);
        }
    }

    // Fallback copy method untuk browser yang tidak support Clipboard API
    function fallbackCopy(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        
        try {
            document.execCommand('copy');
            showCopyToast();
        } catch (err) {
            console.error('Gagal menyalin:', err);
        }
        
        document.body.removeChild(textarea);
    }

    // Show toast notification
    function showCopyToast() {
        const toast = document.getElementById('copyToast');
        toast.classList.add('show');
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 2000);
    }
</script>
</body>
</html>