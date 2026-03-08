<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php"); exit();
}
include '../includes/koneksi.php';

$is_edit = false;
$voucher = ['id'=>'','kode_voucher'=>'','tipe_diskon'=>'persen','nilai_diskon'=>'','status'=>'aktif','tanggal_kadaluarsa'=>''];
$kategori_terpilih = [];

if (isset($_GET['id'])) {
    $is_edit = true;
    $id = (int)$_GET['id'];
    $stmt = $koneksi->prepare("SELECT * FROM voucher WHERE id = ?");
    $stmt->bind_param("i", $id); $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $voucher = $result->fetch_assoc();
        $stmt_kat = $koneksi->prepare("SELECT kategori_id FROM voucher_kategori WHERE voucher_id = ?");
        $stmt_kat->bind_param("i", $id); $stmt_kat->execute();
        $result_kat = $stmt_kat->get_result();
        while ($rk = $result_kat->fetch_assoc()) $kategori_terpilih[] = $rk['kategori_id'];
        $stmt_kat->close();
    } else { die("Voucher tidak ditemukan."); }
    $stmt->close();
}

$semua_kategori = [];
$res_kat = $koneksi->query("SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori");
if ($res_kat) while ($r = $res_kat->fetch_assoc()) $semua_kategori[] = $r;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_edit ? 'Edit' : 'Tambah' ?> Voucher — AdminStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root{--bg:#f7f5f2;--surface:#fff;--surface2:#faf9f7;--border:#e8e4de;--border2:#f0ede8;--text:#1a1714;--muted:#8c8279;--muted2:#b5afa7;--accent:#c17f3e;--accent-bg:rgba(193,127,62,0.08);--accent-border:rgba(193,127,62,0.25);--success:#2d7a4f;--success-bg:rgba(45,122,79,0.08);--info:#2563a8;--info-bg:rgba(37,99,168,0.08);--danger:#b91c1c;--danger-bg:rgba(185,28,28,0.08);--sidebar-w:240px;--font:'Plus Jakarta Sans',sans-serif;--mono:'JetBrains Mono',monospace;--radius:14px;--shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.04);--shadow-lg:0 2px 8px rgba(0,0,0,.08),0 12px 32px rgba(0,0,0,.06)}
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
        .back-btn{display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:var(--surface2);border:1px solid var(--border);border-radius:9px;text-decoration:none;color:var(--muted);font-size:13px;font-weight:600;transition:all .15s}
        .back-btn:hover{background:var(--bg);color:var(--text);border-color:var(--accent-border)}
        .content{padding:28px;flex:1}
        .content-inner{max-width:680px;margin:0 auto}
        .page-title{font-size:clamp(20px,3vw,26px);font-weight:800;letter-spacing:-.02em;margin-bottom:4px}.page-title span{color:var(--accent)}
        .page-subtitle{font-size:13px;color:var(--muted);margin-bottom:28px;margin-top:4px}
        .form-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;animation:fadeUp .4s ease both}
        .form-section{padding:22px 26px;border-bottom:1px solid var(--border2)}.form-section:last-child{border-bottom:none}
        .section-label{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-bottom:16px}
        .sl-icon{width:24px;height:24px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:11px}
        .sl-icon.amber{background:var(--accent-bg);color:var(--accent)}.sl-icon.green{background:var(--success-bg);color:var(--success)}.sl-icon.blue{background:var(--info-bg);color:var(--info)}
        .field-group{display:grid;gap:16px}.field-group.cols-2{grid-template-columns:1fr 1fr}
        .field{display:flex;flex-direction:column;gap:6px}
        .field label{font-size:12px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:var(--muted)}
        .field input,.field select{padding:11px 14px;background:var(--bg);border:1px solid var(--border);border-radius:10px;font-family:var(--font);font-size:13.5px;color:var(--text);outline:none;transition:border-color .15s,box-shadow .15s,background .15s;width:100%}
        .field input:focus,.field select:focus{border-color:var(--accent-border);box-shadow:0 0 0 3px var(--accent-bg);background:var(--surface)}
        .field small{font-size:11.5px;color:var(--muted2);line-height:1.4}
        .nilai-preview{font-family:var(--mono);font-size:12px;color:var(--success);font-weight:600;margin-top:2px;min-height:16px}
        /* Checkbox kategori */
        .kat-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;max-height:220px;overflow-y:auto;padding:4px 2px}
        .kat-item{display:flex;align-items:center;gap:9px;padding:9px 12px;background:var(--bg);border:1px solid var(--border);border-radius:9px;cursor:pointer;transition:all .15s;font-size:13px;font-weight:600}
        .kat-item:hover{border-color:var(--accent-border);background:var(--accent-bg)}
        .kat-item input[type="checkbox"]{appearance:none;-webkit-appearance:none;width:16px;height:16px;border:2px solid var(--border);border-radius:4px;background:var(--surface);cursor:pointer;position:relative;transition:all .15s;flex-shrink:0}
        .kat-item input:checked{background:var(--accent);border-color:var(--accent)}
        .kat-item input:checked::after{content:'';position:absolute;top:1px;left:3px;width:5px;height:8px;border:2px solid white;border-top:none;border-left:none;transform:rotate(45deg)}
        .kat-item input:checked ~ span{color:var(--accent)}
        /* Footer */
        .form-footer{padding:18px 26px;background:var(--surface2);border-top:1px solid var(--border2);display:flex;align-items:center;justify-content:flex-end;gap:12px}
        .btn-cancel{display:inline-flex;align-items:center;gap:7px;padding:11px 20px;background:transparent;border:1px solid var(--border);border-radius:10px;text-decoration:none;color:var(--muted);font-size:13.5px;font-weight:700;transition:all .15s;font-family:var(--font);cursor:pointer}
        .btn-cancel:hover{background:var(--bg);color:var(--text)}
        .btn-save{display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:var(--accent);color:#fff;border:none;border-radius:10px;font-family:var(--font);font-size:13.5px;font-weight:700;cursor:pointer;transition:all .15s;box-shadow:0 2px 8px rgba(193,127,62,.25)}
        .btn-save:hover{background:#a96d31;box-shadow:0 4px 14px rgba(193,127,62,.35);transform:translateY(-1px)}
        @keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
        @media(max-width:1024px){:root{--sidebar-w:210px}}
        @media(max-width:768px){body{display:block}.sidebar{transform:translateX(calc(-1 * var(--sidebar-w)))}.sidebar.open{transform:translateX(0);box-shadow:var(--shadow-lg)}.sidebar-overlay.visible{display:block}.main{margin-left:0}.menu-toggle{display:inline-flex}.content{padding:16px}.topbar{padding:0 16px}.field-group.cols-2{grid-template-columns:1fr}.kat-grid{grid-template-columns:1fr}.form-footer{flex-direction:column}.btn-save,.btn-cancel{width:100%;justify-content:center}}
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
        <a href="voucher.php" class="nav-link active"><span class="nav-icon"><i class="fas fa-ticket-alt"></i></span>Voucher</a>
        <div class="nav-section-label">Laporan & Lainnya</div>
        <a href="testimoni_admin.php" class="nav-link"><span class="nav-icon"><i class="fas fa-comment-dots"></i></span>Testimoni</a>
        <a href="pesanan_sukses.php" class="nav-link"><span class="nav-icon"><i class="fas fa-file-invoice"></i></span>Laporan</a>
        <a href="admin_request.php" class="nav-link"><span class="nav-icon"><i class="fas fa-inbox"></i></span>Request</a>
    </nav>
    <div class="sidebar-footer"><a href="logout.php" class="nav-link logout"><span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>Logout</a></div>
</aside>
<div class="main">
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
            <div class="breadcrumb"><span>Voucher</span><i class="fas fa-chevron-right" style="font-size:9px;"></i><span class="crumb-current"><?= $is_edit ? 'Edit' : 'Tambah' ?></span></div>
        </div>
        <a href="voucher.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali</a>
    </header>
    <div class="content">
        <div class="content-inner">
            <h1 class="page-title"><?= $is_edit ? 'Edit <span>Voucher</span>' : 'Tambah <span>Voucher Baru</span>' ?></h1>
            <p class="page-subtitle"><?= $is_edit ? 'Perbarui detail voucher di bawah ini.' : 'Isi detail voucher diskon yang akan dibuat.' ?></p>
            <form action="voucher_proses.php" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($voucher['id']) ?>">
                <div class="form-card">
                    <!-- Kode & Tipe -->
                    <div class="form-section">
                        <div class="section-label"><span class="sl-icon amber"><i class="fas fa-ticket-alt"></i></span>Detail Voucher</div>
                        <div class="field-group">
                            <div class="field">
                                <label for="kode_voucher">Kode Voucher</label>
                                <input type="text" id="kode_voucher" name="kode_voucher" value="<?= htmlspecialchars($voucher['kode_voucher']) ?>" placeholder="Contoh: HEMAT10K" required style="font-family:var(--mono);letter-spacing:.08em;text-transform:uppercase" oninput="this.value=this.value.toUpperCase()">
                                <small>Harus unik. Contoh: DISKON50, HEMAT10K</small>
                            </div>
                            <div class="field-group cols-2">
                                <div class="field">
                                    <label for="tipe_diskon">Tipe Diskon</label>
                                    <select id="tipe_diskon" name="tipe_diskon" onchange="updateNilaiPreview()">
                                        <option value="persen" <?= $voucher['tipe_diskon']=='persen'?'selected':'' ?>>% Persen</option>
                                        <option value="flat" <?= $voucher['tipe_diskon']=='flat'?'selected':'' ?>>Rp Nominal</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label for="nilai_diskon">Nilai Diskon</label>
                                    <input type="number" id="nilai_diskon" name="nilai_diskon" value="<?= htmlspecialchars($voucher['nilai_diskon']) ?>" required step="0.01" min="0" placeholder="0" oninput="updateNilaiPreview()">
                                    <div class="nilai-preview" id="nilai-preview"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Status & Kadaluarsa -->
                    <div class="form-section">
                        <div class="section-label"><span class="sl-icon green"><i class="fas fa-sliders-h"></i></span>Pengaturan</div>
                        <div class="field-group cols-2">
                            <div class="field">
                                <label for="status">Status</label>
                                <select id="status" name="status">
                                    <option value="aktif" <?= $voucher['status']=='aktif'?'selected':'' ?>>✓ Aktif</option>
                                    <option value="tidak_aktif" <?= $voucher['status']=='tidak_aktif'?'selected':'' ?>>✗ Tidak Aktif</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="tanggal_kadaluarsa">Kadaluarsa <span style="font-weight:400;text-transform:none;letter-spacing:0;">(opsional)</span></label>
                                <input type="date" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa" value="<?= htmlspecialchars($voucher['tanggal_kadaluarsa']) ?>">
                                <small>Kosongkan jika tidak ada batas waktu.</small>
                            </div>
                        </div>
                    </div>
                    <!-- Kategori -->
                    <div class="form-section">
                        <div class="section-label"><span class="sl-icon blue"><i class="fas fa-tags"></i></span>Kategori Berlaku</div>
                        <small style="display:block;font-size:12px;color:var(--muted2);margin-bottom:12px;">Biarkan semua tidak dicentang jika berlaku untuk semua kategori.</small>
                        <div class="kat-grid">
                            <?php if (!empty($semua_kategori)): foreach ($semua_kategori as $kat): $checked = in_array($kat['id'], $kategori_terpilih) ? 'checked' : ''; ?>
                            <label class="kat-item">
                                <input type="checkbox" name="kategori_ids[]" value="<?= $kat['id'] ?>" <?= $checked ?>>
                                <span><?= htmlspecialchars($kat['nama_kategori']) ?></span>
                            </label>
                            <?php endforeach; else: ?>
                            <p style="color:var(--muted);font-size:13px;">Belum ada kategori produk.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-footer">
                        <a href="voucher.php" class="btn-cancel"><i class="fas fa-times"></i> Batal</a>
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> <?= $is_edit ? 'Simpan Perubahan' : 'Buat Voucher' ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const sidebar=document.getElementById('sidebar'),overlay=document.getElementById('overlay');
    document.getElementById('menuToggle').addEventListener('click',()=>sidebar.classList.contains('open')?closeMenu():openMenu());
    overlay.addEventListener('click',closeMenu);
    function openMenu(){sidebar.classList.add('open');overlay.classList.add('visible')}
    function closeMenu(){sidebar.classList.remove('open');overlay.classList.remove('visible')}

    function updateNilaiPreview() {
        const tipe = document.getElementById('tipe_diskon').value;
        const val  = parseFloat(document.getElementById('nilai_diskon').value) || 0;
        const el   = document.getElementById('nilai-preview');
        if (val <= 0) { el.textContent = ''; return; }
        el.textContent = tipe === 'persen'
            ? `Diskon ${val}% dari harga produk`
            : `Potongan Rp ${val.toLocaleString('id-ID')}`;
    }
    updateNilaiPreview();
</script>
</body>
</html>