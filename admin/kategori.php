<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include '../includes/koneksi.php';

$result = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
$total  = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori — AdminStore</title>
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
            --danger:#b91c1c;--danger-bg:rgba(185,28,28,0.08);
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
        .content-inner{max-width:820px;margin:0 auto}
        .page-title{font-size:clamp(20px,3vw,26px);font-weight:800;letter-spacing:-.02em;margin-bottom:4px}
        .page-title span{color:var(--accent)}
        .page-subtitle{font-size:13px;color:var(--muted);margin-bottom:28px;margin-top:4px}

        /* ── Grid ── */
        .grid-layout{display:grid;grid-template-columns:320px 1fr;gap:20px;align-items:start}

        /* ── Card ── */
        .card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;animation:fadeUp .4s ease both}
        .card:nth-child(2){animation-delay:.07s}
        .card-header{padding:18px 22px;border-bottom:1px solid var(--border2);display:flex;align-items:center;justify-content:space-between;gap:10px}
        .card-header h3{font-size:15px;font-weight:700;display:flex;align-items:center;gap:8px}
        .card-icon{width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px}
        .card-icon.green{background:var(--success-bg);color:var(--success)}
        .card-icon.amber{background:var(--accent-bg);color:var(--accent)}
        .card-body{padding:22px}

        /* ── Add form ── */
        .field{display:flex;flex-direction:column;gap:6px;margin-bottom:14px}
        .field label{font-size:12px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:var(--muted)}
        .field input[type="text"]{padding:11px 14px;background:var(--bg);border:1px solid var(--border);border-radius:10px;font-family:var(--font);font-size:13.5px;color:var(--text);outline:none;transition:border-color .15s,box-shadow .15s,background .15s;width:100%}
        .field input:focus{border-color:var(--accent-border);box-shadow:0 0 0 3px var(--accent-bg);background:var(--surface)}

        .btn-add-cat{width:100%;padding:11px 18px;background:var(--accent);color:#fff;border:none;border-radius:10px;font-family:var(--font);font-size:13.5px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .15s;box-shadow:0 2px 8px rgba(193,127,62,.25)}
        .btn-add-cat:hover{background:#a96d31;box-shadow:0 4px 14px rgba(193,127,62,.35);transform:translateY(-1px)}

        /* ── Table ── */
        .table-toolbar{padding:14px 22px;border-bottom:1px solid var(--border2);display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}
        .entry-count{font-size:12px;font-family:var(--mono);color:var(--muted)}

        .search-box{position:relative}
        .search-box input{padding:8px 12px 8px 34px;background:var(--bg);border:1px solid var(--border);border-radius:8px;font-family:var(--font);font-size:12.5px;color:var(--text);width:170px;outline:none;transition:all .15s}
        .search-box input::placeholder{color:var(--muted2)}
        .search-box input:focus{border-color:var(--accent-border);box-shadow:0 0 0 3px var(--accent-bg);width:200px;background:var(--surface)}
        .search-box .si{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted2);font-size:12px;pointer-events:none}

        table{width:100%;border-collapse:collapse}
        thead th{padding:11px 18px;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);background:var(--surface2);border-bottom:1px solid var(--border);text-align:left}
        tbody tr{border-bottom:1px solid var(--border2);transition:background .1s}
        tbody tr:last-child{border-bottom:none}
        tbody tr:hover{background:var(--surface2)}
        td{padding:12px 18px;font-size:13.5px;vertical-align:middle}

        /* Inline edit */
        .inline-form{display:flex;align-items:center;gap:8px}
        .inline-form input[type="text"]{flex:1;padding:8px 12px;background:var(--bg);border:1px solid var(--border);border-radius:8px;font-family:var(--font);font-size:13px;color:var(--text);outline:none;transition:border-color .15s,box-shadow .15s}
        .inline-form input:focus{border-color:var(--accent-border);box-shadow:0 0 0 3px var(--accent-bg);background:var(--surface)}

        .btn-icon-sm{width:30px;height:30px;border-radius:7px;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:12px;transition:all .15s;border:1px solid transparent;cursor:pointer;background:none;font-family:var(--font)}
        .btn-icon-sm.save{background:var(--info-bg);color:var(--info);border-color:rgba(37,99,168,.15)}
        .btn-icon-sm.save:hover{background:var(--info);color:#fff}
        .btn-icon-sm.del{background:var(--danger-bg);color:var(--danger);border-color:rgba(185,28,28,.15)}
        .btn-icon-sm.del:hover{background:var(--danger);color:#fff}

        .empty-state{text-align:center;padding:52px 20px;color:var(--muted)}
        .empty-icon{font-size:36px;opacity:.2;margin-bottom:10px}
        .empty-state p{font-size:13px}

        @keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}

        /* Responsive */
        @media(max-width:1024px){:root{--sidebar-w:210px}}
        @media(max-width:800px){.grid-layout{grid-template-columns:1fr}}
        @media(max-width:768px){
            body{display:block}.sidebar{transform:translateX(calc(-1 * var(--sidebar-w)))}
            .sidebar.open{transform:translateX(0);box-shadow:var(--shadow-lg)}
            .sidebar-overlay.visible{display:block}.main{margin-left:0}
            .menu-toggle{display:inline-flex}.content{padding:16px}.topbar{padding:0 16px}
            .topbar-badge{display:none}
        }
        @media(max-width:480px){
            .search-box input{width:130px}.search-box input:focus{width:155px}
        }
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
        <a href="kategori.php" class="nav-link active"><span class="nav-icon"><i class="fas fa-tags"></i></span>Kategori</a>
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
                <span>Admin</span>
                <i class="fas fa-chevron-right" style="font-size:9px;"></i>
                <span class="crumb-current">Kategori</span>
            </div>
        </div>
        <div class="topbar-badge">Total Kategori: <span><?= $total ?></span></div>
    </header>

    <div class="content">
        <div class="content-inner">
            <h1 class="page-title">Kelola <span>Kategori</span></h1>
            <p class="page-subtitle">Tambah, edit, atau hapus kategori produk.</p>

            <div class="grid-layout">

                <!-- Form Tambah -->
                <div class="card">
                    <div class="card-header">
                        <h3><span class="card-icon green"><i class="fas fa-plus"></i></span>Tambah Kategori</h3>
                    </div>
                    <div class="card-body">
                        <form action="kategori_proses.php" method="post">
                            <div class="field">
                                <label for="nama_kategori">Nama Kategori</label>
                                <input type="text" id="nama_kategori" name="nama_kategori"
                                       placeholder="Contoh: Software, Game, dll." required>
                            </div>
                            <button type="submit" name="tambah_kategori" class="btn-add-cat">
                                <i class="fas fa-plus"></i> Tambah Kategori
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Daftar Kategori -->
                <div class="card">
                    <div class="card-header">
                        <h3><span class="card-icon amber"><i class="fas fa-list"></i></span>Daftar Kategori</h3>
                    </div>

                    <div class="table-toolbar">
                        <span class="entry-count"><?= $total ?> kategori</span>
                        <div class="search-box">
                            <i class="fas fa-search si"></i>
                            <input type="text" id="cat-search" placeholder="Cari kategori...">
                        </div>
                    </div>

                    <div style="overflow-x:auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama Kategori</th>
                                    <th style="width:90px;text-align:center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cat-tbody">
                                <?php if ($total > 0): mysqli_data_seek($result, 0); while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr data-cat="<?= strtolower(htmlspecialchars($row['nama_kategori'])) ?>">
                                    <td>
                                        <form action="kategori_proses.php" method="post" class="inline-form">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <input type="text" name="nama_kategori"
                                                   value="<?= htmlspecialchars($row['nama_kategori']) ?>" required>
                                            <button type="submit" name="update_kategori" class="btn-icon-sm save" title="Simpan">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td style="text-align:center;">
                                        <a href="kategori_proses.php?aksi=hapus&id=<?= $row['id'] ?>"
                                           class="btn-icon-sm del" title="Hapus"
                                           onclick="return confirm('Yakin hapus kategori ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="2">
                                        <div class="empty-state">
                                            <div class="empty-icon"><i class="fas fa-tags"></i></div>
                                            <p>Belum ada kategori. Tambahkan yang pertama!</p>
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

    document.getElementById('cat-search').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#cat-tbody tr[data-cat]').forEach(tr => {
            tr.style.display = tr.dataset.cat.includes(q) ? '' : 'none';
        });
    });
</script>
</body>
</html>