<?php
include 'includes/koneksi.php';

$pesan_sukses = '';
$pesan_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_pengirim   = mysqli_real_escape_string($koneksi, $_POST['nama_pengirim']);
    $kontak_pengirim = mysqli_real_escape_string($koneksi, $_POST['kontak_pengirim']);
    $nama_produk     = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $deskripsi       = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    if (!empty($nama_produk) && !empty($nama_pengirim) && !empty($kontak_pengirim)) {
        $query = "INSERT INTO request_produk (nama_pengirim, kontak_pengirim, nama_produk, deskripsi, status) VALUES (?, ?, ?, ?, 'pending')";
        $stmt  = mysqli_prepare($koneksi, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssss", $nama_pengirim, $kontak_pengirim, $nama_produk, $deskripsi);
            if (mysqli_stmt_execute($stmt)) {
                $pesan_sukses = "Request untuk produk <strong>" . htmlspecialchars($nama_produk) . "</strong> berhasil terkirim. Terima kasih!";
            } else {
                $pesan_error = "Terjadi kesalahan saat mengirim request.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $pesan_error = "Terjadi kesalahan pada server. Silakan coba lagi.";
        }
    } else {
        $pesan_error = "Nama, Kontak, dan Nama Produk wajib diisi.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Produk - WARUNGERIK STORE</title>
    <link rel="canonical" href="https://www.warungerik.com/request_produk.php">
    <meta name="description" content="Request produk digital atau topup game yang belum tersedia di WARUNGERIK STORE.">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>

    <style>
        :root {
            --bg:            #f7f5f2;
            --surface:       #ffffff;
            --surface2:      #faf9f7;
            --border:        #e8e4de;
            --border2:       #f0ede8;
            --text:          #1a1714;
            --muted:         #8c8279;
            --muted2:        #b5afa7;
            --accent:        #c17f3e;
            --accent-hover:  #a96d31;
            --accent-bg:     rgba(193,127,62,0.08);
            --accent-border: rgba(193,127,62,0.25);
            --success:       #2d7a4f;
            --success-bg:    rgba(45,122,79,0.08);
            --success-border:rgba(45,122,79,0.2);
            --danger:        #b91c1c;
            --danger-bg:     rgba(185,28,28,0.08);
            --danger-border: rgba(185,28,28,0.2);
            --font:          'Plus Jakarta Sans', sans-serif;
            --mono:          'JetBrains Mono', monospace;
            --radius:        14px;
            --radius-lg:     20px;
            --shadow:        0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
            --shadow-lg:     0 2px 8px rgba(0,0,0,0.08), 0 12px 32px rgba(0,0,0,0.06);
            --shadow-xl:     0 4px 12px rgba(0,0,0,0.10), 0 20px 48px rgba(0,0,0,0.08);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: var(--font); background: var(--bg); color: var(--text); min-height: 100vh; }
        img { display: block; max-width: 100%; }
        a { text-decoration: none; color: inherit; }

        #particles-container { position: fixed; inset: 0; z-index: -1; pointer-events: none; }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

        /* ─── HEADER ─── */
        .site-header { background: var(--surface); border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 200; height: 64px; display: flex; align-items: center; }
        .header-container { display: flex; align-items: center; justify-content: space-between; width: 100%; }
        .header-brand { display: flex; align-items: center; gap: 10px; }
        .header-logo-mark { width: 36px; height: 36px; background: linear-gradient(135deg, var(--accent), #e09b55); border-radius: 10px; display: flex; align-items: center; justify-content: center; overflow: hidden; box-shadow: 0 4px 12px rgba(193,127,62,0.3); flex-shrink: 0; }
        .header-logo-mark img { width: 100%; height: 100%; object-fit: cover; }
        .header-brand-text { font-size: 15px; font-weight: 800; letter-spacing: -0.01em; }
        .header-nav { display: flex; align-items: center; gap: 4px; }
        .header-nav a { display: flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 9px; font-size: 13px; font-weight: 600; color: var(--muted); transition: all 0.15s; }
        .header-nav a:hover { background: var(--surface2); color: var(--text); }
        .header-nav a.active { background: var(--accent-bg); color: var(--accent); }

        .hamburger-menu { display: none; flex-direction: column; gap: 5px; padding: 8px; background: none; border: 1px solid var(--border); border-radius: 9px; cursor: pointer; width: 38px; height: 38px; align-items: center; justify-content: center; }
        .hamburger-menu span { display: block; width: 18px; height: 2px; background: var(--muted); border-radius: 2px; transition: all 0.3s; }

        .side-menu { position: fixed; top: 0; right: -280px; width: 260px; height: 100%; background: var(--surface); border-left: 1px solid var(--border); z-index: 300; padding: 80px 16px 24px; transition: right 0.3s cubic-bezier(.4,0,.2,1); box-shadow: var(--shadow-xl); }
        .side-menu.is-active { right: 0; }
        .side-menu ul { list-style: none; }
        .side-menu ul li a { display: flex; align-items: center; padding: 11px 14px; border-radius: 10px; font-size: 14px; font-weight: 600; color: var(--muted); transition: all 0.15s; margin-bottom: 2px; }
        .side-menu ul li a:hover { background: var(--surface2); color: var(--text); }
        .menu-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.25); backdrop-filter: blur(3px); z-index: 299; display: none; }
        .menu-overlay.is-active { display: block; }

        /* ─── PAGE ─── */
        .page-wrap { min-height: calc(100vh - 64px); display: flex; align-items: center; justify-content: center; padding: 40px 24px 60px; }
        .page-inner { width: 100%; max-width: 540px; }

        .breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 12.5px; color: var(--muted); margin-bottom: 20px; }
        .breadcrumb a { color: var(--muted); transition: color 0.15s; }
        .breadcrumb a:hover { color: var(--accent); }
        .breadcrumb .sep { font-size: 9px; }
        .breadcrumb .current { color: var(--text); font-weight: 600; }

        /* ─── CARD ─── */
        .main-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); overflow: hidden; animation: fadeUp 0.4s ease both; }
        .main-card::before { content: ''; display: block; height: 4px; background: linear-gradient(90deg, var(--accent), #e09b55); }

        .card-hero { padding: 28px 30px 20px; text-align: center; border-bottom: 1px solid var(--border2); }
        .hero-icon { width: 60px; height: 60px; background: var(--accent-bg); border: 1px solid var(--accent-border); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--accent); margin: 0 auto 14px; }
        .card-hero h1 { font-size: 19px; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 6px; }
        .card-hero p { font-size: 13px; color: var(--muted); line-height: 1.6; }

        .card-body { padding: 24px 30px 28px; }

        /* Alerts */
        .alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border-radius: 10px; font-size: 13px; font-weight: 600; margin-bottom: 20px; line-height: 1.6; }
        .alert-success { background: var(--success-bg); color: var(--success); border: 1px solid var(--success-border); }
        .alert-danger  { background: var(--danger-bg);  color: var(--danger);  border: 1px solid var(--danger-border); }
        .alert i { flex-shrink: 0; margin-top: 2px; }

        /* Form */
        .form-group { margin-bottom: 18px; }
        .form-label { display: flex; align-items: center; gap: 7px; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--muted); margin-bottom: 8px; }
        .form-label i { color: var(--accent); font-size: 10px; }
        .form-label .req { color: var(--danger); margin-left: 2px; }

        .form-input, .form-textarea {
            width: 100%; padding: 11px 14px;
            border: 1px solid var(--border); border-radius: 10px;
            background: var(--surface); font-family: var(--font);
            font-size: 13.5px; color: var(--text); outline: none; transition: all 0.15s;
        }
        .form-input::placeholder, .form-textarea::placeholder { color: var(--muted2); }
        .form-input:focus, .form-textarea:focus { border-color: var(--accent-border); box-shadow: 0 0 0 3px var(--accent-bg); }
        .form-textarea { resize: vertical; min-height: 110px; line-height: 1.65; }

        .form-hint { font-size: 11.5px; color: var(--muted2); margin-top: 5px; display: flex; align-items: center; gap: 4px; }

        .btn-submit { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 13px; background: var(--accent); color: white; border: none; border-radius: 10px; font-family: var(--font); font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.15s; box-shadow: 0 2px 8px rgba(193,127,62,0.3); margin-top: 4px; }
        .btn-submit:hover { background: var(--accent-hover); box-shadow: 0 4px 14px rgba(193,127,62,0.4); transform: translateY(-1px); }

        /* Success state */
        .success-state { text-align: center; padding: 12px 0; }
        .success-state .big-icon { width: 64px; height: 64px; background: var(--success-bg); color: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 26px; margin: 0 auto 16px; }
        .success-state h3 { font-size: 17px; font-weight: 800; margin-bottom: 8px; }
        .success-state p { font-size: 13px; color: var(--muted); margin-bottom: 20px; }
        .btn-back { display: inline-flex; align-items: center; gap: 7px; padding: 10px 20px; background: var(--surface2); border: 1px solid var(--border); border-radius: 10px; font-size: 13px; font-weight: 700; color: var(--muted); transition: all 0.15s; }
        .btn-back:hover { background: var(--surface); color: var(--text); }

        /* ─── FOOTER ─── */
        .site-footer { background: var(--text); color: rgba(255,255,255,0.9); margin-top: 0; }
        .footer-bottom { padding: 20px 0; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; max-width: 1200px; margin: 0 auto; padding: 20px 24px; }
        .footer-bottom p { font-size: 12px; color: rgba(255,255,255,0.35); }
        .footer-links-row { display: flex; gap: 16px; }
        .footer-links-row a { font-size: 12px; color: rgba(255,255,255,0.35); transition: color 0.15s; }
        .footer-links-row a:hover { color: rgba(255,255,255,0.7); }

        /* Help */
        .help-button { position: fixed; bottom: 28px; right: 28px; width: 50px; height: 50px; background: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; box-shadow: 0 6px 20px rgba(193,127,62,0.4); cursor: pointer; z-index: 999; transition: all 0.15s; }
        .help-button:hover { transform: scale(1.08); }
        .help-options { position: fixed; bottom: 90px; right: 28px; background: var(--surface); border: 1px solid var(--border); padding: 18px; border-radius: var(--radius); box-shadow: var(--shadow-xl); z-index: 998; min-width: 240px; transform: translateY(10px); opacity: 0; visibility: hidden; transition: all 0.2s; }
        .help-options.show { transform: translateY(0); opacity: 1; visibility: visible; }
        .help-options h4 { font-size: 13px; font-weight: 800; margin-bottom: 4px; }
        .help-options p { font-size: 12px; color: var(--muted); margin-bottom: 12px; }
        .help-options-btns { display: flex; flex-direction: column; gap: 7px; }
        .help-options-btns a { display: flex; align-items: center; gap: 8px; padding: 9px 13px; border-radius: 9px; font-size: 13px; font-weight: 700; color: white; transition: opacity 0.15s; }
        .help-options-btns a:hover { opacity: 0.9; }
        .help-options-btns a.tg { background: #0088cc; }
        .help-options-btns a.wa { background: #25D366; }
        .help-close { position: absolute; top: 10px; right: 12px; font-size: 18px; color: var(--muted2); cursor: pointer; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 768px) {
            .header-nav { display: none; }
            .hamburger-menu { display: flex; }
            .page-wrap { padding: 24px 14px 48px; align-items: flex-start; }
            .card-hero { padding: 22px 20px 16px; }
            .card-body { padding: 20px; }
            .footer-bottom { flex-direction: column; text-align: center; }
            .footer-links-row { justify-content: center; flex-wrap: wrap; }
            .help-options { right: 14px; left: 14px; bottom: 80px; min-width: auto; }
        }
    </style>
</head>
<body>

<div id="particles-container"></div>

<header class="site-header">
    <div class="container header-container">
        <a href="index.php" class="header-brand">
            <div class="header-logo-mark"><img src="assets/images/logo.jpg" alt="Logo"></div>
            <span class="header-brand-text">WARUNGERIK STORE</span>
        </a>
        <nav class="header-nav">
            <a href="index.php"><i class="fas fa-home"></i> Beranda</a>
            <a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a>
            <a href="testimoni.php"><i class="fas fa-comment-dots"></i> Testimoni</a>
            <a href="cek_pesanan.php"><i class="fas fa-search"></i> Cek Pesanan</a>
            <a href="request_produk.php" class="active"><i class="fas fa-inbox"></i> Request</a>
        </nav>
        <button class="hamburger-menu" id="hamburger-toggle" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<nav class="side-menu" id="side-menu">
    <ul>
        <li><a href="index.php"><i class="fas fa-home" style="width:18px;margin-right:8px;color:var(--accent)"></i>Beranda</a></li>
        <li><a href="tentang.php"><i class="fas fa-info-circle" style="width:18px;margin-right:8px;color:var(--accent)"></i>Tentang</a></li>
        <li><a href="testimoni.php"><i class="fas fa-comment-dots" style="width:18px;margin-right:8px;color:var(--accent)"></i>Testimoni</a></li>
        <li><a href="cek_pesanan.php"><i class="fas fa-search" style="width:18px;margin-right:8px;color:var(--accent)"></i>Cek Pesanan</a></li>
        <li><a href="request_produk.php"><i class="fas fa-inbox" style="width:18px;margin-right:8px;color:var(--accent)"></i>Request Produk</a></li>
    </ul>
</nav>
<div class="menu-overlay" id="menu-overlay"></div>

<main class="page-wrap">
    <div class="page-inner">

        <div class="breadcrumb">
            <a href="index.php"><i class="fas fa-home"></i></a>
            <span class="sep"><i class="fas fa-chevron-right"></i></span>
            <span class="current">Request Produk</span>
        </div>

        <div class="main-card">
            <div class="card-hero">
                <div class="hero-icon"><i class="fas fa-inbox"></i></div>
                <h1>Request Produk</h1>
                <p>Tidak menemukan produk yang dicari? Beritahu kami dan kami akan berusaha menambahkannya.</p>
            </div>

            <div class="card-body">

                <?php if (!empty($pesan_error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i>
                        <span><?= $pesan_error ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($pesan_sukses)): ?>
                    <div class="success-state">
                        <div class="big-icon"><i class="fas fa-check"></i></div>
                        <h3>Request Terkirim!</h3>
                        <p><?= $pesan_sukses ?></p>
                        <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Toko</a>
                    </div>

                <?php else: ?>
                    <form action="request_produk.php" method="POST">

                        <div class="form-group">
                            <label class="form-label" for="nama_pengirim">
                                <i class="fas fa-user"></i> Nama Anda <span class="req">*</span>
                            </label>
                            <input type="text" id="nama_pengirim" name="nama_pengirim" class="form-input" placeholder="Masukkan nama Anda" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="kontak_pengirim">
                                <i class="fas fa-phone"></i> Kontak <span class="req">*</span>
                            </label>
                            <input type="text" id="kontak_pengirim" name="kontak_pengirim" class="form-input" placeholder="Contoh: 0812xxxx (WhatsApp/Telegram)">
                            <p class="form-hint"><i class="fas fa-info-circle"></i> Kami akan menghubungi Anda jika produk tersedia.</p>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="nama_produk">
                                <i class="fas fa-box"></i> Nama Produk <span class="req">*</span>
                            </label>
                            <input type="text" id="nama_produk" name="nama_produk" class="form-input" placeholder="Contoh: Voucher Netflix 1 Bulan" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="deskripsi">
                                <i class="fas fa-align-left"></i> Deskripsi <span style="color:var(--muted2);font-weight:500;text-transform:none;letter-spacing:0;">(Opsional)</span>
                            </label>
                            <textarea id="deskripsi" name="deskripsi" class="form-textarea" placeholder="Beri detail lebih lanjut jika ada..."></textarea>
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> Kirim Request
                        </button>

                    </form>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>

<footer class="site-footer">
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> WARUNGERIK STORE — All Rights Reserved</p>
        <div class="footer-links-row">
            <a href="index.php">Beranda</a>
            <a href="tentang.php">Tentang</a>
                            <a href="legal.php">Kebijakan</a>
            <a href="cek_pesanan.php">Cek Pesanan</a>
        </div>
    </div>
</footer>

<a href="#" class="help-button" id="helpBtn"><i class="fas fa-question"></i></a>
<div class="help-options" id="helpOptions">
    <span class="help-close" id="closeHelpBtn">&times;</span>
    <h4>Butuh Bantuan?</h4>
    <p>Hubungi kami melalui:</p>
    <div class="help-options-btns">
        <a href="https://t.me/warung_erik" target="_blank" class="tg"><i class="fab fa-telegram"></i> Telegram</a>
        <a href="https://wa.me/6285183129647" target="_blank" class="wa"><i class="fab fa-whatsapp"></i> WhatsApp</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof tsParticles !== 'undefined') {
        tsParticles.load('particles-container', {
            particles: {
                number: { value: 35, density: { enable: true, value_area: 900 } },
                color: { value: "#c17f3e" }, shape: { type: "circle" },
                opacity: { value: 0.1, random: true }, size: { value: 2.5, random: true },
                links: { enable: true, distance: 130, color: "#c17f3e", opacity: 0.06, width: 1 },
                move: { enable: true, speed: 1, outModes: { default: "bounce" } }
            },
            interactivity: { events: { onHover: { enable: true, mode: "grab" }, resize: true }, modes: { grab: { distance: 120, links: { opacity: 0.15 } } } }
        });
    }

    const hamburger = document.getElementById('hamburger-toggle');
    const sideMenu  = document.getElementById('side-menu');
    const overlay   = document.getElementById('menu-overlay');
    hamburger?.addEventListener('click', () => { hamburger.classList.toggle('is-active'); sideMenu.classList.toggle('is-active'); overlay.classList.toggle('is-active'); });
    overlay?.addEventListener('click', () => { hamburger.classList.remove('is-active'); sideMenu.classList.remove('is-active'); overlay.classList.remove('is-active'); });

    const helpBtn = document.getElementById('helpBtn');
    const helpOptions = document.getElementById('helpOptions');
    const closeHelp = document.getElementById('closeHelpBtn');
    helpBtn?.addEventListener('click', e => { e.preventDefault(); helpOptions.classList.toggle('show'); });
    closeHelp?.addEventListener('click', () => helpOptions.classList.remove('show'));
    window.addEventListener('click', e => { if (!helpBtn?.contains(e.target) && !helpOptions?.contains(e.target)) helpOptions?.classList.remove('show'); });
});
</script>
</body>
</html>