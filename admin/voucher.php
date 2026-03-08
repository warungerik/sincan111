<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include '../includes/koneksi.php';

$result     = $koneksi->query("SELECT * FROM voucher ORDER BY id DESC");
$total      = $result ? $result->num_rows : 0;
$aktif      = $koneksi->query("SELECT COUNT(*) as c FROM voucher WHERE status='aktif'")->fetch_assoc()['c'];
$nonaktif   = $total - $aktif;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher — AdminStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --bg:#f7f5f2;--surface:#fff;--surface2:#faf9f7;--border:#e8e4de;--border2:#f0ede8;
            --text:#1a1714;--muted:#8c8279;--muted2:#b5afa7;
            --accent:#c17f3e;--accent-bg:rgba(193,127,62,0.08);--accent-border:rgba(193,127,62,0.25);
            --success:#2d7a4f;--success-bg:rgba(45,122,79,0.08);
            --info:#2563a8;--info-bg:rgba(37,99,168,0.08);
            --warn:#b45309;--warn-bg:rgba(180,83,9,0.08);
            --danger:#b91c1c;--danger-bg:rgba(185,28,28,0.08);
            --neutral:#6b7280;--neutral-bg:rgba(107,114,128,0.08);
            --sidebar-w:240px;--font:'Plus Jakarta Sans',sans-serif;--mono:'JetBrains Mono',monospace;
            --radius:14px;--shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.04);
            --shadow-lg:0 2px 8px rgba(0,0,0,.08),0 12px 32px rgba(0,0,0,.06);
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:var(--font);background:var(--bg);color:var(--text);min-height:100vh;display:flex}

        /* ── Sidebar ── */
        .sidebar{width:var(--sidebar-w);background:var(--surface);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:100;transition:transform .3s cubic-bezier(.4,0,.2,1)}
        .sidebar-brand{padding:28px 24px 24px;border-bottom:1px solid var(--border2)}
        .logo-mark{width:36px;height:36px;background:linear-gradient(135deg,var(--accent),#e09b55);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;margin-bottom:10px;box-shadow:0 4px 12px rgba(193,127,62,.3)}
        .brand-name{font-weight:800;font-size:15px;letter-spacing:-.01em}
        .brand-sub{font-size:11px;color:var(--muted);font-weight:500;margin-top:2px}
        .sidebar-nav{padding:16px 12px;flex:1;overflow-y:auto}
        .nav-section-label{font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--muted2);padding:0 12px;margin-bottom:6px;margin-top:16px}
        .nav-section-label:first-child{margin-top:0}
        .nav-link{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;text-decoration:none;color:var(--muted);font-size:13.5px;font-weight:600;transition:all .15s;margin-bottom:2px}
        .nav-link .nav-icon{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:13px;background:transparent;transition:all .15s;flex-shrink:0}
        .nav-link:hover{color:var(--text);background:var(--surface2)}.nav-link:hover .nav-icon{background:var(--bg)}
        .nav-link.active{color:var(--accent);background:var(--accent-bg)}.nav-link.active .nav-icon{background:var(--accent-bg);color:var(--accent)}
        .sidebar-footer{padding:16px 12px;border-top:1px solid var(--border2)}
        .nav-link.logout{color:var(--danger)}.nav-link.logout:hover{background:var(--danger-bg)}
        .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.3);z-index:99;backdrop-filter:blur(2px)}

        /* ── Main ── */
        .main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-width:0}
        .topbar{background:var(--surface);border-bottom:1px solid var(--border);padding:0 28px;height:64px;display:flex;align-items:center;justify-content:space-between;gap:16px;position:sticky;top:0;z-index:50}
        .topbar-left{display:flex;align-items:center;gap:12px}
        .menu-toggle{display:none;width:36px;height:36px;border:1px solid var(--border);border-radius:9px;background:var(--surface);cursor:pointer;align-items:center;justify-content:center;color:var(--muted);font-size:14px;transition:all .15s}
        .menu-toggle:hover{background:var(--bg);color:var(--text)}
        .breadcrumb{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--muted)}
        .breadcrumb .crumb-current{color:var(--text);font-weight:700}
        .topbar-badge{background:var(--surface2);border:1px solid var(--border);border-radius:99px;padding:6px 14px;font-size:12px;font-weight:600;color:var(--muted);white-space:nowrap}
        .topbar-badge span{color:var(--text);font-family:var(--mono)}

        /* ── Content ── */
        .content{padding:28px;flex:1}
        .page-title{font-size:clamp(20px,3vw,26px);font-weight:800;letter-spacing:-.02em;margin-bottom:4px}
        .page-title span{color:var(--accent)}
        .page-subtitle{font-size:13px;color:var(--muted);margin-bottom:28px;margin-top:4px}

        /* ── Stat row ── */
        .stat-row{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px}
        .stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:14px;box-shadow:var(--shadow);transition:transform .2s,box-shadow .2s}
        .stat-card:hover{transform:translateY(-1px);box-shadow:var(--shadow-lg)}
        .stat-icon-box{width:40px;height:40px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
        .stat-icon-box.amber{background:var(--warn-bg);color:var(--warn)}
        .stat-icon-box.green{background:var(--success-bg);color:var(--success)}
        .stat-icon-box.grey{background:var(--neutral-bg);color:var(--neutral)}
        .stat-num{font-size:24px;font-weight:800;line-height:1;font-family:var(--mono)}
        .stat-label{font-size:12px;color:var(--muted);font-weight:600;margin-top:3px}

        /* ── Table card ── */
        .table-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;animation:fadeUp .4s ease both}
        .table-header{padding:16px 22px;border-bottom:1px solid var(--border2);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
        .table-header h2{font-size:15px;font-weight:700;display:flex;align-items:center;gap:8px}

        .btn-primary{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;background:var(--accent);color:#fff;border-radius:10px;text-decoration:none;font-weight:700;font-size:13px;border:none;cursor:pointer;transition:all .15s;box-shadow:0 2px 8px rgba(193,127,62,.25)}
        .btn-primary:hover{background:#a96d31;box-shadow:0 4px 14px rgba(193,127,62,.35);transform:translateY(-1px)}

        .search-box{position:relative}
        .search-box input{padding:8px 12px 8px 34px;background:var(--bg);border:1px solid var(--border);border-radius:8px;font-family:var(--font);font-size:12.5px;color:var(--text);width:180px;outline:none;transition:all .15s}
        .search-box input::placeholder{color:var(--muted2)}
        .search-box input:focus{border-color:var(--accent-border);box-shadow:0 0 0 3px var(--accent-bg);width:210px;background:var(--surface)}
        .search-box .si{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted2);font-size:12px;pointer-events:none}

        .table-inner{overflow-x:auto}
        table{width:100%;border-collapse:collapse;min-width:700px}
        thead th{padding:11px 18px;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);background:var(--surface2);border-bottom:1px solid var(--border);text-align:left;white-space:nowrap}
        tbody tr{border-bottom:1px solid var(--border2);transition:background .1s}
        tbody tr:last-child{border-bottom:none}
        tbody tr:hover{background:var(--surface2)}
        td{padding:13px 18px;font-size:13.5px;vertical-align:middle}

        .voucher-code{font-family:var(--mono);font-size:13px;font-weight:600;background:var(--bg);border:1px solid var(--border);padding:5px 10px;border-radius:7px;display:inline-flex;align-items:center;gap:6px;cursor:pointer;transition:all .15s;color:var(--text);letter-spacing:.05em}
        .voucher-code:hover{border-color:var(--accent-border);background:var(--accent-bg)}
        .voucher-code.copied{border-color:var(--success);background:var(--success-bg);color:var(--success)}

        .tipe-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:700;font-family:var(--mono);white-space:nowrap}
        .tipe-persen{background:var(--info-bg);color:var(--info);border:1px solid rgba(37,99,168,.2)}
        .tipe-nominal{background:var(--success-bg);color:var(--success);border:1px solid rgba(45,122,79,.2)}

        .nilai-cell{font-family:var(--mono);font-weight:700;font-size:13px}
        .nilai-cell.persen{color:var(--info)}
        .nilai-cell.nominal{color:var(--success)}

        .kat-chips{display:flex;flex-wrap:wrap;gap:5px;max-width:220px}
        .kat-chip{display:inline-block;padding:3px 8px;border-radius:6px;background:var(--accent-bg);border:1px solid var(--accent-border);color:var(--accent);font-size:11px;font-weight:600;white-space:nowrap}
        .kat-all{font-style:italic;color:var(--muted);font-size:12px}

        .status-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:700;font-family:var(--mono);white-space:nowrap}
        .status-badge::before{content:'';width:5px;height:5px;border-radius:50%;flex-shrink:0}
        .status-aktif{background:var(--success-bg);color:var(--success);border:1px solid rgba(45,122,79,.2)}.status-aktif::before{background:var(--success)}
        .status-tidak-aktif{background:var(--neutral-bg);color:var(--neutral);border:1px solid rgba(107,114,128,.2)}.status-tidak-aktif::before{background:var(--neutral)}

        .exp-date{font-family:var(--mono);font-size:12px;color:var(--muted)}
        .exp-date.expired{color:var(--danger)}

        .action-group{display:inline-flex;align-items:center;gap:6px}
        .btn-icon{width:32px;height:32px;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:12px;transition:all .15s;border:1px solid transparent;cursor:pointer}
        .btn-icon.edit{background:var(--info-bg);color:var(--info);border-color:rgba(37,99,168,.15)}.btn-icon.edit:hover{background:var(--info);color:#fff}
        .btn-icon.del{background:var(--danger-bg);color:var(--danger);border-color:rgba(185,28,28,.15)}.btn-icon.del:hover{background:var(--danger);color:#fff}

        .empty-state{text-align:center;padding:64px 20px;color:var(--muted)}
        .empty-icon{font-size:40px;opacity:.2;margin-bottom:12px}
        .empty-state p{font-size:13px}

        @keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
        .stat-row{animation:fadeUp .4s ease both}
        .table-card{animation:fadeUp .4s .08s ease both}

        @media(max-width:1024px){:root{--sidebar-w:210px}}
        @media(max-width:768px){
            body{display:block}.sidebar{transform:translateX(calc(-1 * var(--sidebar-w)))}
            .sidebar.open{transform:translateX(0);box-shadow:var(--shadow-lg)}
            .sidebar-overlay.visible{display:block}.main{margin-left:0}
            .menu-toggle{display:inline-flex}.content{padding:16px}.topbar{padding:0 16px}
            .stat-row{grid-template-columns:1fr 1fr}.topbar-badge{display:none}
        }
        @media(max-width:480px){.stat-row{grid-template-columns:1fr}}
    </style>
</head>
<body>

<div class="sidebar-overlay" id="overlay"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo-mark"><i class="fas fa-store"></i></div>
        <div class="brand-name">DASHBOARD ADMIN</div>
        <div class="brand-sub">Panel Manajemen</div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Utama</div>
        <a href="index.php" class="nav-link"><span class="nav-icon"><i class="fas fa-box-open"></i></span>Produk</a>
        <a href="kategori.php" class="nav-link"><span class="nav-icon"><i class="fas fa-tags"></i></span>Kategori</a>
        <a href="voucher.php" class="nav-link active"><span class="nav-icon"><i class="fas fa-ticket-alt"></i></span>Voucher</a>
        <div class="nav-section-label">Laporan & Lainnya</div>
        <a href="testimoni_admin.php" class="nav-link"><span class="nav-icon"><i class="fas fa-comment-dots"></i></span>Testimoni</a>
        <a href="pesanan_sukses.php" class="nav-link"><span class="nav-icon"><i class="fas fa-file-invoice"></i></span>Laporan</a>
        <a href="admin_request.php" class="nav-link"><span class="nav-icon"><i class="fas fa-inbox"></i></span>Request</a>
    </nav>
    <div class="sidebar-footer">
        <a href="logout.php" class="nav-link logout"><span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>Logout</a>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
            <div class="breadcrumb">
                <span>Admin</span>
                <i class="fas fa-chevron-right" style="font-size:9px;"></i>
                <span class="crumb-current">Voucher</span>
            </div>
        </div>
        <div class="topbar-badge">Total Voucher: <span><?= $total ?></span></div>
    </header>

    <div class="content">
        <h1 class="page-title">Kelola <span>Voucher</span></h1>
        <p class="page-subtitle">Buat dan kelola kode diskon untuk pelanggan.</p>

        <!-- Stats -->
        <div class="stat-row">
            <div class="stat-card">
                <div class="stat-icon-box amber"><i class="fas fa-ticket-alt"></i></div>
                <div><div class="stat-num"><?= $total ?></div><div class="stat-label">Total Voucher</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-box green"><i class="fas fa-check-circle"></i></div>
                <div><div class="stat-num"><?= $aktif ?></div><div class="stat-label">Aktif</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-box grey"><i class="fas fa-ban"></i></div>
                <div><div class="stat-num"><?= $nonaktif ?></div><div class="stat-label">Tidak Aktif</div></div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-card">
            <div class="table-header">
                <h2><i class="fas fa-list" style="color:var(--accent);font-size:13px;"></i> Daftar Voucher</h2>
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                    <div class="search-box">
                        <i class="fas fa-search si"></i>
                        <input type="text" id="voucher-search" placeholder="Cari kode...">
                    </div>
                    <a href="voucher_edit.php" class="btn-primary"><i class="fas fa-plus"></i> Tambah Voucher</a>
                </div>
            </div>

            <div class="table-inner">
                <table>
                    <thead>
                        <tr>
                            <th>Kode Voucher</th>
                            <th>Tipe</th>
                            <th>Nilai</th>
                            <th>Kategori Berlaku</th>
                            <th>Status</th>
                            <th>Kadaluarsa</th>
                            <th style="text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="voucher-tbody">
                        <?php if ($total > 0): $result->data_seek(0); while ($row = $result->fetch_assoc()):
                            $vid = $row['id'];
                            $kategori_list = [];
                            $res_kat = $koneksi->query("SELECT k.nama_kategori FROM kategori k JOIN voucher_kategori vk ON k.id = vk.kategori_id WHERE vk.voucher_id = $vid ORDER BY k.nama_kategori");
                            if ($res_kat && $res_kat->num_rows > 0) {
                                while ($rk = $res_kat->fetch_assoc()) $kategori_list[] = htmlspecialchars($rk['nama_kategori']);
                            }
                            $is_expired = $row['tanggal_kadaluarsa'] && strtotime($row['tanggal_kadaluarsa']) < time();
                        ?>
                        <tr data-search="<?= strtolower(htmlspecialchars($row['kode_voucher'])) ?>">
                            <td>
                                <span class="voucher-code" onclick="copyCode(this)" title="Klik untuk salin">
                                    <i class="fas fa-copy" style="font-size:10px;opacity:.5;"></i>
                                    <?= htmlspecialchars($row['kode_voucher']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="tipe-badge <?= $row['tipe_diskon'] == 'persen' ? 'tipe-persen' : 'tipe-nominal' ?>">
                                    <?= $row['tipe_diskon'] == 'persen' ? '% Persen' : 'Rp Nominal' ?>
                                </span>
                            </td>
                            <td>
                                <span class="nilai-cell <?= $row['tipe_diskon'] == 'persen' ? 'persen' : 'nominal' ?>">
                                    <?php if ($row['tipe_diskon'] == 'persen'): ?>
                                        <?= rtrim(rtrim(number_format($row['nilai_diskon'], 2, ',', ''), '0'), ',') ?>%
                                    <?php else: ?>
                                        Rp <?= number_format($row['nilai_diskon'], 0, ',', '.') ?>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <?php if (empty($kategori_list)): ?>
                                    <span class="kat-all">Semua Kategori</span>
                                <?php else: ?>
                                    <div class="kat-chips">
                                        <?php foreach ($kategori_list as $k): ?>
                                            <span class="kat-chip"><?= $k ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?= str_replace(' ', '-', $row['status']) ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['tanggal_kadaluarsa']): ?>
                                    <span class="exp-date <?= $is_expired ? 'expired' : '' ?>">
                                        <?= $is_expired ? '<i class="fas fa-exclamation-triangle" style="font-size:10px;margin-right:3px;"></i>' : '' ?>
                                        <?= date('d M Y', strtotime($row['tanggal_kadaluarsa'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color:var(--muted);font-size:12px;">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:center;">
                                <div class="action-group">
                                    <a href="voucher_edit.php?id=<?= $row['id'] ?>" class="btn-icon edit" title="Edit"><i class="fas fa-pen"></i></a>
                                    <a href="voucher_proses.php?aksi=hapus&id=<?= $row['id'] ?>" class="btn-icon del" title="Hapus"
                                       onclick="return confirm('Yakin hapus voucher ini?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-ticket-alt"></i></div>
                                <p>Belum ada voucher. <a href="voucher_edit.php" style="color:var(--accent);font-weight:700;">Tambah sekarang →</a></p>
                            </div>
                        </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    document.getElementById('menuToggle').addEventListener('click', () =>
        sidebar.classList.contains('open') ? closeMenu() : openMenu());
    overlay.addEventListener('click', closeMenu);
    function openMenu()  { sidebar.classList.add('open'); overlay.classList.add('visible'); }
    function closeMenu() { sidebar.classList.remove('open'); overlay.classList.remove('visible'); }

    document.getElementById('voucher-search').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#voucher-tbody tr[data-search]').forEach(tr => {
            tr.style.display = tr.dataset.search.includes(q) ? '' : 'none';
        });
    });

    function copyCode(el) {
        const text = el.textContent.trim();
        navigator.clipboard.writeText(text).then(() => {
            el.classList.add('copied');
            const orig = el.innerHTML;
            el.innerHTML = '<i class="fas fa-check" style="font-size:10px;"></i> Tersalin!';
            setTimeout(() => { el.classList.remove('copied'); el.innerHTML = orig; }, 1500);
        });
    }
</script>
</body>
</html>