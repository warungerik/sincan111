<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include '../includes/koneksi.php';

$produk_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($produk_id <= 0) die("ID Produk tidak valid.");

$stmt_produk = $koneksi->prepare("SELECT nama_produk FROM produk WHERE id = ?");
$stmt_produk->bind_param("i", $produk_id);
$stmt_produk->execute();
$produk = $stmt_produk->get_result()->fetch_assoc();
if (!$produk) die("Produk tidak ditemukan.");

if (isset($_POST['tambah_keys'])) {
    $keys_list = trim($_POST['keys_list']);
    if (!empty($keys_list)) {
        $keys = explode("\n", str_replace("\r", "", $keys_list));
        $stmt_insert = $koneksi->prepare("INSERT INTO produk_keys (produk_id, key_value) VALUES (?, ?)");
        foreach ($keys as $key) {
            $key = trim($key);
            if (!empty($key)) { $stmt_insert->bind_param("is", $produk_id, $key); $stmt_insert->execute(); }
        }
        $stmt_count = $koneksi->prepare("SELECT COUNT(*) as total FROM produk_keys WHERE produk_id = ? AND status = 'tersedia'");
        $stmt_count->bind_param("i", $produk_id);
        $stmt_count->execute();
        $total_stok = $stmt_count->get_result()->fetch_assoc()['total'];
        $s = $koneksi->prepare("UPDATE produk SET stok = ? WHERE id = ?");
        $s->bind_param("ii", $total_stok, $produk_id); $s->execute();
    }
    header("Location: kelola_keys.php?id=" . $produk_id); exit();
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['key_id'])) {
    $kid = (int)$_GET['key_id'];
    $d = $koneksi->prepare("DELETE FROM produk_keys WHERE id = ? AND produk_id = ?");
    $d->bind_param("ii", $kid, $produk_id); $d->execute();
    $stmt_count = $koneksi->prepare("SELECT COUNT(*) as total FROM produk_keys WHERE produk_id = ? AND status = 'tersedia'");
    $stmt_count->bind_param("i", $produk_id); $stmt_count->execute();
    $total_stok = $stmt_count->get_result()->fetch_assoc()['total'];
    $s = $koneksi->prepare("UPDATE produk SET stok = ? WHERE id = ?");
    $s->bind_param("ii", $total_stok, $produk_id); $s->execute();
    header("Location: kelola_keys.php?id=" . $produk_id); exit();
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus_semua') {
    $d = $koneksi->prepare("DELETE FROM produk_keys WHERE produk_id = ?");
    $d->bind_param("i", $produk_id); $d->execute();
    $z = 0; $s = $koneksi->prepare("UPDATE produk SET stok = ? WHERE id = ?");
    $s->bind_param("ii", $z, $produk_id); $s->execute();
    header("Location: kelola_keys.php?id=" . $produk_id); exit();
}

$list_keys  = mysqli_query($koneksi, "SELECT * FROM produk_keys WHERE produk_id = $produk_id ORDER BY id DESC");
$total_keys = mysqli_num_rows($list_keys);
$tersedia   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM produk_keys WHERE produk_id=$produk_id AND status='tersedia'"))['c'];
$terjual    = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM produk_keys WHERE produk_id=$produk_id AND status='terjual'"))['c'];
mysqli_data_seek($list_keys, 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kunci — <?= htmlspecialchars($produk['nama_produk']) ?></title>
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
            --sidebar-w:240px;--font:'Plus Jakarta Sans',sans-serif;--mono:'JetBrains Mono',monospace;
            --radius:14px;--shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.04);
            --shadow-lg:0 2px 8px rgba(0,0,0,.08),0 12px 32px rgba(0,0,0,.06);
        }
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:var(--font);background:var(--bg);color:var(--text);min-height:100vh;display:flex}
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
        .main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-width:0}
        .topbar{background:var(--surface);border-bottom:1px solid var(--border);padding:0 28px;height:64px;display:flex;align-items:center;justify-content:space-between;gap:16px;position:sticky;top:0;z-index:50}
        .topbar-left{display:flex;align-items:center;gap:12px}
        .menu-toggle{display:none;width:36px;height:36px;border:1px solid var(--border);border-radius:9px;background:var(--surface);cursor:pointer;align-items:center;justify-content:center;color:var(--muted);font-size:14px;transition:all .15s}
        .menu-toggle:hover{background:var(--bg);color:var(--text)}
        .breadcrumb{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--muted)}
        .breadcrumb .crumb-current{color:var(--text);font-weight:700}
        .back-btn{display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:var(--surface2);border:1px solid var(--border);border-radius:9px;text-decoration:none;color:var(--muted);font-size:13px;font-weight:600;transition:all .15s}
        .back-btn:hover{background:var(--bg);color:var(--text);border-color:var(--accent-border)}
        .content{padding:28px;flex:1;max-width:1000px}
        .page-title{font-size:clamp(20px,3vw,26px);font-weight:800;letter-spacing:-.02em;margin-bottom:4px}
        .page-title span{color:var(--accent)}
        .page-subtitle{font-size:13px;color:var(--muted);margin-bottom:28px}
        .stat-row{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:28px}
        .stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:18px 20px;display:flex;align-items:center;gap:14px;box-shadow:var(--shadow);transition:transform .2s,box-shadow .2s}
        .stat-card:hover{transform:translateY(-1px);box-shadow:var(--shadow-lg)}
        .stat-icon-box{width:40px;height:40px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
        .stat-icon-box.amber{background:var(--warn-bg);color:var(--warn)}
        .stat-icon-box.green{background:var(--success-bg);color:var(--success)}
        .stat-icon-box.red{background:var(--danger-bg);color:var(--danger)}
        .stat-num{font-size:24px;font-weight:800;line-height:1;font-family:var(--mono)}
        .stat-label{font-size:12px;color:var(--muted);font-weight:600;margin-top:3px}
        .grid-layout{display:grid;grid-template-columns:1fr 1.7fr;gap:20px;align-items:start}
        .card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden}
        .card-header{padding:18px 22px;border-bottom:1px solid var(--border2);display:flex;align-items:center;justify-content:space-between;gap:12px}
        .card-header h3{font-size:15px;font-weight:700;display:flex;align-items:center;gap:8px}
        .card-icon{width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px}
        .card-icon.green{background:var(--success-bg);color:var(--success)}
        .card-icon.amber{background:var(--warn-bg);color:var(--warn)}
        .card-body{padding:22px}
        .form-label{display:block;font-size:12px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:var(--muted);margin-bottom:8px}
        .form-hint{font-size:12px;color:var(--muted2);margin-bottom:12px;line-height:1.5}
        textarea{width:100%;min-height:160px;padding:12px 14px;background:var(--bg);border:1px solid var(--border);border-radius:10px;font-family:var(--mono);font-size:13px;color:var(--text);resize:vertical;outline:none;transition:border-color .15s,box-shadow .15s;line-height:1.7}
        textarea:focus{border-color:var(--accent-border);box-shadow:0 0 0 3px var(--accent-bg);background:var(--surface)}
        .btn-submit{margin-top:14px;width:100%;padding:12px 20px;border:none;background:var(--accent);color:#fff;border-radius:10px;font-family:var(--font);font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .15s;box-shadow:0 2px 8px rgba(193,127,62,.25)}
        .btn-submit:hover{background:#a96d31;box-shadow:0 4px 14px rgba(193,127,62,.35);transform:translateY(-1px)}
        .table-toolbar{padding:14px 22px;border-bottom:1px solid var(--border2);display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}
        .entry-count{font-size:12px;font-family:var(--mono);color:var(--muted)}
        .btn-danger-sm{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;background:var(--danger-bg);color:var(--danger);border:1px solid rgba(185,28,28,.2);font-size:12px;font-weight:700;text-decoration:none;font-family:var(--font);transition:all .15s;cursor:pointer}
        .btn-danger-sm:hover{background:var(--danger);color:#fff}
        .search-box{position:relative}
        .search-box input{padding:8px 12px 8px 34px;background:var(--bg);border:1px solid var(--border);border-radius:8px;font-family:var(--font);font-size:12.5px;color:var(--text);width:180px;outline:none;transition:all .15s}
        .search-box input::placeholder{color:var(--muted2)}
        .search-box input:focus{border-color:var(--accent-border);box-shadow:0 0 0 3px var(--accent-bg);width:210px;background:var(--surface)}
        .search-box .si{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted2);font-size:12px;pointer-events:none}
        table{width:100%;border-collapse:collapse}
        thead th{padding:11px 18px;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);background:var(--surface2);border-bottom:1px solid var(--border);text-align:left}
        tbody tr{border-bottom:1px solid var(--border2);transition:background .1s}
        tbody tr:last-child{border-bottom:none}
        tbody tr:hover{background:var(--surface2)}
        td{padding:13px 18px;font-size:13px;vertical-align:middle}
        .key-value{font-family:var(--mono);font-size:12.5px;background:var(--bg);border:1px solid var(--border);padding:5px 10px;border-radius:7px;display:inline-block;max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;cursor:pointer;transition:all .15s;color:var(--text)}
        .key-value:hover{border-color:var(--accent-border);background:var(--accent-bg)}
        .key-value.copied{border-color:var(--success);background:var(--success-bg);color:var(--success)}
        .status-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:700;font-family:var(--mono);white-space:nowrap}
        .status-badge::before{content:'';width:5px;height:5px;border-radius:50%;flex-shrink:0}
        .status-tersedia{background:var(--success-bg);color:var(--success);border:1px solid rgba(45,122,79,.2)}.status-tersedia::before{background:var(--success)}
        .status-terjual{background:var(--danger-bg);color:var(--danger);border:1px solid rgba(185,28,28,.2)}.status-terjual::before{background:var(--danger)}
        .btn-icon-sm{width:30px;height:30px;border-radius:7px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:12px;transition:all .15s;border:1px solid transparent;cursor:pointer}
        .btn-icon-sm.del{background:var(--danger-bg);color:var(--danger);border-color:rgba(185,28,28,.15)}.btn-icon-sm.del:hover{background:var(--danger);color:#fff}
        .empty-state{text-align:center;padding:52px 20px;color:var(--muted)}
        .empty-icon{font-size:36px;opacity:.2;margin-bottom:10px}
        .empty-state p{font-size:13px}
        @keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
        .stat-row{animation:fadeUp .4s ease both}
        .grid-layout{animation:fadeUp .4s .08s ease both}
        @media(max-width:1024px){:root{--sidebar-w:210px}}
        @media(max-width:900px){.grid-layout{grid-template-columns:1fr}}
        @media(max-width:768px){body{display:block}.sidebar{transform:translateX(calc(-1 * var(--sidebar-w)))}.sidebar.open{transform:translateX(0);box-shadow:var(--shadow-lg)}.sidebar-overlay.visible{display:block}.main{margin-left:0}.menu-toggle{display:inline-flex}.content{padding:16px}.topbar{padding:0 16px}.stat-row{grid-template-columns:1fr 1fr}}
        @media(max-width:480px){.stat-row{grid-template-columns:1fr}.search-box input{width:100%}.search-box input:focus{width:100%}}
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
        <a href="voucher.php" class="nav-link"><span class="nav-icon"><i class="fas fa-ticket-alt"></i></span>Voucher</a>
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
                <span>Produk</span>
                <i class="fas fa-chevron-right" style="font-size:9px;"></i>
                <span class="crumb-current">Kelola Kunci</span>
            </div>
        </div>
        <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
    </header>

    <div class="content">
        <h1 class="page-title">Kelola <span>Kunci</span></h1>
        <p class="page-subtitle"><i class="fas fa-box" style="color:var(--accent);margin-right:5px;"></i><?= htmlspecialchars($produk['nama_produk']) ?></p>

        <div class="stat-row">
            <div class="stat-card">
                <div class="stat-icon-box amber"><i class="fas fa-key"></i></div>
                <div><div class="stat-num"><?= $total_keys ?></div><div class="stat-label">Total Kunci</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-box green"><i class="fas fa-check-circle"></i></div>
                <div><div class="stat-num"><?= $tersedia ?></div><div class="stat-label">Tersedia</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-box red"><i class="fas fa-shopping-cart"></i></div>
                <div><div class="stat-num"><?= $terjual ?></div><div class="stat-label">Terjual</div></div>
            </div>
        </div>

        <div class="grid-layout">
            <!-- Form -->
            <div class="card">
                <div class="card-header">
                    <h3><span class="card-icon green"><i class="fas fa-plus"></i></span>Tambah Kunci Baru</h3>
                </div>
                <div class="card-body">
                    <label class="form-label">Daftar Kunci</label>
                    <p class="form-hint">Satu kunci per baris. Stok diperbarui otomatis.</p>
                    <form method="post">
                        <textarea name="keys_list" placeholder="KEY123-ABCD-EFGH&#10;KEY456-IJKL-MNOP&#10;KEY789-QRST-UVWX"></textarea>
                        <button type="submit" name="tambah_keys" class="btn-submit">
                            <i class="fas fa-plus"></i> Tambah Kunci
                        </button>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="card">
                <div class="card-header">
                    <h3><span class="card-icon amber"><i class="fas fa-list"></i></span>Daftar Kunci</h3>
                    <?php if ($total_keys > 0): ?>
                    <a href="kelola_keys.php?id=<?= $produk_id ?>&aksi=hapus_semua" class="btn-danger-sm"
                       onclick="return confirm('PERINGATAN: Hapus SEMUA kunci? Tindakan tidak bisa dibatalkan.')">
                        <i class="fas fa-trash-alt"></i> Hapus Semua
                    </a>
                    <?php endif; ?>
                </div>

                <div class="table-toolbar">
                    <span class="entry-count"><?= $total_keys ?> kunci</span>
                    <div class="search-box">
                        <i class="fas fa-search si"></i>
                        <input type="text" id="key-search" placeholder="Cari kunci...">
                    </div>
                </div>

                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Kunci</th>
                                <th>Status</th>
                                <th style="width:50px;text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="keys-tbody">
                            <?php if ($total_keys > 0): while ($row = mysqli_fetch_assoc($list_keys)): ?>
                            <tr data-key="<?= strtolower(htmlspecialchars($row['key_value'])) ?>">
                                <td>
                                    <span class="key-value" onclick="copyKey(this)" title="Klik untuk salin">
                                        <?= htmlspecialchars($row['key_value']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span>
                                </td>
                                <td style="text-align:center;">
                                    <a href="kelola_keys.php?id=<?= $produk_id ?>&aksi=hapus&key_id=<?= $row['id'] ?>"
                                       class="btn-icon-sm del" title="Hapus"
                                       onclick="return confirm('Yakin hapus kunci ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="3">
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="fas fa-key"></i></div>
                                    <p>Belum ada kunci untuk produk ini.</p>
                                </div>
                            </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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

    document.getElementById('key-search').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#keys-tbody tr[data-key]').forEach(tr => {
            tr.style.display = tr.dataset.key.includes(q) ? '' : 'none';
        });
    });

    function copyKey(el) {
        navigator.clipboard.writeText(el.textContent.trim()).then(() => {
            const orig = el.textContent;
            el.classList.add('copied');
            el.textContent = '✓ Tersalin!';
            setTimeout(() => { el.classList.remove('copied'); el.textContent = orig; }, 1500);
        });
    }
</script>
</body>
</html>