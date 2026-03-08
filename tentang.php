<?php
include 'includes/koneksi.php';

$nomor_wa = "6285183129647";
$username_tele = "warung_erik";

$result_terjual = mysqli_query($koneksi, "SELECT SUM(jumlah_terjual) as total_terjual FROM produk");
$data_terjual = mysqli_fetch_assoc($result_terjual);
$total_terjual_akumulasi = $data_terjual['total_terjual'] ?? 0;

$result_produk = mysqli_query($koneksi, "SELECT COUNT(*) as total_produk FROM produk WHERE stok > 0");
$data_produk = mysqli_fetch_assoc($result_produk);
$total_produk = $data_produk['total_produk'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - WARUNGERIK STORE</title>
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
            --info:          #2563a8;
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
        .site-header {
            background: var(--surface); border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 200; height: 64px; display: flex; align-items: center;
        }
        .header-container { display: flex; align-items: center; justify-content: space-between; width: 100%; }
        .header-brand { display: flex; align-items: center; gap: 10px; }
        .header-logo-mark {
            width: 36px; height: 36px; background: linear-gradient(135deg, var(--accent), #e09b55);
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            overflow: hidden; box-shadow: 0 4px 12px rgba(193,127,62,0.3); flex-shrink: 0;
        }
        .header-logo-mark img { width: 100%; height: 100%; object-fit: cover; }
        .header-brand-text { font-size: 15px; font-weight: 800; letter-spacing: -0.01em; }
        .header-nav { display: flex; align-items: center; gap: 4px; }
        .header-nav a {
            display: flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 9px;
            font-size: 13px; font-weight: 600; color: var(--muted); transition: all 0.15s;
        }
        .header-nav a:hover { background: var(--surface2); color: var(--text); }
        .header-nav a.active { background: var(--accent-bg); color: var(--accent); }

        .hamburger-menu {
            display: none; flex-direction: column; gap: 5px; padding: 8px;
            background: none; border: 1px solid var(--border); border-radius: 9px;
            cursor: pointer; width: 38px; height: 38px; align-items: center; justify-content: center;
        }
        .hamburger-menu span { display: block; width: 18px; height: 2px; background: var(--muted); border-radius: 2px; transition: all 0.3s; }

        .side-menu {
            position: fixed; top: 0; right: -280px; width: 260px; height: 100%;
            background: var(--surface); border-left: 1px solid var(--border);
            z-index: 300; padding: 80px 16px 24px;
            transition: right 0.3s cubic-bezier(.4,0,.2,1); box-shadow: var(--shadow-xl);
        }
        .side-menu.is-active { right: 0; }
        .side-menu ul { list-style: none; }
        .side-menu ul li a {
            display: flex; align-items: center; padding: 11px 14px; border-radius: 10px;
            font-size: 14px; font-weight: 600; color: var(--muted); transition: all 0.15s; margin-bottom: 2px;
        }
        .side-menu ul li a:hover { background: var(--surface2); color: var(--text); }
        .menu-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.25); backdrop-filter: blur(3px); z-index: 299; display: none; }
        .menu-overlay.is-active { display: block; }

        /* ─── PAGE ─── */
        .page-wrap { padding: 48px 0 64px; }

        .about-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 24px;
            align-items: start;
        }

        /* ─── LEFT: Profile Card ─── */
        .profile-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius-lg); box-shadow: var(--shadow);
            overflow: hidden;
            animation: fadeUp 0.4s ease both;
        }

        .profile-card-top {
            padding: 28px 24px 20px;
            text-align: center;
            border-bottom: 1px solid var(--border2);
        }

        .profile-avatar-wrap {
            position: relative;
            display: inline-block;
            margin-bottom: 16px;
        }

        .profile-avatar {
            width: 100px; height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--surface);
            box-shadow: 0 4px 16px rgba(193,127,62,0.2), 0 0 0 2px var(--accent-border);
            animation: floatAvatar 4s ease-in-out infinite;
        }

        .profile-badge {
            position: absolute; bottom: 4px; right: 4px;
            width: 22px; height: 22px;
            background: var(--success); border: 2px solid var(--surface);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 9px; color: white;
        }

        .profile-name { font-size: 16px; font-weight: 800; margin-bottom: 4px; }

        .profile-role {
            display: inline-block; background: var(--accent-bg);
            color: var(--accent); border: 1px solid var(--accent-border);
            font-size: 10px; font-weight: 700; letter-spacing: 0.08em;
            text-transform: uppercase; padding: 3px 10px; border-radius: 99px;
        }

        .profile-card-contacts { padding: 16px 20px; display: flex; flex-direction: column; gap: 8px; }

        .contact-btn {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; border-radius: 9px;
            font-size: 13px; font-weight: 700; color: white; transition: opacity 0.15s;
        }
        .contact-btn:hover { opacity: 0.88; }
        .contact-btn.tg { background: #0088cc; }
        .contact-btn.wa { background: #25D366; }
        .contact-btn i { font-size: 15px; width: 18px; text-align: center; }

        /* ─── RIGHT: Content ─── */
        .about-content { display: flex; flex-direction: column; gap: 20px; animation: fadeUp 0.4s 0.08s ease both; }

        /* About card */
        .content-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); box-shadow: var(--shadow);
            overflow: hidden;
        }

        .content-card-head {
            padding: 16px 22px; border-bottom: 1px solid var(--border2);
            display: flex; align-items: center; gap: 10px;
        }

        .content-card-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; flex-shrink: 0;
            background: var(--accent-bg); color: var(--accent);
        }

        .content-card-head h2 { font-size: 14px; font-weight: 800; }

        .content-card-body { padding: 20px 22px; }

        .bio-text {
            font-size: 14px; color: var(--muted); line-height: 1.8;
        }

        /* Stats */
        .stats-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;
        }

        .stat-card {
            background: var(--surface2); border: 1px solid var(--border2);
            border-radius: var(--radius); padding: 18px 16px; text-align: center;
            transition: all 0.2s;
        }

        .stat-card:hover { border-color: var(--accent-border); box-shadow: var(--shadow); transform: translateY(-2px); }

        .stat-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--accent-bg); color: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; margin: 0 auto 10px;
        }

        .stat-number {
            font-family: var(--mono); font-size: 20px; font-weight: 700;
            color: var(--text); margin-bottom: 4px;
        }

        .stat-label { font-size: 11px; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; color: var(--muted2); }

        /* Features */
        .features-grid {
            display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;
        }

        .feature-item {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 14px; background: var(--surface2);
            border: 1px solid var(--border2); border-radius: 10px;
            transition: all 0.15s;
        }

        .feature-item:hover { border-color: var(--accent-border); background: var(--accent-bg); }

        .feature-icon {
            width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0;
            background: var(--accent-bg); color: var(--accent);
            display: flex; align-items: center; justify-content: center; font-size: 14px;
        }

        .feature-title { font-size: 13px; font-weight: 700; margin-bottom: 3px; }
        .feature-desc { font-size: 12px; color: var(--muted); line-height: 1.5; }

        /* ─── FOOTER ─── */
        .site-footer { background: var(--text); color: rgba(255,255,255,0.9); margin-top: 0; }
        .footer-top { padding: 40px 0 32px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 40px; }
        .footer-brand-img { width: 40px; height: 40px; border-radius: 10px; object-fit: cover; margin-bottom: 12px; }
        .footer-brand-name { font-size: 15px; font-weight: 800; color: white; margin-bottom: 6px; }
        .footer-brand-desc { font-size: 12.5px; color: rgba(255,255,255,0.5); line-height: 1.7; margin-bottom: 18px; }
        .footer-socials { display: flex; gap: 8px; flex-wrap: wrap; }
        .social-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 9px; font-size: 12px; font-weight: 700; color: white; transition: all 0.15s; }
        .social-btn.tg { background: rgba(0,136,204,0.25); }
        .social-btn.tg:hover { background: #0088cc; }
        .social-btn.wa { background: rgba(37,211,102,0.2); }
        .social-btn.wa:hover { background: #25D366; }
        .footer-col-title { font-size: 10px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.35); margin-bottom: 14px; }
        .footer-links { display: flex; flex-direction: column; gap: 5px; }
        .footer-links a { font-size: 13px; color: rgba(255,255,255,0.55); font-weight: 500; transition: color 0.15s; padding: 3px 0; }
        .footer-links a:hover { color: white; }
        .footer-img-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 7px; margin-bottom: 16px; }
        .footer-img-grid img { width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 7px; }
        .footer-bottom { padding: 18px 0; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .footer-bottom p { font-size: 12px; color: rgba(255,255,255,0.35); }
        .footer-bottom-links { display: flex; gap: 16px; }
        .footer-bottom-links a { font-size: 12px; color: rgba(255,255,255,0.35); transition: color 0.15s; }
        .footer-bottom-links a:hover { color: rgba(255,255,255,0.7); }

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

        /* Animations */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes floatAvatar { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }

        /* Responsive */
        @media (max-width: 900px) {
            .about-grid { grid-template-columns: 1fr; }
            .profile-card { max-width: 400px; margin: 0 auto; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .header-nav { display: none; }
            .hamburger-menu { display: flex; }
        }
        @media (max-width: 600px) {
            .stats-grid { grid-template-columns: 1fr; }
            .features-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; gap: 24px; }
            .footer-bottom { flex-direction: column; text-align: center; }
            .footer-bottom-links { justify-content: center; flex-wrap: wrap; }
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
            <a href="tentang.php" class="active"><i class="fas fa-info-circle"></i> Tentang</a>
            <a href="testimoni.php"><i class="fas fa-comment-dots"></i> Testimoni</a>
            <a href="cek_pesanan.php"><i class="fas fa-search"></i> Cek Pesanan</a>
            <a href="request_produk.php"><i class="fas fa-inbox"></i> Request</a>
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
    <div class="container">
        <div class="about-grid">

            <!-- LEFT: Profile -->
            <div class="profile-card">
                <div class="profile-card-top">
                    <div class="profile-avatar-wrap">
                        <img src="assets/images/logo.jpg" alt="Erik" class="profile-avatar">
                        <div class="profile-badge"><i class="fas fa-check"></i></div>
                    </div>
                    <div class="profile-name">Erik</div>
                    <span class="profile-role">Founder & Owner</span>
                </div>
                <div class="profile-card-contacts">
                    <a href="https://t.me/<?= $username_tele ?>" target="_blank" class="contact-btn tg">
                        <i class="fab fa-telegram"></i> Telegram
                    </a>
                    <a href="https://wa.me/<?= $nomor_wa ?>" target="_blank" class="contact-btn wa">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>

            <!-- RIGHT: Content -->
            <div class="about-content">

                <!-- Bio -->
                <div class="content-card">
                    <div class="content-card-head">
                        <div class="content-card-icon"><i class="fas fa-user"></i></div>
                        <h2>Tentang WARUNGERIK STORE</h2>
                    </div>
                    <div class="content-card-body">
                        <p class="bio-text">Halo! 👋 Saya Erik, founder dari WARUNGERIK STORE. Kami adalah platform digital store terpercaya yang menyediakan layanan top-up game dengan harga termurah dan proses super instan. Kepuasan pelanggan adalah prioritas utama kami!</p>
                    </div>
                </div>

                <!-- Stats -->
                <div class="content-card">
                    <div class="content-card-head">
                        <div class="content-card-icon"><i class="fas fa-chart-bar"></i></div>
                        <h2>Statistik</h2>
                    </div>
                    <div class="content-card-body">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                                <div class="stat-number"><?= number_format($total_terjual_akumulasi) ?>+</div>
                                <div class="stat-label">Item Terjual</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon"><i class="fas fa-gamepad"></i></div>
                                <div class="stat-number"><?= number_format($total_produk) ?>+</div>
                                <div class="stat-label">Produk</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon"><i class="fas fa-bolt"></i></div>
                                <div class="stat-number">24/7</div>
                                <div class="stat-label">Support</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features -->
                <div class="content-card">
                    <div class="content-card-head">
                        <div class="content-card-icon"><i class="fas fa-star"></i></div>
                        <h2>Keunggulan Kami</h2>
                    </div>
                    <div class="content-card-body">
                        <div class="features-grid">
                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-rocket"></i></div>
                                <div>
                                    <div class="feature-title">Proses Instan</div>
                                    <div class="feature-desc">Transaksi diproses otomatis dalam hitungan detik</div>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                                <div>
                                    <div class="feature-title">Aman & Terpercaya</div>
                                    <div class="feature-desc">Keamanan data dan transaksi terjamin 100%</div>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-tag"></i></div>
                                <div>
                                    <div class="feature-title">Harga Terbaik</div>
                                    <div class="feature-desc">Harga termurah dengan kualitas terbaik</div>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-headset"></i></div>
                                <div>
                                    <div class="feature-title">CS Responsif</div>
                                    <div class="feature-desc">Customer service siap membantu kapan saja</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-grid">
                <div>
                    <img src="assets/images/logo.jpg" alt="Logo" class="footer-brand-img">
                    <div class="footer-brand-name">WARUNGERIK STORE</div>
                    <p class="footer-brand-desc">Panel penyedia layanan topup games terbaik #1 Indonesia, harga termurah dan proses super instan.</p>
                    <div class="footer-socials">
                        <a href="https://t.me/warung_erik" target="_blank" class="social-btn tg"><i class="fab fa-telegram"></i> Telegram</a>
                        <a href="https://wa.me/6285183129647" target="_blank" class="social-btn wa"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                    </div>
                </div>
                <div>
                    <div class="footer-col-title">Navigasi</div>
                    <div class="footer-links">
                        <a href="index.php">Beranda</a>
                        <a href="tentang.php">Tentang</a>
                        <a href="testimoni.php">Testimoni</a>
                        <a href="cek_pesanan.php">Cek Pesanan</a>
                        <a href="request_produk.php">Request Produk</a>
                    </div>
                </div>
                <div>
                    <div class="footer-col-title">Games Populer</div>
                    <div class="footer-img-grid" style="margin-bottom:20px;">
                        <img src="https://warungerik.com/tools/img/3af48847.png" alt="ML">
                        <img src="https://upload.wikimedia.org/wikipedia/fi/9/9b/Call_of_Duty_Mobile_logo.jpg" alt="COD">
                        <img src="https://warungerik.com/tools/img/ebd92394.png" alt="Genshin">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2f/Icon_PUBGM.jpg" alt="PUBG">
                    </div>
                    <div class="footer-col-title">Metode Bayar</div>
                    <div class="footer-img-grid">
                        <img src="https://warungerik.com/tools/img/11748921.png" alt="QRIS">
                        <img src="https://warungerik.com/tools/img/90faf3de.png" alt="SeaBank">
                        <img src="https://warungerik.com/tools/img/1524c130.png" alt="BRI">
                        <img src="https://warungerik.com/tools/img/7e9e87e3.png" alt="BCA">
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> WARUNGERIK STORE — All Rights Reserved</p>
            <div class="footer-bottom-links">
                <a href="index.php">Beranda</a>
                <a href="tentang.php">Tentang</a>
                                <a href="legal.php">Kebijakan</a>
                <a href="cek_pesanan.php">Cek Pesanan</a>
            </div>
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