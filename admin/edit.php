<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include '../includes/koneksi.php';

$kategori_list = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

$id = $_GET['id'] ?? null;
$produk = [
    'nama_produk'  => '',
    'harga'        => '',
    'deskripsi'    => '',
    'gambar'       => '',
    'kategori_id'  => null,
    'cek_stok'     => 0,
    'stok'         => null,
    'diskon_persen'=> 0
];
$page_title = "Tambah Produk Baru";
$is_edit    = false;

if ($id) {
    $page_title = "Edit Produk";
    $is_edit    = true;
    $stmt = $koneksi->prepare("SELECT * FROM produk WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) $produk = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> — AdminStore</title>
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
        .back-btn{display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:var(--surface2);border:1px solid var(--border);border-radius:9px;text-decoration:none;color:var(--muted);font-size:13px;font-weight:600;transition:all .15s}
        .back-btn:hover{background:var(--bg);color:var(--text);border-color:var(--accent-border)}

        /* ── Content ── */
        .content{padding:28px;flex:1}
        .content-inner{max-width:760px;margin:0 auto}
        .page-title{font-size:clamp(20px,3vw,26px);font-weight:800;letter-spacing:-.02em;margin-bottom:4px}
        .page-title span{color:var(--accent)}
        .page-subtitle{font-size:13px;color:var(--muted);margin-bottom:28px;margin-top:4px}

        /* ── Form card ── */
        .form-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;animation:fadeUp .4s ease both}

        .form-section{padding:24px 28px;border-bottom:1px solid var(--border2)}
        .form-section:last-child{border-bottom:none}

        .section-label{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-bottom:18px}
        .section-label .sl-icon{width:24px;height:24px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:11px}
        .sl-icon.amber{background:var(--accent-bg);color:var(--accent)}
        .sl-icon.green{background:var(--success-bg);color:var(--success)}
        .sl-icon.blue{background:rgba(37,99,168,.08);color:#2563a8}

        .field-group{display:grid;gap:18px}
        .field-group.cols-2{grid-template-columns:1fr 1fr}
        .field-group.cols-3{grid-template-columns:1fr 1fr 1fr}

        .field{display:flex;flex-direction:column;gap:6px}

        .field label{font-size:12px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:var(--muted)}

        .field input[type="text"],
        .field input[type="number"],
        .field select,
        .field textarea{
            padding:11px 14px;background:var(--bg);border:1px solid var(--border);
            border-radius:10px;font-family:var(--font);font-size:13.5px;color:var(--text);
            outline:none;transition:border-color .15s,box-shadow .15s,background .15s;width:100%
        }
        .field input:focus,.field select:focus,.field textarea:focus{
            border-color:var(--accent-border);box-shadow:0 0 0 3px var(--accent-bg);background:var(--surface)
        }
        .field textarea{resize:vertical;min-height:110px;line-height:1.6}
        .field small{font-size:11.5px;color:var(--muted2);line-height:1.4}

        /* Harga preview */
        .price-preview{font-family:var(--mono);font-size:12px;color:var(--success);font-weight:600;margin-top:2px;min-height:16px}

        /* Stock toggle */
        .toggle-row{display:flex;align-items:center;gap:14px;background:var(--surface2);border:1px solid var(--border);border-radius:12px;padding:14px 18px}
        .toggle-wrap{position:relative;flex-shrink:0}
        .toggle-wrap input{position:absolute;opacity:0;width:0;height:0}
        .toggle-track{display:block;width:40px;height:22px;background:var(--border);border-radius:99px;cursor:pointer;transition:background .2s}
        .toggle-wrap input:checked + .toggle-track{background:var(--accent)}
        .toggle-track::after{content:'';position:absolute;top:3px;left:3px;width:16px;height:16px;background:#fff;border-radius:50%;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.15)}
        .toggle-wrap input:checked + .toggle-track::after{transform:translateX(18px)}
        .toggle-info{flex:1}
        .toggle-info strong{display:block;font-size:13.5px;font-weight:700}
        .toggle-info span{font-size:12px;color:var(--muted)}

        /* Image upload */
        .img-upload-area{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
        .img-preview-box{width:80px;height:80px;border-radius:12px;border:1px solid var(--border);background:var(--bg);overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center;color:var(--muted2);font-size:22px}
        .img-preview-box img{width:100%;height:100%;object-fit:cover}
        .img-upload-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:var(--surface2);border:1px dashed var(--border);border-radius:10px;cursor:pointer;font-size:13px;font-weight:600;color:var(--muted);transition:all .15s}
        .img-upload-btn:hover{border-color:var(--accent-border);color:var(--accent);background:var(--accent-bg)}
        .img-upload-btn input{display:none}

        /* Diskon badge */
        .diskon-preview{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:99px;background:var(--danger-bg);color:var(--danger);font-size:11px;font-weight:700;margin-top:3px;font-family:var(--mono);min-height:20px}

        /* Submit */
        .form-footer{padding:20px 28px;background:var(--surface2);border-top:1px solid var(--border2);display:flex;align-items:center;justify-content:flex-end;gap:12px}

        .btn-cancel{display:inline-flex;align-items:center;gap:7px;padding:11px 20px;background:transparent;border:1px solid var(--border);border-radius:10px;text-decoration:none;color:var(--muted);font-size:13.5px;font-weight:700;transition:all .15s;font-family:var(--font);cursor:pointer}
        .btn-cancel:hover{background:var(--bg);color:var(--text)}

        .btn-save{display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:var(--accent);color:#fff;border:none;border-radius:10px;font-family:var(--font);font-size:13.5px;font-weight:700;cursor:pointer;transition:all .15s;box-shadow:0 2px 8px rgba(193,127,62,.25)}
        .btn-save:hover{background:#a96d31;box-shadow:0 4px 14px rgba(193,127,62,.35);transform:translateY(-1px)}

        @keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}

        /* Responsive */
        @media(max-width:1024px){:root{--sidebar-w:210px}}
        @media(max-width:768px){
            body{display:block}.sidebar{transform:translateX(calc(-1 * var(--sidebar-w)))}
            .sidebar.open{transform:translateX(0);box-shadow:var(--shadow-lg)}
            .sidebar-overlay.visible{display:block}.main{margin-left:0}
            .menu-toggle{display:inline-flex}.content{padding:16px}.topbar{padding:0 16px}
            .field-group.cols-2,.field-group.cols-3{grid-template-columns:1fr}
            .form-section{padding:18px 18px}
            .form-footer{padding:16px 18px;flex-direction:column}
            .btn-save,.btn-cancel{width:100%;justify-content:center}
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
        <a href="index.php" class="nav-link active"><span class="nav-icon"><i class="fas fa-box-open"></i></span>Produk</a>
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
                <span class="crumb-current"><?= $page_title ?></span>
            </div>
        </div>
        <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </header>

    <div class="content">
        <div class="content-inner">
            <h1 class="page-title"><?= $is_edit ? 'Edit <span>Produk</span>' : 'Tambah <span>Produk Baru</span>' ?></h1>
            <p class="page-subtitle"><?= $is_edit ? 'Perbarui informasi produk di bawah ini.' : 'Isi detail produk yang akan ditambahkan ke katalog.' ?></p>

            <form action="proses.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($produk['gambar']) ?>">

                <div class="form-card">

                    <!-- Informasi Dasar -->
                    <div class="form-section">
                        <div class="section-label">
                            <span class="sl-icon amber"><i class="fas fa-info"></i></span>
                            Informasi Dasar
                        </div>
                        <div class="field-group">
                            <div class="field">
                                <label for="kategori_id">Kategori</label>
                                <select name="kategori_id" id="kategori_id" required>
                                    <option value="">— Pilih Kategori —</option>
                                    <?php mysqli_data_seek($kategori_list, 0); while($kat = mysqli_fetch_assoc($kategori_list)): ?>
                                    <option value="<?= $kat['id'] ?>" <?= (isset($produk['kategori_id']) && $kat['id'] == $produk['kategori_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($kat['nama_kategori']) ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="field">
                                <label for="nama_produk">Nama Produk</label>
                                <input type="text" id="nama_produk" name="nama_produk"
                                       value="<?= htmlspecialchars($produk['nama_produk']) ?>"
                                       placeholder="Contoh: Windows 11 Pro Key" required>
                            </div>
                            <div class="field">
                                <label for="deskripsi">Deskripsi</label>
                                <textarea id="deskripsi" name="deskripsi"
                                          placeholder="Tulis deskripsi produk..."><?= htmlspecialchars($produk['deskripsi']) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Harga & Diskon -->
                    <div class="form-section">
                        <div class="section-label">
                            <span class="sl-icon green"><i class="fas fa-tag"></i></span>
                            Harga & Diskon
                        </div>
                        <div class="field-group cols-2">
                            <div class="field">
                                <label for="harga">Harga (Rp)</label>
                                <input type="number" id="harga" name="harga"
                                       value="<?= htmlspecialchars($produk['harga']) ?>"
                                       placeholder="50000" min="0" required
                                       oninput="updatePricePreview()">
                                <div class="price-preview" id="price-preview">
                                    <?php if (!empty($produk['harga'])): ?>
                                    Rp <?= number_format($produk['harga'], 0, ',', '.') ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="field">
                                <label for="diskon_persen">Diskon (%)</label>
                                <input type="number" id="diskon_persen" name="diskon_persen"
                                       value="<?= htmlspecialchars($produk['diskon_persen']) ?>"
                                       min="0" max="100" placeholder="0"
                                       oninput="updateDiskonPreview()">
                                <div class="diskon-preview" id="diskon-preview">
                                    <?php
                                    $d = (int)$produk['diskon_persen'];
                                    $h = (float)$produk['harga'];
                                    if ($d > 0 && $h > 0) {
                                        $final = $h - ($h * $d / 100);
                                        echo "Harga akhir: Rp " . number_format($final, 0, ',', '.');
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Manajemen Stok -->
                    <div class="form-section">
                        <div class="section-label">
                            <span class="sl-icon blue"><i class="fas fa-boxes"></i></span>
                            Manajemen Stok
                        </div>
                        <div class="toggle-row">
                            <label class="toggle-wrap">
                                <input type="checkbox" name="cek_stok" id="cek_stok" value="1"
                                       <?= (!empty($produk['cek_stok']) && $produk['cek_stok'] == 1) ? 'checked' : '' ?>>
                                <span class="toggle-track"></span>
                            </label>
                            <div class="toggle-info">
                                <strong>Stok Berbasis Kunci (License Key)</strong>
                                <span>Stok dihitung otomatis dari jumlah kunci yang tersedia. Kelola kunci setelah produk disimpan.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Gambar -->
                    <div class="form-section">
                        <div class="section-label">
                            <span class="sl-icon amber"><i class="fas fa-image"></i></span>
                            Gambar Produk
                        </div>
                        <div class="img-upload-area">
                            <div class="img-preview-box" id="img-preview-box">
                                <?php if (!empty($produk['gambar'])): ?>
                                    <img src="../assets/images/<?= htmlspecialchars($produk['gambar']) ?>" id="img-preview" alt="Preview">
                                <?php else: ?>
                                    <i class="fas fa-image" id="img-placeholder"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <label class="img-upload-btn">
                                    <i class="fas fa-upload"></i>
                                    <?= $is_edit ? 'Ganti Gambar' : 'Pilih Gambar' ?>
                                    <input type="file" name="gambar" id="gambar-input" accept="image/*">
                                </label>
                                <div style="font-size:11.5px;color:var(--muted2);margin-top:6px;">
                                    <?= $is_edit ? 'Kosongkan jika tidak ingin mengubah gambar.' : 'Format: JPG, PNG, WEBP.' ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="form-footer">
                        <a href="index.php" class="btn-cancel"><i class="fas fa-times"></i> Batal</a>
                        <button type="submit" name="simpan" class="btn-save">
                            <i class="fas fa-save"></i> <?= $is_edit ? 'Simpan Perubahan' : 'Tambah Produk' ?>
                        </button>
                    </div>

                </div><!-- end form-card -->
            </form>
        </div>
    </div>
</div>

<script>
    // Sidebar
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    document.getElementById('menuToggle').addEventListener('click', () =>
        sidebar.classList.contains('open') ? closeMenu() : openMenu());
    overlay.addEventListener('click', closeMenu);
    function openMenu()  { sidebar.classList.add('open'); overlay.classList.add('visible'); }
    function closeMenu() { sidebar.classList.remove('open'); overlay.classList.remove('visible'); }

    // Price preview
    function updatePricePreview() {
        const v = parseInt(document.getElementById('harga').value) || 0;
        document.getElementById('price-preview').textContent = v > 0
            ? 'Rp ' + v.toLocaleString('id-ID') : '';
        updateDiskonPreview();
    }

    function updateDiskonPreview() {
        const h = parseInt(document.getElementById('harga').value) || 0;
        const d = parseInt(document.getElementById('diskon_persen').value) || 0;
        const el = document.getElementById('diskon-preview');
        if (d > 0 && h > 0) {
            const final = h - (h * d / 100);
            el.textContent = 'Harga akhir: Rp ' + final.toLocaleString('id-ID');
        } else {
            el.textContent = '';
        }
    }

    // Image preview
    document.getElementById('gambar-input').addEventListener('change', function() {
        if (!this.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            const box = document.getElementById('img-preview-box');
            box.innerHTML = '<img src="' + e.target.result + '" id="img-preview" alt="Preview" style="width:100%;height:100%;object-fit:cover;">';
        };
        reader.readAsDataURL(this.files[0]);
    });
</script>
</body>
</html>