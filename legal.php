<?php
include 'includes/koneksi.php';

$nomor_wa = "6285183129647";
$username_tele = "warung_erik";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan & Ketentuan - WARUNGERIK STORE</title>
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

        /* ─── PAGE HERO ─── */
        .page-hero {
            text-align: center;
            margin-bottom: 40px;
            animation: fadeUp 0.4s ease both;
        }
        .page-hero-badge {
            display: inline-flex; align-items: center; gap: 7px;
            background: var(--accent-bg); color: var(--accent);
            border: 1px solid var(--accent-border);
            font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
            padding: 5px 14px; border-radius: 99px; margin-bottom: 14px;
        }
        .page-hero h1 { font-size: 30px; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 8px; }
        .page-hero p { font-size: 14px; color: var(--muted); }
        .page-hero-updated {
            display: inline-flex; align-items: center; gap: 6px;
            margin-top: 12px; background: var(--surface); border: 1px solid var(--border);
            border-radius: 99px; padding: 5px 14px; font-size: 11px; color: var(--muted2); font-family: var(--mono);
        }

        /* ─── LAYOUT ─── */
        .legal-layout {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 28px;
            align-items: start;
        }

        /* ─── SIDEBAR TOC ─── */
        .toc-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius-lg); box-shadow: var(--shadow);
            overflow: hidden;
            position: sticky;
            top: 84px;
            animation: fadeUp 0.4s 0.05s ease both;
        }
        .toc-header {
            padding: 14px 18px; border-bottom: 1px solid var(--border2);
            font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted2);
        }
        .toc-body { padding: 10px; }
        .toc-section { margin-bottom: 4px; }
        .toc-section-title {
            display: flex; align-items: center; gap: 8px;
            padding: 9px 10px; border-radius: 9px;
            font-size: 12px; font-weight: 700; color: var(--text);
            cursor: pointer; transition: all 0.15s;
        }
        .toc-section-title:hover, .toc-section-title.active { background: var(--accent-bg); color: var(--accent); }
        .toc-section-title i { font-size: 11px; width: 14px; text-align: center; color: var(--accent); }
        .toc-items { padding: 0 0 4px 32px; }
        .toc-items a {
            display: block; font-size: 11.5px; color: var(--muted); padding: 5px 0;
            border-left: 2px solid var(--border2); padding-left: 10px; transition: all 0.15s;
            font-weight: 500;
        }
        .toc-items a:hover { color: var(--accent); border-left-color: var(--accent); }

        /* ─── CONTENT ─── */
        .legal-content { display: flex; flex-direction: column; gap: 24px; animation: fadeUp 0.4s 0.1s ease both; }

        .legal-section-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); box-shadow: var(--shadow);
            overflow: hidden;
            scroll-margin-top: 90px;
        }

        .legal-section-head {
            padding: 18px 24px; border-bottom: 1px solid var(--border2);
            display: flex; align-items: center; gap: 12px;
        }
        .legal-section-icon {
            width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 14px;
            background: var(--accent-bg); color: var(--accent);
        }
        .legal-section-head h2 { font-size: 15px; font-weight: 800; }
        .legal-section-head .section-tag {
            margin-left: auto; font-size: 10px; font-weight: 700; letter-spacing: 0.07em;
            text-transform: uppercase; padding: 3px 9px; border-radius: 99px;
            background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-border);
        }

        .legal-section-body { padding: 24px; }

        .legal-intro {
            font-size: 13.5px; color: var(--muted); line-height: 1.8; margin-bottom: 20px;
            padding: 14px 16px; background: var(--surface2); border-radius: 10px;
            border-left: 3px solid var(--accent);
        }

        .legal-article { margin-bottom: 22px; }
        .legal-article:last-child { margin-bottom: 0; }

        .legal-article-num {
            display: flex; align-items: center; gap: 10px; margin-bottom: 10px;
        }
        .num-badge {
            width: 26px; height: 26px; border-radius: 7px;
            background: var(--accent-bg); color: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; font-family: var(--mono); flex-shrink: 0;
        }
        .legal-article-num h3 { font-size: 13.5px; font-weight: 700; }

        .legal-article p {
            font-size: 13.5px; color: var(--muted); line-height: 1.8;
            padding-left: 36px; margin-bottom: 8px;
        }

        .legal-list {
            padding-left: 36px; margin-top: 8px; display: flex; flex-direction: column; gap: 6px;
        }
        .legal-list li {
            font-size: 13px; color: var(--muted); line-height: 1.7;
            display: flex; align-items: flex-start; gap: 8px;
        }
        .legal-list li::before {
            content: ""; flex-shrink: 0; width: 5px; height: 5px;
            border-radius: 50%; background: var(--accent); margin-top: 9px;
        }

        .legal-highlight {
            background: var(--accent-bg); border: 1px solid var(--accent-border);
            border-radius: 10px; padding: 14px 16px; margin: 12px 0 0 36px;
            display: flex; align-items: flex-start; gap: 10px;
        }
        .legal-highlight i { color: var(--accent); font-size: 13px; margin-top: 2px; flex-shrink: 0; }
        .legal-highlight p { font-size: 12.5px !important; color: var(--text) !important; padding-left: 0 !important; font-weight: 600; line-height: 1.6 !important; }

        .legal-table {
            width: 100%; border-collapse: collapse; margin: 10px 0 0 0; font-size: 12.5px;
        }
        .legal-table th {
            background: var(--surface2); padding: 9px 14px; text-align: left;
            font-weight: 700; font-size: 11px; letter-spacing: 0.06em; text-transform: uppercase;
            color: var(--muted2); border: 1px solid var(--border2);
        }
        .legal-table td {
            padding: 9px 14px; border: 1px solid var(--border2); color: var(--muted); line-height: 1.6;
        }
        .legal-table tr:hover td { background: var(--surface2); }

        .contact-banner {
            background: linear-gradient(135deg, var(--accent), #e09b55);
            border-radius: var(--radius); padding: 24px; color: white; text-align: center;
        }
        .contact-banner h3 { font-size: 16px; font-weight: 800; margin-bottom: 6px; }
        .contact-banner p { font-size: 13px; opacity: 0.85; margin-bottom: 18px; }
        .contact-banner-btns { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .contact-banner-btns a {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 18px; border-radius: 9px; font-size: 13px; font-weight: 700;
            transition: all 0.15s; color: white;
        }
        .contact-banner-btns a.tg { background: rgba(0,0,0,0.2); }
        .contact-banner-btns a.wa { background: rgba(0,0,0,0.2); }
        .contact-banner-btns a:hover { background: rgba(0,0,0,0.35); }

        /* ─── FOOTER ─── */
        .site-footer { background: var(--text); color: rgba(255,255,255,0.9); }
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

        /* Responsive */
        @media (max-width: 900px) {
            .legal-layout { grid-template-columns: 1fr; }
            .toc-card { position: static; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .header-nav { display: none; }
            .hamburger-menu { display: flex; }
        }
        @media (max-width: 600px) {
            .footer-grid { grid-template-columns: 1fr; gap: 24px; }
            .footer-bottom { flex-direction: column; text-align: center; }
            .footer-bottom-links { justify-content: center; flex-wrap: wrap; }
            .help-options { right: 14px; left: 14px; bottom: 80px; min-width: auto; }
            .page-hero h1 { font-size: 22px; }
        }
    </style>
</head>
<body>

<div id="particles-container"></div>

<!-- HEADER -->
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

        <!-- Hero -->
        <div class="page-hero">
            <div class="page-hero-badge"><i class="fas fa-shield-alt"></i> Legal & Kebijakan</div>
            <h1>Privasi, Disclaimer & Syarat Layanan</h1>
            <p>Baca dengan seksama sebelum menggunakan layanan WARUNGERIK STORE</p>
            <div class="page-hero-updated"><i class="fas fa-clock"></i> Terakhir diperbarui: <?= date('d F Y') ?></div>
        </div>

        <div class="legal-layout">

            <!-- SIDEBAR TOC -->
            <aside>
                <div class="toc-card">
                    <div class="toc-header"><i class="fas fa-list"></i> &nbsp;Daftar Isi</div>
                    <div class="toc-body">

                        <div class="toc-section">
                            <div class="toc-section-title active" onclick="scrollToSection('privacy')">
                                <i class="fas fa-lock"></i> Kebijakan Privasi
                            </div>
                            <div class="toc-items">
                                <a href="#priv-1">Data yang Dikumpulkan</a>
                                <a href="#priv-2">Penggunaan Data</a>
                                <a href="#priv-3">Keamanan Data</a>
                                <a href="#priv-4">Hak Pengguna</a>
                                <a href="#priv-5">Cookie</a>
                            </div>
                        </div>

                        <div class="toc-section">
                            <div class="toc-section-title" onclick="scrollToSection('disclaimer')">
                                <i class="fas fa-exclamation-triangle"></i> Disclaimer
                            </div>
                            <div class="toc-items">
                                <a href="#disc-1">Batasan Tanggung Jawab</a>
                                <a href="#disc-2">Keakuratan Informasi</a>
                                <a href="#disc-3">Tautan Pihak Ketiga</a>
                            </div>
                        </div>

                        <div class="toc-section">
                            <div class="toc-section-title" onclick="scrollToSection('tos')">
                                <i class="fas fa-file-contract"></i> Syarat Layanan
                            </div>
                            <div class="toc-items">
                                <a href="#tos-1">Penerimaan Syarat</a>
                                <a href="#tos-2">Aturan Penggunaan</a>
                                <a href="#tos-3">Pembayaran</a>
                                <a href="#tos-4">Refund & Komplain</a>
                                <a href="#tos-5">Penghentian Layanan</a>
                                <a href="#tos-6">Perubahan Syarat</a>
                            </div>
                        </div>

                    </div>
                </div>
            </aside>

            <!-- MAIN CONTENT -->
            <div class="legal-content">

                <!-- ━━━ PRIVACY POLICY ━━━ -->
                <div class="legal-section-card" id="privacy">
                    <div class="legal-section-head">
                        <div class="legal-section-icon"><i class="fas fa-lock"></i></div>
                        <h2>Kebijakan Privasi</h2>
                        <span class="section-tag">Privacy Policy</span>
                    </div>
                    <div class="legal-section-body">
                        <div class="legal-intro">
                            WARUNGERIK STORE berkomitmen untuk melindungi privasi dan keamanan data pribadi Anda. Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan menjaga informasi Anda saat menggunakan layanan kami.
                        </div>

                        <!-- 1 -->
                        <div class="legal-article" id="priv-1">
                            <div class="legal-article-num">
                                <div class="num-badge">01</div>
                                <h3>Data yang Kami Kumpulkan</h3>
                            </div>
                            <p>Saat Anda menggunakan layanan kami, kami dapat mengumpulkan informasi berikut:</p>
                            <ul class="legal-list">
                                <li>Nama atau username yang Anda berikan saat bertransaksi</li>
                                <li>Nomor WhatsApp atau kontak Telegram untuk keperluan konfirmasi pesanan</li>
                                <li>ID Game / Username In-Game yang diperlukan untuk proses top-up</li>
                                <li>Riwayat transaksi dan pesanan yang dilakukan</li>
                                <li>Data teknis seperti alamat IP, jenis perangkat, dan browser (dikumpulkan secara otomatis)</li>
                            </ul>
                        </div>

                        <!-- 2 -->
                        <div class="legal-article" id="priv-2">
                            <div class="legal-article-num">
                                <div class="num-badge">02</div>
                                <h3>Penggunaan Data</h3>
                            </div>
                            <p>Data yang kami kumpulkan digunakan <strong>hanya</strong> untuk keperluan:</p>
                            <ul class="legal-list">
                                <li>Memproses dan menyelesaikan transaksi top-up yang Anda lakukan</li>
                                <li>Mengkonfirmasi pesanan dan mengirimkan notifikasi terkait status pesanan</li>
                                <li>Memberikan layanan pelanggan dan menyelesaikan keluhan</li>
                                <li>Meningkatkan kualitas layanan kami secara berkelanjutan</li>
                                <li>Mencegah dan mendeteksi penipuan atau penyalahgunaan layanan</li>
                            </ul>
                            <div class="legal-highlight">
                                <i class="fas fa-info-circle"></i>
                                <p>Kami TIDAK menjual, menyewakan, atau membagikan data pribadi Anda kepada pihak ketiga untuk keperluan pemasaran tanpa seizin Anda.</p>
                            </div>
                        </div>

                        <!-- 3 -->
                        <div class="legal-article" id="priv-3">
                            <div class="legal-article-num">
                                <div class="num-badge">03</div>
                                <h3>Keamanan Data</h3>
                            </div>
                            <p>Kami menerapkan langkah-langkah keamanan teknis dan organisasional yang wajar untuk melindungi data Anda dari akses yang tidak sah, perubahan, pengungkapan, atau penghapusan. Namun demikian, tidak ada metode transmisi data melalui internet yang 100% aman.</p>
                            <ul class="legal-list">
                                <li>Data disimpan di server yang dilindungi dengan enkripsi</li>
                                <li>Akses ke data dibatasi hanya untuk personel yang berwenang</li>
                                <li>Sistem dipantau secara berkala untuk mendeteksi ancaman keamanan</li>
                            </ul>
                        </div>

                        <!-- 4 -->
                        <div class="legal-article" id="priv-4">
                            <div class="legal-article-num">
                                <div class="num-badge">04</div>
                                <h3>Hak Pengguna</h3>
                            </div>
                            <p>Sebagai pengguna, Anda berhak untuk:</p>
                            <ul class="legal-list">
                                <li>Mengakses data pribadi yang kami simpan tentang Anda</li>
                                <li>Meminta koreksi data yang tidak akurat</li>
                                <li>Meminta penghapusan data Anda (dengan ketentuan tertentu)</li>
                                <li>Mengajukan keberatan atas penggunaan data Anda</li>
                            </ul>
                            <p>Untuk menggunakan hak-hak ini, hubungi kami melalui Telegram atau WhatsApp.</p>
                        </div>

                        <!-- 5 -->
                        <div class="legal-article" id="priv-5">
                            <div class="legal-article-num">
                                <div class="num-badge">05</div>
                                <h3>Penggunaan Cookie</h3>
                            </div>
                            <p>Website ini menggunakan cookie dan teknologi serupa untuk meningkatkan pengalaman pengguna, seperti menyimpan preferensi dan sesi login. Anda dapat mengatur browser untuk menolak cookie, namun beberapa fitur situs mungkin tidak berfungsi dengan optimal.</p>
                        </div>

                    </div>
                </div>

                <!-- ━━━ DISCLAIMER ━━━ -->
                <div class="legal-section-card" id="disclaimer">
                    <div class="legal-section-head">
                        <div class="legal-section-icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <h2>Disclaimer</h2>
                        <span class="section-tag">Penyangkalan</span>
                    </div>
                    <div class="legal-section-body">
                        <div class="legal-intro">
                            Informasi di bawah ini penting untuk dipahami sebelum menggunakan layanan WARUNGERIK STORE. Dengan menggunakan layanan kami, Anda dianggap telah memahami dan menerima disclaimer ini.
                        </div>

                        <!-- D1 -->
                        <div class="legal-article" id="disc-1">
                            <div class="legal-article-num">
                                <div class="num-badge">01</div>
                                <h3>Batasan Tanggung Jawab</h3>
                            </div>
                            <p>WARUNGERIK STORE tidak bertanggung jawab atas:</p>
                            <ul class="legal-list">
                                <li>Kerugian yang timbul akibat kesalahan input data (ID Game, Server, dll.) oleh pengguna</li>
                                <li>Gangguan atau pemeliharaan sistem dari pihak penyedia game/publisher</li>
                                <li>Keterlambatan proses yang disebabkan oleh gangguan jaringan di luar kendali kami</li>
                                <li>Akun game yang diblokir atau dibanned oleh publisher karena pelanggaran ketentuan mereka</li>
                                <li>Perubahan harga atau ketersediaan item dari pihak publisher secara sepihak</li>
                            </ul>
                            <div class="legal-highlight">
                                <i class="fas fa-exclamation-circle"></i>
                                <p>Pastikan Anda memasukkan data yang benar dan valid. Transaksi yang sudah diproses dengan data benar tidak dapat dibatalkan.</p>
                            </div>
                        </div>

                        <!-- D2 -->
                        <div class="legal-article" id="disc-2">
                            <div class="legal-article-num">
                                <div class="num-badge">02</div>
                                <h3>Keakuratan Informasi</h3>
                            </div>
                            <p>Meskipun kami berupaya menjaga keakuratan informasi di website ini, harga, deskripsi produk, dan ketersediaan stok dapat berubah sewaktu-waktu tanpa pemberitahuan terlebih dahulu. Kami tidak memberikan jaminan bahwa informasi yang tersedia selalu akurat, lengkap, atau terkini.</p>
                        </div>

                        <!-- D3 -->
                        <div class="legal-article" id="disc-3">
                            <div class="legal-article-num">
                                <div class="num-badge">03</div>
                                <h3>Tautan Pihak Ketiga</h3>
                            </div>
                            <p>Website kami mungkin menampilkan gambar atau referensi dari platform lain (seperti gambar produk game). WARUNGERIK STORE tidak bertanggung jawab atas konten, kebijakan privasi, atau praktik dari website pihak ketiga tersebut. Nama dan logo game adalah milik publisher masing-masing.</p>
                        </div>

                    </div>
                </div>

                <!-- ━━━ TERMS OF SERVICE ━━━ -->
                <div class="legal-section-card" id="tos">
                    <div class="legal-section-head">
                        <div class="legal-section-icon"><i class="fas fa-file-contract"></i></div>
                        <h2>Syarat & Ketentuan Layanan</h2>
                        <span class="section-tag">Terms of Service</span>
                    </div>
                    <div class="legal-section-body">
                        <div class="legal-intro">
                            Syarat dan Ketentuan ini mengatur penggunaan layanan WARUNGERIK STORE. Dengan melakukan transaksi atau menggunakan layanan kami, Anda dianggap telah membaca, memahami, dan menyetujui seluruh ketentuan berikut.
                        </div>

                        <!-- T1 -->
                        <div class="legal-article" id="tos-1">
                            <div class="legal-article-num">
                                <div class="num-badge">01</div>
                                <h3>Penerimaan Syarat</h3>
                            </div>
                            <p>Dengan mengakses dan menggunakan layanan WARUNGERIK STORE, Anda menyatakan bahwa Anda berusia minimal 13 tahun atau telah mendapatkan izin dari orang tua/wali, dan memiliki kapasitas hukum untuk membuat perjanjian yang mengikat.</p>
                        </div>

                        <!-- T2 -->
                        <div class="legal-article" id="tos-2">
                            <div class="legal-article-num">
                                <div class="num-badge">02</div>
                                <h3>Aturan Penggunaan Layanan</h3>
                            </div>
                            <p>Pengguna dilarang melakukan hal-hal berikut:</p>
                            <ul class="legal-list">
                                <li>Menggunakan layanan untuk tujuan ilegal atau melanggar hukum yang berlaku di Indonesia</li>
                                <li>Melakukan pembelian menggunakan akun/kartu pembayaran orang lain tanpa izin</li>
                                <li>Melakukan chargeback palsu atau klaim pengembalian dana yang tidak berdasar</li>
                                <li>Mencoba meretas, memanipulasi, atau mengganggu sistem kami</li>
                                <li>Menyalahgunakan sistem komplain atau layanan pelanggan</li>
                                <li>Membeli untuk dijual kembali tanpa izin resmi dari kami</li>
                            </ul>
                            <div class="legal-highlight">
                                <i class="fas fa-ban"></i>
                                <p>Pelanggaran aturan di atas dapat mengakibatkan pemblokiran akses layanan dan/atau tindakan hukum sesuai peraturan yang berlaku.</p>
                            </div>
                        </div>

                        <!-- T3 -->
                        <div class="legal-article" id="tos-3">
                            <div class="legal-article-num">
                                <div class="num-badge">03</div>
                                <h3>Pembayaran</h3>
                            </div>
                            <p>Ketentuan pembayaran yang berlaku di WARUNGERIK STORE:</p>
                            <table class="legal-table">
                                <thead>
                                    <tr>
                                        <th>Metode</th>
                                        <th>Keterangan</th>
                                        <th>Waktu Verifikasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>QRIS</td>
                                        <td>Scan & bayar otomatis</td>
                                        <td>Real-time</td>
                                    </tr>
                                    <tr>
                                        <td>Transfer Bank (BRI, BCA)</td>
                                        <td>Transfer manual ke rekening</td>
                                        <td>1–5 menit</td>
                                    </tr>
                                    <tr>
                                        <td>SeaBank</td>
                                        <td>Transfer antar SeaBank</td>
                                        <td>Real-time</td>
                                    </tr>
                                </tbody>
                            </table>
                            <p style="margin-top:12px;">Pesanan akan diproses setelah pembayaran terverifikasi. Jangan kirim bukti transfer palsu — tindakan tersebut merupakan tindak pidana penipuan.</p>
                        </div>

                        <!-- T4 -->
                        <div class="legal-article" id="tos-4">
                            <div class="legal-article-num">
                                <div class="num-badge">04</div>
                                <h3>Kebijakan Refund & Komplain</h3>
                            </div>
                            <p>Kami memiliki kebijakan refund sebagai berikut:</p>
                            <ul class="legal-list">
                                <li><strong>Refund penuh</strong> diberikan jika transaksi gagal dan item tidak masuk ke akun game Anda setelah konfirmasi dari kami</li>
                                <li><strong>Tidak ada refund</strong> jika item sudah berhasil masuk ke akun game yang dituju</li>
                                <li><strong>Tidak ada refund</strong> jika kesalahan input data (ID/Server) adalah dari pihak pembeli</li>
                                <li>Komplain harus diajukan maksimal <strong>1×24 jam</strong> setelah transaksi selesai</li>
                            </ul>
                            <div class="legal-highlight">
                                <i class="fas fa-headset"></i>
                                <p>Untuk mengajukan komplain, hubungi CS kami via Telegram @<?= $username_tele ?> atau WhatsApp +<?= $nomor_wa ?> dengan menyertakan nomor pesanan Anda.</p>
                            </div>
                        </div>

                        <!-- T5 -->
                        <div class="legal-article" id="tos-5">
                            <div class="legal-article-num">
                                <div class="num-badge">05</div>
                                <h3>Penghentian Layanan</h3>
                            </div>
                            <p>WARUNGERIK STORE berhak untuk menghentikan, menangguhkan, atau membatasi akses layanan kepada pengguna yang terbukti melanggar Syarat & Ketentuan ini, melakukan penipuan, atau tindakan yang merugikan pihak lain, tanpa pemberitahuan terlebih dahulu.</p>
                        </div>

                        <!-- T6 -->
                        <div class="legal-article" id="tos-6">
                            <div class="legal-article-num">
                                <div class="num-badge">06</div>
                                <h3>Perubahan Syarat & Ketentuan</h3>
                            </div>
                            <p>WARUNGERIK STORE berhak mengubah Syarat & Ketentuan ini sewaktu-waktu. Perubahan akan diinformasikan melalui website atau media sosial kami. Penggunaan layanan setelah perubahan berlaku dianggap sebagai persetujuan atas syarat yang baru.</p>
                        </div>

                    </div>
                </div>

                <!-- Contact Banner -->
                <div class="contact-banner">
                    <h3>Ada Pertanyaan Seputar Kebijakan Kami?</h3>
                    <p>Tim kami siap membantu Anda memahami kebijakan dan ketentuan layanan</p>
                    <div class="contact-banner-btns">
                        <a href="https://t.me/<?= $username_tele ?>" target="_blank" class="tg"><i class="fab fa-telegram"></i> Telegram</a>
                        <a href="https://wa.me/<?= $nomor_wa ?>" target="_blank" class="wa"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<!-- FOOTER -->
<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-grid">
                <div>
                    <img src="assets/images/logo.jpg" alt="Logo" class="footer-brand-img">
                    <div class="footer-brand-name">WARUNGERIK STORE</div>
                    <p class="footer-brand-desc">Panel penyedia layanan topup games terbaik #1 Indonesia, harga termurah dan proses super instan.</p>
                    <div class="footer-socials">
                        <a href="https://t.me/<?= $username_tele ?>" target="_blank" class="social-btn tg"><i class="fab fa-telegram"></i> Telegram</a>
                        <a href="https://wa.me/<?= $nomor_wa ?>" target="_blank" class="social-btn wa"><i class="fab fa-whatsapp"></i> WhatsApp</a>
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
                        <a href="legal.php">Kebijakan & Syarat</a>
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
        <a href="https://t.me/<?= $username_tele ?>" target="_blank" class="tg"><i class="fab fa-telegram"></i> Telegram</a>
        <a href="https://wa.me/<?= $nomor_wa ?>" target="_blank" class="wa"><i class="fab fa-whatsapp"></i> WhatsApp</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Particles
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

    // Hamburger
    const hamburger = document.getElementById('hamburger-toggle');
    const sideMenu  = document.getElementById('side-menu');
    const overlay   = document.getElementById('menu-overlay');
    hamburger?.addEventListener('click', () => { hamburger.classList.toggle('is-active'); sideMenu.classList.toggle('is-active'); overlay.classList.toggle('is-active'); });
    overlay?.addEventListener('click', () => { hamburger.classList.remove('is-active'); sideMenu.classList.remove('is-active'); overlay.classList.remove('is-active'); });

    // Help
    const helpBtn = document.getElementById('helpBtn');
    const helpOptions = document.getElementById('helpOptions');
    const closeHelp = document.getElementById('closeHelpBtn');
    helpBtn?.addEventListener('click', e => { e.preventDefault(); helpOptions.classList.toggle('show'); });
    closeHelp?.addEventListener('click', () => helpOptions.classList.remove('show'));
    window.addEventListener('click', e => { if (!helpBtn?.contains(e.target) && !helpOptions?.contains(e.target)) helpOptions?.classList.remove('show'); });

    // Active TOC on scroll
    const sections = document.querySelectorAll('.legal-section-card, .legal-article');
    const tocTitles = document.querySelectorAll('.toc-section-title');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.id;
                tocTitles.forEach(t => t.classList.remove('active'));
                if (id === 'privacy' || id?.startsWith('priv')) tocTitles[0].classList.add('active');
                if (id === 'disclaimer' || id?.startsWith('disc')) tocTitles[1].classList.add('active');
                if (id === 'tos' || id?.startsWith('tos')) tocTitles[2].classList.add('active');
            }
        });
    }, { rootMargin: '-80px 0px -60% 0px' });

    sections.forEach(s => observer.observe(s));
});

function scrollToSection(id) {
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
</script>
</body>
</html>