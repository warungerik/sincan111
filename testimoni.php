<?php
include 'includes/koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimoni Pelanggan - WARUNGERIK STORE</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <script defer src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        /* ═══════════════════════════════════════════
           DESIGN SYSTEM
        ═══════════════════════════════════════════ */
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
            --warn:          #b45309;
            --info:          #2563a8;
            --info-bg:       rgba(37,99,168,0.08);
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

        #particles-container {
            position: fixed; inset: 0; z-index: -1; pointer-events: none;
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

        /* ═══════════════════════════════════════════
           HEADER
        ═══════════════════════════════════════════ */
        .site-header {
            background: var(--surface); border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 200;
            height: 64px; display: flex; align-items: center;
        }

        .header-container {
            display: flex; align-items: center; justify-content: space-between; width: 100%;
        }

        .header-brand { display: flex; align-items: center; gap: 10px; }

        .header-logo-mark {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent), #e09b55);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; box-shadow: 0 4px 12px rgba(193,127,62,0.3); flex-shrink: 0;
        }

        .header-logo-mark img { width: 100%; height: 100%; object-fit: cover; }
        .header-brand-text { font-size: 15px; font-weight: 800; letter-spacing: -0.01em; }

        .header-nav { display: flex; align-items: center; gap: 4px; }

        .header-nav a {
            display: flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: 9px;
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

        .menu-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.25);
            backdrop-filter: blur(3px); z-index: 299; display: none;
        }

        .menu-overlay.is-active { display: block; }

        /* ═══════════════════════════════════════════
           PAGE
        ═══════════════════════════════════════════ */
        .page-wrap { padding: 40px 0 64px; }

        /* Section header */
        .section-head {
            text-align: center;
            margin-bottom: 32px;
            animation: fadeUp 0.45s ease both;
        }

        .section-head h1 {
            font-size: 26px; font-weight: 800;
            letter-spacing: -0.02em; margin-bottom: 8px;
        }

        .section-head h1 span { color: var(--accent); }

        .section-head p { font-size: 13.5px; color: var(--muted); }

        .section-head p strong { color: var(--text); }

        /* ═══════════════════════════════════════════
           TESTIMONI SLIDER
        ═══════════════════════════════════════════ */
        .slider-wrap {
            margin-bottom: 48px;
            animation: fadeUp 0.45s 0.08s ease both;
        }

        .swiper.testimoni-slider {
            width: 100%;
            padding: 12px 0 20px;
            overflow: hidden;
        }

        .swiper.testimoni-slider .swiper-wrapper {
            transition-timing-function: linear;
        }

        .swiper.testimoni-slider .swiper-slide {
            width: 300px;
            margin: 0 10px;
        }

        .testi-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            gap: 12px;
            height: 100%;
            transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s;
        }

        .testi-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
            border-color: var(--accent-border);
        }

        .testi-stars { display: flex; gap: 3px; }

        .testi-stars .fa-star { font-size: 13px; color: var(--accent); }
        .testi-stars .fa-star.empty { color: var(--border); }

        .testi-text {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.7;
            flex-grow: 1;
            font-style: italic;
            overflow-wrap: break-word;
        }

        .testi-author {
            display: flex;
            align-items: center;
            gap: 8px;
            padding-top: 10px;
            border-top: 1px solid var(--border2);
        }

        .testi-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--accent-bg);
            border: 1px solid var(--accent-border);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 800; color: var(--accent);
            flex-shrink: 0;
        }

        .testi-name { font-size: 13px; font-weight: 700; color: var(--text); }

        /* ═══════════════════════════════════════════
           FORM SECTION
        ═══════════════════════════════════════════ */
        .form-wrap {
            max-width: 580px;
            margin: 0 auto;
            animation: fadeUp 0.45s 0.14s ease both;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .card-header {
            padding: 20px 28px;
            border-bottom: 1px solid var(--border2);
            display: flex; align-items: center; gap: 10px;
        }

        .card-header-icon {
            width: 34px; height: 34px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0;
            background: var(--accent-bg); color: var(--accent);
        }

        .card-header h2 { font-size: 15px; font-weight: 800; }

        .card-body { padding: 24px 28px; }

        /* Alerts */
        .alert {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 14px; border-radius: 10px;
            font-size: 13px; font-weight: 600; margin-bottom: 20px;
        }

        .alert-success { background: var(--success-bg); color: var(--success); border: 1px solid var(--success-border); }
        .alert-danger  { background: var(--danger-bg);  color: var(--danger);  border: 1px solid var(--danger-border);  }

        /* Form elements */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: flex; align-items: center; gap: 7px;
            font-size: 11px; font-weight: 700;
            letter-spacing: 0.08em; text-transform: uppercase;
            color: var(--muted); margin-bottom: 8px;
        }

        .form-label i { color: var(--accent); font-size: 10px; }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--surface);
            font-family: var(--font);
            font-size: 13.5px;
            color: var(--text);
            outline: none;
            transition: all 0.15s;
        }

        .form-input::placeholder,
        .form-textarea::placeholder { color: var(--muted2); }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: var(--accent-border);
            box-shadow: 0 0 0 3px var(--accent-bg);
        }

        .form-select { appearance: none; cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238c8279' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; padding-right: 38px; }

        .form-textarea { resize: vertical; min-height: 120px; line-height: 1.65; }

        /* reCAPTCHA */
        .recaptcha-wrapper {
            display: flex; justify-content: flex-start; overflow: hidden;
        }

        /* Submit button */
        .btn-submit {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 13px;
            background: var(--accent); color: white;
            border: none; border-radius: 10px;
            font-family: var(--font); font-size: 14px; font-weight: 700;
            cursor: pointer; transition: all 0.15s;
            box-shadow: 0 2px 8px rgba(193,127,62,0.3);
        }

        .btn-submit:hover {
            background: var(--accent-hover);
            box-shadow: 0 4px 14px rgba(193,127,62,0.4);
            transform: translateY(-1px);
        }

        /* ═══════════════════════════════════════════
           FOOTER
        ═══════════════════════════════════════════ */
        .site-footer {
            background: var(--text); color: rgba(255,255,255,0.9); margin-top: 64px;
        }

        .footer-top { padding: 40px 0 32px; border-bottom: 1px solid rgba(255,255,255,0.08); }

        .footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 40px; }

        .footer-brand-img { width: 40px; height: 40px; border-radius: 10px; object-fit: cover; margin-bottom: 12px; }
        .footer-brand-name { font-size: 15px; font-weight: 800; color: white; margin-bottom: 6px; }
        .footer-brand-desc { font-size: 12.5px; color: rgba(255,255,255,0.5); line-height: 1.7; margin-bottom: 18px; }

        .footer-socials { display: flex; gap: 8px; flex-wrap: wrap; }

        .social-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: 9px;
            font-size: 12px; font-weight: 700; color: white; transition: all 0.15s;
        }

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

        /* Help Button */
        .help-button {
            position: fixed; bottom: 28px; right: 28px;
            width: 50px; height: 50px; background: var(--accent); border-radius: 50%;
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
        .help-options p { font-size: 12px; color: var(--muted); margin-bottom: 12px; }
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

        /* Animations */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }

        /* ═══════════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════════ */
        @media (max-width: 900px) {
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .header-nav { display: none; }
            .hamburger-menu { display: flex; }
        }

        @media (max-width: 768px) {
            .footer-grid { grid-template-columns: 1fr; gap: 24px; }
            .footer-bottom { flex-direction: column; text-align: center; }
            .footer-bottom-links { justify-content: center; flex-wrap: wrap; }
            .card-body { padding: 20px; }
            .card-header { padding: 16px 20px; }
            .help-options { right: 14px; bottom: 80px; min-width: auto; left: 14px; }
        }
    </style>
</head>
<body>

<div id="particles-container"></div>

<!-- Header -->
<header class="site-header">
    <div class="container header-container">
        <a href="index.php" class="header-brand">
            <div class="header-logo-mark">
                <img src="assets/images/logo.jpg" alt="Logo">
            </div>
            <span class="header-brand-text">WARUNGERIK STORE</span>
        </a>

        <nav class="header-nav">
            <a href="index.php"><i class="fas fa-home"></i> Beranda</a>
            <a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a>
            <a href="testimoni.php" class="active"><i class="fas fa-comment-dots"></i> Testimoni</a>
            <a href="cek_pesanan.php"><i class="fas fa-search"></i> Cek Pesanan</a>
            <a href="request_produk.php"><i class="fas fa-inbox"></i> Request</a>
        </nav>

        <button class="hamburger-menu" id="hamburger-toggle" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- Side Menu -->
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

<!-- Page -->
<main class="page-wrap">
    <div class="container">

        <!-- Section Header -->
        <div class="section-head">
            <h1>Apa Kata <span>Pelanggan</span> Kami</h1>
            <?php
            $result_count = mysqli_query($koneksi, "SELECT COUNT(id) AS total FROM testimoni WHERE status = 'approved'");
            $total = mysqli_fetch_assoc($result_count)['total'];
            if ($total > 0):
            ?>
            <p>Berdasarkan <strong><?= $total ?> ulasan</strong> pelanggan yang puas.</p>
            <?php endif; ?>
        </div>

        <!-- Slider -->
        <div class="slider-wrap">
            <div class="swiper testimoni-slider">
                <div class="swiper-wrapper">
                    <?php
                    $result_testi = mysqli_query($koneksi, "SELECT * FROM testimoni WHERE status = 'approved' ORDER BY tanggal_submit DESC");
                    if (mysqli_num_rows($result_testi) > 0):
                        while ($row = mysqli_fetch_assoc($result_testi)):
                            $initial = mb_strtoupper(mb_substr($row['nama'], 0, 1));
                    ?>
                    <div class="swiper-slide">
                        <div class="testi-card">
                            <div class="testi-stars">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="fas fa-star <?= $i < $row['rating'] ? '' : 'empty' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="testi-text">"<?= htmlspecialchars($row['testimoni']) ?>"</p>
                            <div class="testi-author">
                                <div class="testi-avatar"><?= $initial ?></div>
                                <span class="testi-name"><?= htmlspecialchars($row['nama']) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php
                        endwhile;
                    else:
                        echo "<p style='padding:0 20px;color:var(--muted);'>Belum ada testimoni.</p>";
                    endif;
                    ?>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="form-wrap">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-icon"><i class="fas fa-pen"></i></div>
                    <h2>Berikan Ulasan Anda</h2>
                </div>
                <div class="card-body">

                    <?php if (isset($_GET['status'])): ?>
                        <?php if ($_GET['status'] === 'sukses'): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                Terima kasih! Testimoni Anda telah dikirim dan akan kami review.
                            </div>
                        <?php elseif ($_GET['status'] === 'gagal'): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle"></i>
                                Maaf, terjadi kesalahan. Silakan coba lagi.
                            </div>
                        <?php elseif ($_GET['status'] === 'captcha_gagal'): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-shield-alt"></i>
                                Verifikasi CAPTCHA gagal. Centang "I'm not a robot" dan coba lagi.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <form action="proses_testimoni.php" method="POST">

                        <div class="form-group">
                            <label class="form-label" for="nama">
                                <i class="fas fa-user"></i> Nama Anda
                            </label>
                            <input type="text" id="nama" name="nama" class="form-input" placeholder="Masukkan nama Anda" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="rating">
                                <i class="fas fa-star"></i> Rating
                            </label>
                            <select id="rating" name="rating" class="form-select" required>
                                <option value="5">⭐⭐⭐⭐⭐ — Bintang 5</option>
                                <option value="4">⭐⭐⭐⭐ — Bintang 4</option>
                                <option value="3">⭐⭐⭐ — Bintang 3</option>
                                <option value="2">⭐⭐ — Bintang 2</option>
                                <option value="1">⭐ — Bintang 1</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="testimoni">
                                <i class="fas fa-comment"></i> Pesan Testimoni
                            </label>
                            <textarea id="testimoni" name="testimoni" class="form-textarea" placeholder="Tulis pengalaman Anda berbelanja di sini..." required></textarea>
                        </div>

                        <div class="form-group">
                            <div class="recaptcha-wrapper">
                                <div class="g-recaptcha" data-sitekey="6Ldkdm4sAAAAACl5v-flTYTZU09vhEl4TADqi0e_"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> Kirim Testimoni
                        </button>

                    </form>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- Footer -->
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

<!-- Help Button -->
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

    // — Particles —
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

    // — Mobile Menu —
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

    // — Help Button —
    const helpBtn     = document.getElementById('helpBtn');
    const helpOptions = document.getElementById('helpOptions');
    const closeHelp   = document.getElementById('closeHelpBtn');

    helpBtn?.addEventListener('click', e => { e.preventDefault(); helpOptions.classList.toggle('show'); });
    closeHelp?.addEventListener('click', () => helpOptions.classList.remove('show'));
    window.addEventListener('click', e => {
        if (!helpBtn?.contains(e.target) && !helpOptions?.contains(e.target)) helpOptions?.classList.remove('show');
    });

    // — Swiper Testimoni (auto-scroll) —
    if (document.querySelector('.testimoni-slider')) {
        new Swiper('.testimoni-slider', {
            loop: true,
            slidesPerView: 'auto',
            speed: 5000,
            autoplay: { delay: 0, disableOnInteraction: false },
            grabCursor: true,
        });
    }
});
</script>
</body>
</html>