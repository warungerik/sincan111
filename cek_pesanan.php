<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Pesanan - WARUNGERIK STORE</title>
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
            --info:          #2563a8;
            --info-bg:       rgba(37,99,168,0.08);
            --warn:          #b45309;
            --warn-bg:       rgba(180,83,9,0.08);
            --warn-border:   rgba(180,83,9,0.2);
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

        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        img { display: block; max-width: 100%; }
        a { text-decoration: none; color: inherit; }

        #particles-container {
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
        }

        .site-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 200;
            height: 64px;
            display: flex;
            align-items: center;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-logo-mark {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent), #e09b55);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(193,127,62,0.3);
            flex-shrink: 0;
        }

        .header-logo-mark img { width: 100%; height: 100%; object-fit: cover; }

        .header-brand-text { font-size: 15px; font-weight: 800; letter-spacing: -0.01em; }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .header-nav a {
            display: flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: 9px;
            font-size: 13px; font-weight: 600; color: var(--muted);
            transition: all 0.15s;
        }

        .header-nav a:hover { background: var(--surface2); color: var(--text); }
        .header-nav a.active { background: var(--accent-bg); color: var(--accent); }

        .hamburger-menu {
            display: none;
            flex-direction: column; gap: 5px; padding: 8px;
            background: none; border: 1px solid var(--border);
            border-radius: 9px; cursor: pointer;
            width: 38px; height: 38px;
            align-items: center; justify-content: center;
        }

        .hamburger-menu span {
            display: block; width: 18px; height: 2px;
            background: var(--muted); border-radius: 2px; transition: all 0.3s;
        }

        .side-menu {
            position: fixed; top: 0; right: -280px;
            width: 260px; height: 100%;
            background: var(--surface); border-left: 1px solid var(--border);
            z-index: 300; padding: 80px 16px 24px;
            transition: right 0.3s cubic-bezier(.4,0,.2,1);
            box-shadow: var(--shadow-xl);
        }

        .side-menu.is-active { right: 0; }
        .side-menu ul { list-style: none; }

        .side-menu ul li a {
            display: flex; align-items: center;
            padding: 11px 14px; border-radius: 10px;
            font-size: 14px; font-weight: 600; color: var(--muted);
            transition: all 0.15s; margin-bottom: 2px;
        }

        .side-menu ul li a:hover { background: var(--surface2); color: var(--text); }

        .menu-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.25); backdrop-filter: blur(3px);
            z-index: 299; display: none;
        }

        .menu-overlay.is-active { display: block; }

        .page-wrap {
            min-height: calc(100vh - 64px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        .page-inner {
            width: 100%;
            max-width: 520px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12.5px;
            color: var(--muted);
            margin-bottom: 20px;
        }

        .breadcrumb a { color: var(--muted); transition: color 0.15s; }
        .breadcrumb a:hover { color: var(--accent); }
        .breadcrumb .sep { font-size: 9px; }
        .breadcrumb .current { color: var(--text); font-weight: 600; }

        .main-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            animation: fadeUp 0.45s ease both;
        }

        .main-card::before {
            content: '';
            display: block;
            height: 4px;
            background: linear-gradient(90deg, var(--accent), #e09b55);
        }

        .card-hero {
            padding: 32px 32px 24px;
            text-align: center;
            border-bottom: 1px solid var(--border2);
        }

        .hero-icon {
            width: 64px; height: 64px;
            background: var(--accent-bg);
            border: 1px solid var(--accent-border);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px;
            color: var(--accent);
            margin: 0 auto 16px;
            animation: floatIcon 3s ease-in-out infinite;
        }

        .card-hero h1 {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .card-hero p {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.6;
        }

        .card-body { padding: 28px 32px; }

        .form-label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .form-label i { color: var(--accent); font-size: 10px; }

        .input-row {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
        }

        .form-input {
            flex: 1;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--surface);
            font-family: var(--mono);
            font-size: 13px;
            color: var(--text);
            outline: none;
            transition: all 0.15s;
        }

        .form-input::placeholder { color: var(--muted2); font-family: var(--font); }

        .form-input:focus {
            border-color: var(--accent-border);
            box-shadow: 0 0 0 3px var(--accent-bg);
        }

        .btn-search {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 12px 20px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: var(--font);
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
            box-shadow: 0 2px 8px rgba(193,127,62,0.25);
            white-space: nowrap;
        }

        .btn-search:hover {
            background: var(--accent-hover);
            box-shadow: 0 4px 14px rgba(193,127,62,0.35);
            transform: translateY(-1px);
        }

        .btn-search:disabled {
            background: var(--muted2);
            box-shadow: none;
            transform: none;
            cursor: not-allowed;
        }

        #result-container { margin-top: 4px; }

        .result-divider {
            border: none;
            border-top: 1px dashed var(--border);
            margin: 20px 0;
        }

        .loading-state {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 24px;
            color: var(--muted);
            font-size: 13.5px;
            font-weight: 600;
        }

        .loading-state i { animation: spin 0.8s linear infinite; color: var(--accent); }

        .error-state {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            background: var(--danger-bg);
            border: 1px solid var(--danger-border);
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--danger);
        }

        .result-box {
            animation: fadeUp 0.35s ease both;
        }

        .result-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 700;
            color: var(--success);
            margin-bottom: 16px;
        }

        .result-title i { font-size: 12px; }

        .details-grid {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 14px;
            background: var(--surface2);
            border: 1px solid var(--border2);
            border-radius: 10px;
            transition: border-color 0.15s;
            gap: 12px;
        }

        .detail-item:hover { border-color: var(--border); }

        .detail-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            color: var(--muted2);
            flex-shrink: 0;
        }

        .detail-value {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text);
            text-align: right;
            word-break: break-word;
        }

        .detail-value.mono { font-family: var(--mono); font-size: 13px; }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .status-success { background: var(--success-bg); color: var(--success); border: 1px solid var(--success-border); }
        .status-pending  { background: var(--warn-bg); color: var(--warn); border: 1px solid var(--warn-border); }
        .status-failed   { background: var(--danger-bg); color: var(--danger); border: 1px solid var(--danger-border); }

        .price-display {
            font-family: var(--mono);
            font-size: 16px;
            font-weight: 700;
            color: var(--success);
        }

        /* ── Deskripsi produk ── */
        .desc-block {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        /* Baris pertama: teks pendahuluan (sebelum → pertama) */
        .desc-intro {
            font-size: 13px;
            color: var(--muted);
            font-weight: 500;
            line-height: 1.6;
        }

        /* Setiap item / baris setelah → atau newline */
        .desc-row {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 13px;
            color: var(--text);
            font-weight: 500;
            line-height: 1.55;
            padding: 6px 10px;
            background: var(--surface);
            border: 1px solid var(--border2);
            border-radius: 8px;
        }

        .desc-row .desc-arrow {
            color: var(--accent);
            font-weight: 700;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .desc-row a {
            color: var(--accent);
            text-decoration: underline;
            word-break: break-all;
            font-weight: 600;
        }

        .desc-row a:hover { color: var(--accent-hover); }

        /* ── License ── */
        .license-section { margin-top: 4px; }

        .license-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            color: var(--muted2);
            margin-bottom: 8px;
        }

        .license-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            transition: border-color 0.15s;
        }

        .license-box:hover { border-color: var(--accent-border); }

        #licenseKey {
            flex: 1;
            font-family: var(--mono);
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
            line-height: 1.65;
            word-break: break-all;
        }

        .copy-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: var(--accent-bg);
            border: 1px solid var(--accent-border);
            border-radius: 8px;
            color: var(--accent);
            font-family: var(--font);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .copy-btn:hover { background: var(--accent); color: white; }

        .copy-btn.copied {
            background: var(--success-bg);
            border-color: var(--success-border);
            color: var(--success);
        }

        .help-tip {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: var(--info-bg);
            border: 1px solid rgba(37,99,168,0.15);
            border-radius: 9px;
            font-size: 12px;
            color: var(--info);
            margin-top: 16px;
        }

        .help-tip i { flex-shrink: 0; font-size: 12px; }

        .site-footer {
            background: var(--text);
            color: rgba(255,255,255,0.9);
        }

        .footer-bottom {
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-bottom p { font-size: 12px; color: rgba(255,255,255,0.4); }

        .footer-links { display: flex; gap: 16px; }
        .footer-links a { font-size: 12px; color: rgba(255,255,255,0.4); transition: color 0.15s; }
        .footer-links a:hover { color: rgba(255,255,255,0.8); }

        .help-button {
            position: fixed; bottom: 28px; right: 28px;
            width: 50px; height: 50px;
            background: var(--accent); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 18px;
            box-shadow: 0 6px 20px rgba(193,127,62,0.4);
            cursor: pointer; z-index: 999; transition: all 0.15s;
        }

        .help-button:hover { transform: scale(1.08); }

        .help-options {
            position: fixed; bottom: 90px; right: 28px;
            background: var(--surface); border: 1px solid var(--border);
            padding: 18px; border-radius: var(--radius); box-shadow: var(--shadow-xl);
            z-index: 998; min-width: 240px;
            transform: translateY(10px); opacity: 0; visibility: hidden; transition: all 0.2s;
        }

        .help-options.show { transform: translateY(0); opacity: 1; visibility: visible; }
        .help-options h4 { font-size: 13px; font-weight: 800; margin-bottom: 4px; }
        .help-options p  { font-size: 12px; color: var(--muted); margin-bottom: 12px; }
        .help-options-btns { display: flex; flex-direction: column; gap: 7px; }

        .help-options-btns a {
            display: flex; align-items: center; gap: 8px;
            padding: 9px 13px; border-radius: 9px;
            font-size: 13px; font-weight: 700; color: white; transition: opacity 0.15s;
        }

        .help-options-btns a:hover { opacity: 0.9; }
        .help-options-btns a.tg { background: #0088cc; }
        .help-options-btns a.wa { background: #25D366; }
        .help-close { position: absolute; top: 10px; right: 12px; font-size: 18px; color: var(--muted2); cursor: pointer; }
        .help-close:hover { color: var(--text); }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        @keyframes floatIcon {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-6px); }
        }

        @media (max-width: 768px) {
            .header-nav { display: none; }
            .hamburger-menu { display: flex; }
            .page-wrap { padding: 24px 16px; align-items: flex-start; }
            .card-hero { padding: 24px 22px 18px; }
            .card-body { padding: 22px; }
            .input-row { flex-direction: column; }
            .btn-search { justify-content: center; }
            .detail-item { flex-direction: column; align-items: flex-start; gap: 4px; }
            .detail-value { text-align: left; }
            .license-box { flex-direction: column; }
            .copy-btn { justify-content: center; width: 100%; }
            .footer-bottom { flex-direction: column; text-align: center; }
            .footer-links { justify-content: center; flex-wrap: wrap; }
            .help-options { right: 14px; bottom: 80px; min-width: auto; left: 14px; }
        }
    </style>
</head>
<body>

<div id="particles-container"></div>

<header class="site-header">
    <div class="header-container">
        <a href="index.php" class="header-brand">
            <div class="header-logo-mark">
                <img src="assets/images/logo.jpg" alt="Logo">
            </div>
            <span class="header-brand-text">WARUNGERIK STORE</span>
        </a>

        <nav class="header-nav">
            <a href="index.php"><i class="fas fa-home"></i> Beranda</a>
            <a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a>
            <a href="testimoni.php"><i class="fas fa-comment-dots"></i> Testimoni</a>
            <a href="cek_pesanan.php" class="active"><i class="fas fa-search"></i> Cek Pesanan</a>
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
    <div class="page-inner">

        <div class="breadcrumb">
            <a href="index.php"><i class="fas fa-home"></i></a>
            <span class="sep"><i class="fas fa-chevron-right"></i></span>
            <span class="current">Cek Pesanan</span>
        </div>

        <div class="main-card">

            <div class="card-hero">
                <div class="hero-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <h1>Cek Status Pesanan</h1>
                <p>Masukkan nomor pesanan untuk melihat detail dan status pembayaran</p>
            </div>

            <div class="card-body">
                <form id="searchForm">
                    <label class="form-label" for="order_id">
                        <i class="fas fa-hashtag"></i> Nomor Pesanan
                    </label>
                    <div class="input-row">
                        <input
                            type="text"
                            id="order_id"
                            name="order_id"
                            class="form-input"
                            placeholder="WARUNGERIK-XXXX-XXX"
                            required
                            autocomplete="off"
                            spellcheck="false"
                        >
                        <button type="submit" id="submitBtn" class="btn-search">
                            <i class="fas fa-search"></i> Cek
                        </button>
                    </div>
                </form>

                <div id="result-container"></div>

                <div class="help-tip">
                    <i class="fas fa-info-circle"></i>
                    Rahasiakan detail pesanan kamu dari orang lain.
                </div>
            </div>
        </div>

    </div>
</main>

<footer class="site-footer">
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> WARUNGERIK STORE — All Rights Reserved</p>
        <div class="footer-links">
            <a href="index.php">Beranda</a>
            <a href="tentang.php">Tentang</a>
            <a href="legal.php">Kebijakan</a>
            <a href="testimoni.php">Testimoni</a>
            <a href="request_produk.php">Request</a>
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

    // Particles
    if (typeof tsParticles !== 'undefined') {
        tsParticles.load('particles-container', {
            particles: {
                number: { value: 35, density: { enable: true, value_area: 900 } },
                color: { value: "#c17f3e" },
                shape: { type: "circle" },
                opacity: { value: 0.1, random: true },
                size: { value: 2.5, random: true },
                links: { enable: true, distance: 130, color: "#c17f3e", opacity: 0.06, width: 1 },
                move: { enable: true, speed: 1, outModes: { default: "bounce" } }
            },
            interactivity: {
                events: { onHover: { enable: true, mode: "grab" }, resize: true },
                modes: { grab: { distance: 120, links: { opacity: 0.15 } } }
            }
        });
    }

    // Mobile Menu
    const hamburger = document.getElementById('hamburger-toggle');
    const sideMenu  = document.getElementById('side-menu');
    const overlay   = document.getElementById('menu-overlay');

    hamburger?.addEventListener('click', () => {
        hamburger.classList.toggle('is-active');
        sideMenu.classList.toggle('is-active');
        overlay.classList.toggle('is-active');
    });
    overlay?.addEventListener('click', () => {
        hamburger.classList.remove('is-active');
        sideMenu.classList.remove('is-active');
        overlay.classList.remove('is-active');
    });

    // Help Button
    const helpBtn     = document.getElementById('helpBtn');
    const helpOptions = document.getElementById('helpOptions');
    const closeHelp   = document.getElementById('closeHelpBtn');

    helpBtn?.addEventListener('click', e => { e.preventDefault(); helpOptions.classList.toggle('show'); });
    closeHelp?.addEventListener('click', () => helpOptions.classList.remove('show'));
    window.addEventListener('click', e => {
        if (!helpBtn?.contains(e.target) && !helpOptions?.contains(e.target)) helpOptions?.classList.remove('show');
    });

    // Search Form
    const searchForm      = document.getElementById('searchForm');
    const orderIdInput    = document.getElementById('order_id');
    const resultContainer = document.getElementById('result-container');
    const submitBtn       = document.getElementById('submitBtn');

    searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const orderId = orderIdInput.value.trim();
        if (!orderId) return;

        resultContainer.innerHTML = `
            <hr class="result-divider">
            <div class="loading-state">
                <i class="fas fa-circle-notch"></i> Mencari pesanan...
            </div>
        `;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fas fa-circle-notch fa-spin"></i> Mencari...`;

        fetch(`api_cek_pesanan.php?order_id=${encodeURIComponent(orderId)}`)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const d = data.data;
                    const statusText = d.status_pembayaran.toUpperCase();

                    let statusClass = 'status-failed';
                    let statusIcon  = 'fa-times-circle';
                    if (['SUCCESS','LUNAS','PAID'].includes(statusText)) {
                        statusClass = 'status-success';
                        statusIcon  = 'fa-check-circle';
                    } else if (statusText === 'PENDING') {
                        statusClass = 'status-pending';
                        statusIcon  = 'fa-clock';
                    }

                    const tanggal = new Date(d.tanggal_transaksi).toLocaleDateString('id-ID', {
                        day: '2-digit', month: 'short', year: 'numeric',
                        hour: '2-digit', minute: '2-digit'
                    }) + ' WIB';

                    const harga = new Intl.NumberFormat('id-ID', {
                        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
                    }).format(d.harga);

                    // ── Render deskripsi produk ──────────────────────────────────
                    // Fungsi linkify: ubah URL jadi <a>
                    function linkify(text) {
                        return text.replace(
                            /(https?:\/\/[^\s]+)/g,
                            url => `<a href="${url}" target="_blank" rel="noopener noreferrer">${url}</a>`
                        );
                    }

                    let descHtml = '';
                    if (d.deskripsi_produk) {
                        // Pisah per newline (\r\n, \n) ATAU per "→"
                        // Langkah 1: normalisasi "→" menjadi newline agar satu path
                        const normalized = d.deskripsi_produk
                            .replace(/\r\n/g, '\n')   // Windows newline → \n
                            .replace(/→/g, '\n');      // panah → newline

                        // Langkah 2: split per \n, trim, buang baris kosong
                        const lines = normalized
                            .split('\n')
                            .map(s => s.trim())
                            .filter(s => s.length > 0);

                        if (lines.length > 0) {
                            // Baris pertama = intro (teks deskripsi sebelum daftar item)
                            const introLine = lines[0];
                            const itemLines = lines.slice(1);

                            descHtml = `
                                <div class="detail-item" style="flex-direction:column; align-items:flex-start; gap:10px;">
                                    <span class="detail-label">Deskripsi Produk</span>
                                    <div class="desc-block">
                                        <span class="desc-intro">${linkify(introLine)}</span>
                                        ${itemLines.map(line => `
                                            <div class="desc-row">
                                                <span class="desc-arrow">→</span>
                                                <span>${linkify(line)}</span>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            `;
                        }
                    }
                    // ────────────────────────────────────────────────────────────

                    resultContainer.innerHTML = `
                        <hr class="result-divider">
                        <div class="result-box">
                            <div class="result-title">
                                <i class="fas fa-check-circle"></i> Detail Pesanan Ditemukan
                            </div>
                            <div class="details-grid">
                                <div class="detail-item">
                                    <span class="detail-label">Status</span>
                                    <span class="detail-value">
                                        <span class="status-badge ${statusClass}">
                                            <i class="fas ${statusIcon}"></i> ${statusText}
                                        </span>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Nomor Pesanan</span>
                                    <span class="detail-value mono">${d.order_id}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Nama Pelanggan</span>
                                    <span class="detail-value">${d.nama_pelanggan}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Tanggal</span>
                                    <span class="detail-value mono">${tanggal}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Total Bayar</span>
                                    <span class="detail-value"><span class="price-display">${harga}</span></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Nama Produk</span>
                                    <span class="detail-value">${d.nama_produk ?? '-'}</span>
                                </div>
                                ${descHtml}
                            </div>

                            <div class="license-section" style="margin-top:12px;">
                                <div class="license-label"><i class="fas fa-key" style="margin-right:5px;color:var(--accent)"></i> Produk Kamu</div>
                                <div class="license-box">
                                    <span id="licenseKey">${d.key_terjual}</span>
                                    <button class="copy-btn" id="copyBtn" onclick="copyToClipboard()">
                                        <i class="fas fa-copy"></i> Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    resultContainer.innerHTML = `
                        <hr class="result-divider">
                        <div class="error-state">
                            <i class="fas fa-exclamation-circle"></i>
                            ${data.message}
                        </div>
                    `;
                }
            })
            .catch(() => {
                resultContainer.innerHTML = `
                    <hr class="result-divider">
                    <div class="error-state">
                        <i class="fas fa-wifi"></i>
                        Terjadi kesalahan koneksi. Coba lagi nanti.
                    </div>
                `;
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `<i class="fas fa-search"></i> Cek`;
            });
    });
});

function copyToClipboard() {
    const text = document.getElementById('licenseKey')?.innerText;
    const btn  = document.getElementById('copyBtn');
    if (!text || !btn) return;

    navigator.clipboard.writeText(text).then(() => {
        btn.innerHTML = `<i class="fas fa-check"></i> Tersalin!`;
        btn.classList.add('copied');
        btn.disabled = true;
        setTimeout(() => {
            btn.innerHTML = `<i class="fas fa-copy"></i> Copy`;
            btn.classList.remove('copied');
            btn.disabled = false;
        }, 2000);
    }).catch(() => {});
}
</script>
</body>
</html>