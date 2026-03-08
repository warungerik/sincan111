<?php
include 'includes/koneksi.php';

function stok_badge($row) {
    if (!$row['cek_stok']) return '';
    $stok = (int)$row['stok'];
    if ($stok <= 0) return '<span class="card-stock out-stock"><i class="fas fa-circle-xmark"></i> Habis</span>';
    if ($stok <= 5) return '<span class="card-stock low-stock"><i class="fas fa-triangle-exclamation"></i> Stok ' . $stok . '</span>';
    return '<span class="card-stock in-stock"><i class="fas fa-circle-check"></i> Stok ' . $stok . '</span>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="google-adsense-account" content="ca-pub-2861077713064091">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WARUNGERIK STORE - Tempatnya Dunia Digital.</title>
    <link rel="canonical" href="https://www.warungerik.com/" />
    <meta name="description" content="WARUNGERIK STORE adalah panel penyedia layanan topup games dan produk digital terbaik #1 di Indonesia. Dapatkan harga termurah, proses instan, dan pembayaran lengkap.">
    <meta name="keywords" content="topup game, WARUNGERIK STORE, voucher game, topup murah, panel smm, layanan otp, diamond ml, uc pubg, topup ff">
    <meta name="author" content="WARUNGERIK STORE">
    <meta property="og:title" content="WARUNGERIK STORE - Topup Games & Produk Digital Murah">
    <meta property="og:description" content="Panel penyedia layanan topup games terbaik #1 di Indonesia. Harga termurah, proses instan 24 jam, dan metode pembayaran lengkap.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.warungerik.com/">
    <meta property="og:image" content="https://www.warungerik.com/assets/images/banner.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="WARUNGERIK STORE">
    <meta property="fb:app_id" content="869669698498357" />
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="WARUNGERIK STORE - Topup Games & Produk Digital Murah">
    <meta name="twitter:description" content="Panel penyedia layanan topup games terbaik #1 di Indonesia. Harga termurah, proses instan 24 jam, dan metode pembayaran lengkap.">
    <meta name="twitter:image" content="https://www.warungerik.com/assets/images/banner.jpg">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <script defer src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>

    <style>
        :root {
            --bg:              #f7f5f2;
            --surface:         #ffffff;
            --surface2:        #faf9f7;
            --border:          #e8e4de;
            --border2:         #f0ede8;
            --text:            #1a1714;
            --muted:           #8c8279;
            --muted2:          #b5afa7;
            --accent:          #c17f3e;
            --accent-hover:    #a96d31;
            --accent-bg:       rgba(193,127,62,0.08);
            --accent-border:   rgba(193,127,62,0.25);
            --success:         #2d7a4f;
            --success-bg:      rgba(45,122,79,0.08);
            --info:            #2563a8;
            --info-bg:         rgba(37,99,168,0.08);
            --warn:            #b45309;
            --warn-bg:         rgba(180,83,9,0.08);
            --danger:          #b91c1c;
            --danger-bg:       rgba(185,28,28,0.08);
            --font:            'Plus Jakarta Sans', sans-serif;
            --mono:            'JetBrains Mono', monospace;
            --radius:          14px;
            --radius-lg:       20px;
            --shadow:          0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
            --shadow-lg:       0 2px 8px rgba(0,0,0,0.08), 0 12px 32px rgba(0,0,0,0.06);
            --shadow-xl:       0 4px 12px rgba(0,0,0,0.10), 0 20px 48px rgba(0,0,0,0.08);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: var(--font); background: var(--bg); color: var(--text); min-height: 100vh; line-height: 1.6; }
        img { display: block; max-width: 100%; }
        a { text-decoration: none; color: inherit; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

        /* ══════════════════════════════════════════
           LOADER — BRIGHT / SMOOTH VERSION
        ══════════════════════════════════════════ */
        #site-loader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: #faf9f7;
            background-image:
                radial-gradient(ellipse 80% 55% at 50% 45%,
                    rgba(193,127,62,0.09) 0%,
                    transparent 70%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
            transition: opacity 0.9s cubic-bezier(0.4,0,0.2,1),
                        visibility 0.9s cubic-bezier(0.4,0,0.2,1);
        }
        #site-loader.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        /* Corner decorations */
        .loader-corner {
            position: absolute;
            width: 28px;
            height: 28px;
            border-color: rgba(193,127,62,0.22);
            border-style: solid;
        }
        .loader-corner.tl { top: 28px; left: 28px;   border-width: 1.5px 0 0 1.5px; animation: lcIn 0.5s ease 0.1s both; }
        .loader-corner.tr { top: 28px; right: 28px;  border-width: 1.5px 1.5px 0 0; animation: lcIn 0.5s ease 0.2s both; }
        .loader-corner.bl { bottom: 28px; left: 28px;  border-width: 0 0 1.5px 1.5px; animation: lcIn 0.5s ease 0.3s both; }
        .loader-corner.br { bottom: 28px; right: 28px; border-width: 0 1.5px 1.5px 0; animation: lcIn 0.5s ease 0.4s both; }
        @keyframes lcIn { from { opacity:0; transform:scale(0.5); } to { opacity:1; transform:scale(1); } }

        /* Logo mark */
        .loader-logo {
            width: 54px;
            height: 54px;
            border-radius: 15px;
            overflow: hidden;
            background: linear-gradient(135deg, #c17f3e, #e09b55);
            box-shadow: 0 6px 20px rgba(193,127,62,0.28),
                        0 1px 4px rgba(0,0,0,0.06);
            opacity: 0;
            transform: scale(0.75) translateY(8px);
            animation: logoIn 0.7s cubic-bezier(0.34,1.56,0.64,1) 0.15s both;
            flex-shrink: 0;
        }
        .loader-logo img { width:100%; height:100%; object-fit:cover; }
        @keyframes logoIn {
            from { opacity:0; transform:scale(0.75) translateY(8px); }
            to   { opacity:1; transform:scale(1) translateY(0); }
        }

        /* Brand letters */
        .loader-brand {
            display: flex;
            align-items: flex-end;
            gap: 0;
            height: 58px;
            overflow: hidden;
        }
        .l-char {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: clamp(28px, 6.5vw, 46px);
            font-weight: 800;
            letter-spacing: 0.13em;
            color: #1a1714;
            display: inline-block;
            opacity: 0;
            transform: translateY(105%);
            animation: charUp 0.6s cubic-bezier(0.22,1,0.36,1) both;
            will-change: transform, opacity;
        }
        .l-char.accent { color: #c17f3e; }

        @keyframes charUp {
            0%   { opacity:0; transform:translateY(105%) rotate(2deg); }
            55%  { opacity:1; }
            100% { opacity:1; transform:translateY(0) rotate(0deg); }
        }

        /* Underline sweep */
        .loader-underline {
            width: 0%;
            max-width: 380px;
            align-self: center;
            height: 2px;
            margin-top: -10px;
            background: linear-gradient(90deg,
                transparent 0%,
                #c17f3e 30%,
                #e09b55 60%,
                transparent 100%);
            border-radius: 99px;
            opacity: 0;
            animation: underSweep 0.75s cubic-bezier(0.4,0,0.2,1) 1.35s both;
        }
        @keyframes underSweep {
            0%   { width:0%;   opacity:0; }
            15%  { opacity:1; }
            100% { width:100%; opacity:1; }
        }

        /* Tagline */
        .loader-tagline {
            font-family: 'JetBrains Mono', monospace;
            font-size: clamp(9px, 1.8vw, 11px);
            letter-spacing: 0.32em;
            text-transform: uppercase;
            color: #b5afa7;
            opacity: 0;
            animation: fadeSlideUp 0.55s ease 1.6s both;
        }

        /* Progress bar */
        .loader-bar-wrap {
            width: clamp(180px, 36vw, 290px);
            height: 2.5px;
            background: #ede9e3;
            border-radius: 99px;
            overflow: hidden;
            opacity: 0;
            animation: fadeSlideUp 0.45s ease 1.5s both;
        }
        .loader-bar {
            height: 100%;
            width: 0%;
            border-radius: 99px;
            background: linear-gradient(90deg, #c17f3e 0%, #e8a85f 50%, #c17f3e 100%);
            background-size: 300% 100%;
            animation:
                barFill    2.1s cubic-bezier(0.4,0,0.2,1) 0.5s both,
                barShimmer 1.8s linear 0.5s infinite;
            will-change: width, background-position;
        }
        @keyframes barFill {
            0%   { width:0%;   }
            55%  { width:72%;  }
            85%  { width:91%;  }
            100% { width:100%; }
        }
        @keyframes barShimmer {
            0%   { background-position: 120%  0; }
            100% { background-position: -120% 0; }
        }

        /* Dots */
        .loader-dots {
            display: flex;
            gap: 7px;
            opacity: 0;
            animation: fadeSlideUp 0.45s ease 1.85s both;
        }
        .loader-dots span {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #d9d2c9;
            animation: dotBounce 1.3s ease-in-out infinite;
            will-change: transform, background;
        }
        .loader-dots span:nth-child(2) { animation-delay: 0.18s; }
        .loader-dots span:nth-child(3) { animation-delay: 0.36s; }
        @keyframes dotBounce {
            0%,100% { transform:scaleY(1);   background:#d9d2c9; }
            42%      { transform:scaleY(1.9); background:#c17f3e; }
        }

        /* Shared util */
        @keyframes fadeSlideUp {
            from { opacity:0; transform:translateY(7px); }
            to   { opacity:1; transform:translateY(0);   }
        }

        /* ── HEADER ── */
        .site-header { background: var(--surface); border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 200; height: 64px; display: flex; align-items: center; }
        .header-container { display: flex; align-items: center; justify-content: space-between; width: 100%; }
        .header-brand { display: flex; align-items: center; gap: 10px; }
        .header-logo-mark { width: 36px; height: 36px; background: linear-gradient(135deg, var(--accent), #e09b55); border-radius: 10px; display: flex; align-items: center; justify-content: center; overflow: hidden; box-shadow: 0 4px 12px rgba(193,127,62,0.3); flex-shrink: 0; }
        .header-logo-mark img { width: 100%; height: 100%; object-fit: cover; }
        .header-brand-text { font-size: 15px; font-weight: 800; letter-spacing: -0.01em; color: var(--text); }
        .header-nav { display: flex; align-items: center; gap: 4px; }
        .header-nav a { display: flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 9px; font-size: 13px; font-weight: 600; color: var(--muted); transition: all 0.15s; }
        .header-nav a:hover { background: var(--surface2); color: var(--text); }
        .header-nav a.active { background: var(--accent-bg); color: var(--accent); }
        .hamburger-menu { display: none; flex-direction: column; gap: 5px; padding: 8px; background: none; border: 1px solid var(--border); border-radius: 9px; cursor: pointer; width: 38px; height: 38px; align-items: center; justify-content: center; }
        .hamburger-menu span { display: block; width: 18px; height: 2px; background: var(--muted); border-radius: 2px; transition: all 0.3s; }

        /* ── SIDE MENU ── */
        .side-menu { position: fixed; top: 0; right: -280px; width: 260px; height: 100%; background: var(--surface); border-left: 1px solid var(--border); z-index: 300; padding: 80px 16px 24px; transition: right 0.3s cubic-bezier(.4,0,.2,1); box-shadow: var(--shadow-xl); }
        .side-menu.is-active { right: 0; }
        .side-menu ul { list-style: none; }
        .side-menu ul li a { display: flex; align-items: center; padding: 11px 14px; border-radius: 10px; font-size: 14px; font-weight: 600; color: var(--muted); transition: all 0.15s; margin-bottom: 2px; }
        .side-menu ul li a:hover { background: var(--surface2); color: var(--text); }
        .menu-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.25); backdrop-filter: blur(3px); z-index: 299; display: none; }
        .menu-overlay.is-active { display: block; }

        /* ── ANNOUNCEMENT ── */
        .announcement-bar { background: var(--surface); border-bottom: 1px solid var(--border2); padding: 10px 0; overflow: hidden; }
        .announcement-inner { display: flex; align-items: center; gap: 10px; }
        .announcement-label { background: var(--accent); color: white; font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; padding: 4px 10px; border-radius: 99px; white-space: nowrap; flex-shrink: 0; }
        .announcement-text { font-size: 13px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .announcement-text strong { color: var(--text); }

        .page-content { padding: 28px 0 60px; }

        /* ── BANNER ── */
        .banner-wrap { margin-bottom: 32px; }
        .banner-slider { border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-lg); }
        .banner-slider img { width: 100%; height: auto; object-fit: cover; }
        .swiper-button-next, .swiper-button-prev { display: none !important; }
        .swiper-pagination-bullet { background: var(--muted2) !important; }
        .swiper-pagination-bullet-active { background: var(--accent) !important; }

        /* ── SECTION ── */
        .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; gap: 12px; }
        .section-title { font-size: clamp(18px, 2.5vw, 22px); font-weight: 800; letter-spacing: -0.02em; color: var(--text); }
        .section-title span { color: var(--accent); }
        .section-badge { background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-border); font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 99px; }
        .section-block { margin-bottom: 40px; }

        /* ── PRODUCT GRID ── */
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
        .product-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s; opacity: 0; transform: translateY(12px); cursor: pointer; }
        .product-card.is-visible { opacity: 1; transform: translateY(0); transition: opacity 0.4s ease, transform 0.4s ease, box-shadow 0.2s, border-color 0.2s; }
        .product-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-3px); border-color: var(--accent-border); }
        .product-img-wrap { position: relative; aspect-ratio: 16/9; overflow: hidden; background: var(--surface2); }
        .product-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; }
        .product-card:hover .product-img-wrap img { transform: scale(1.06); }
        .discount-ribbon { position: absolute; top: 10px; right: 10px; background: var(--danger); color: white; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 99px; font-family: var(--mono); }
        .card-body { padding: 14px 16px 16px; }
        .card-category { display: inline-block; background: var(--accent-bg); color: var(--accent); border: 1px solid var(--accent-border); font-size: 10px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; padding: 3px 9px; border-radius: 99px; margin-bottom: 8px; }
        .card-name { font-size: 14px; font-weight: 700; color: var(--text); line-height: 1.35; margin-bottom: 10px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .card-price-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 8px; }
        .price-final { font-family: var(--mono); font-size: 15px; font-weight: 700; color: var(--success); }
        .price-normal { font-family: var(--mono); font-size: 15px; font-weight: 700; color: var(--text); }
        .price-original { font-family: var(--mono); font-size: 12px; color: var(--muted2); text-decoration: line-through; }
        .card-sold { display: flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600; color: var(--muted); }
        .card-sold .fire-icon { color: var(--danger); }

        /* ── STOCK BADGE ── */
        .card-stock { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 99px; margin-top: 6px; }
        .card-stock.in-stock  { background: var(--success-bg); color: var(--success); }
        .card-stock.low-stock { background: var(--warn-bg);    color: var(--warn);    }
        .card-stock.out-stock { background: var(--danger-bg);  color: var(--danger);  }

        /* ── SEARCH ── */
        .search-wrap { margin-bottom: 20px; }
        .search-form { display: flex; background: var(--surface); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow); max-width: 520px; transition: box-shadow 0.2s, border-color 0.2s; }
        .search-form:focus-within { border-color: var(--accent-border); box-shadow: 0 0 0 3px var(--accent-bg), var(--shadow); }
        .search-form input { flex: 1; padding: 12px 16px; border: none; background: none; font-family: var(--font); font-size: 13.5px; color: var(--text); outline: none; }
        .search-form input::placeholder { color: var(--muted2); }
        .search-form button { padding: 0 20px; background: var(--accent); border: none; color: white; cursor: pointer; font-size: 14px; transition: background 0.15s; }
        .search-form button:hover { background: var(--accent-hover); }

        /* ── CATEGORY FILTER ── */
        .cat-filter-wrap { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 14px 18px; margin-bottom: 20px; box-shadow: var(--shadow); }
        .cat-filter-label { font-size: 10px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--muted2); margin-bottom: 10px; }
        .cat-scroll-wrapper { position: relative; padding: 0 36px; }
        .cat-scroll-wrapper::before, .cat-scroll-wrapper::after { content: ''; position: absolute; top: 0; bottom: 0; width: 48px; pointer-events: none; z-index: 1; }
        .cat-scroll-wrapper::before { left: 36px; background: linear-gradient(to right, var(--surface), transparent); }
        .cat-scroll-wrapper::after  { right: 36px; background: linear-gradient(to left, var(--surface), transparent); }
        .cat-scroll { display: flex; gap: 8px; overflow-x: auto; padding-bottom: 2px; scrollbar-width: none; cursor: grab; user-select: none; -webkit-user-select: none; }
        .cat-scroll.is-dragging { cursor: grabbing; }
        .cat-scroll::-webkit-scrollbar { display: none; }
        .cat-arrow { position: absolute; top: 50%; transform: translateY(-50%); z-index: 2; width: 32px; height: 32px; border-radius: 6px; background: #1a6fb5; border: none; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; transition: background 0.15s; opacity: 0; pointer-events: none; }
        .cat-arrow:hover { background: #155d9e; }
        .cat-arrow.left  { left: 0; }
        .cat-arrow.right { right: 0; }
        @media (hover: hover) { .cat-arrow { opacity: 1; pointer-events: auto; } }
        @media (max-width: 768px) { .cat-arrow { display: none !important; } .cat-scroll-wrapper { padding: 0; } .cat-scroll-wrapper::before { left: 0; } .cat-scroll-wrapper::after { right: 0; } }
        .cat-arrow.hidden { opacity: 0 !important; pointer-events: none !important; }
        .cat-btn { display: inline-flex; align-items: center; gap: 5px; padding: 7px 16px; border-radius: 99px; border: 1px solid var(--border); background: var(--bg); color: var(--muted); font-size: 12.5px; font-weight: 600; white-space: nowrap; transition: all 0.15s; flex-shrink: 0; }
        .cat-btn:hover { border-color: var(--accent-border); color: var(--accent); background: var(--accent-bg); }
        .cat-btn.active { background: var(--accent); border-color: var(--accent); color: white; box-shadow: 0 3px 10px rgba(193,127,62,0.25); }
        .cat-btn.attic-btn { background: var(--bg); color: var(--muted); }
        .cat-btn.attic-btn:hover { border-color: var(--accent-border); color: var(--accent); background: var(--accent-bg); }

        /* ── STAT CARDS ── */
        .stat-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 32px; }
        .stat-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 22px; display: flex; align-items: flex-start; gap: 14px; box-shadow: var(--shadow); transition: box-shadow 0.2s, transform 0.2s; }
        .stat-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); }
        .stat-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .stat-icon.amber { background: var(--warn-bg); color: var(--warn); }
        .stat-icon.green { background: var(--success-bg); color: var(--success); }
        .stat-icon.blue  { background: var(--info-bg); color: var(--info); }
        .stat-content h3 { font-size: 16px; font-weight: 800; color: var(--text); margin-bottom: 4px; }
        .stat-content p  { font-size: 13px; color: var(--muted); line-height: 1.5; }

        /* ── STEPS ── */
        .steps-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        .step-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 22px 18px; text-align: center; box-shadow: var(--shadow); transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s; position: relative; }
        .step-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); border-color: var(--accent-border); }
        .step-num { position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background: var(--accent); color: white; font-size: 11px; font-weight: 800; font-family: var(--mono); width: 24px; height: 24px; border-radius: 99px; display: flex; align-items: center; justify-content: center; }
        .step-icon { width: 52px; height: 52px; border-radius: 14px; background: var(--accent-bg); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 20px; margin: 0 auto 14px; }
        .step-card h4 { font-size: 14px; font-weight: 700; margin-bottom: 8px; color: var(--text); }
        .step-card p  { font-size: 12.5px; color: var(--muted); line-height: 1.55; }

        /* ── TESTIMONI ── */
        .testimoni-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 22px; box-shadow: var(--shadow); height: 100%; transition: box-shadow 0.2s, border-color 0.2s; }
        .testimoni-card:hover { box-shadow: var(--shadow-lg); border-color: var(--accent-border); }
        .testimoni-stars { display: flex; gap: 3px; margin-bottom: 12px; color: var(--warn); font-size: 14px; }
        .testimoni-text { font-size: 13.5px; color: var(--muted); line-height: 1.7; margin-bottom: 16px; font-style: italic; }
        .testimoni-author { font-size: 13px; font-weight: 700; color: var(--text); }

        /* ── SERVICES ── */
        .service-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; display: flex; align-items: center; gap: 14px; box-shadow: var(--shadow); transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s; height: 100%; }
        .service-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-2px); border-color: var(--accent-border); }
        .service-img { width: 56px; height: 56px; border-radius: 12px; object-fit: cover; border: 1px solid var(--border); flex-shrink: 0; }
        .service-info h3 { font-size: 13.5px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
        .service-info p  { font-size: 12px; color: var(--accent); font-weight: 600; }

        /* ── BUTTONS ── */
        .btn-outline { display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px; border-radius: 10px; border: 1px solid var(--border); background: var(--surface); color: var(--muted); font-size: 13px; font-weight: 600; transition: all 0.15s; font-family: var(--font); cursor: pointer; }
        .btn-outline:hover { border-color: var(--accent-border); color: var(--accent); background: var(--accent-bg); }

        /* ── FOOTER ── */
        .site-footer { background: var(--text); color: rgba(255,255,255,0.9); }
        .footer-top { padding: 48px 0 36px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 48px; }
        .footer-brand-img { width: 44px; height: 44px; border-radius: 12px; object-fit: cover; margin-bottom: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
        .footer-brand-name { font-size: 16px; font-weight: 800; color: white; margin-bottom: 8px; }
        .footer-brand-desc { font-size: 13px; color: rgba(255,255,255,0.55); line-height: 1.7; margin-bottom: 20px; }
        .footer-socials { display: flex; gap: 10px; flex-wrap: wrap; }
        .social-btn { display: inline-flex; align-items: center; gap: 7px; padding: 9px 16px; border-radius: 9px; font-size: 12.5px; font-weight: 700; color: white; transition: all 0.15s; }
        .social-btn.tg { background: rgba(0,136,204,0.25); } .social-btn.tg:hover { background: #0088cc; }
        .social-btn.wa { background: rgba(37,211,102,0.2); } .social-btn.wa:hover { background: #25D366; }
        .footer-col-title { font-size: 11px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,255,255,0.4); margin-bottom: 16px; }
        .footer-links { display: flex; flex-direction: column; gap: 6px; }
        .footer-links a { font-size: 13.5px; color: rgba(255,255,255,0.6); font-weight: 500; transition: color 0.15s; padding: 4px 0; }
        .footer-links a:hover { color: white; }
        .footer-img-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
        .footer-img-grid img { width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); transition: transform 0.2s; }
        .footer-img-grid img:hover { transform: scale(1.05); }
        .footer-bottom { padding: 20px 0; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
        .footer-bottom p { font-size: 12.5px; color: rgba(255,255,255,0.4); }
        .footer-bottom-links { display: flex; gap: 20px; }
        .footer-bottom-links a { font-size: 12.5px; color: rgba(255,255,255,0.4); transition: color 0.15s; }
        .footer-bottom-links a:hover { color: rgba(255,255,255,0.8); }

        /* ── HELP BUTTON ── */
        .help-button { position: fixed; bottom: 28px; right: 28px; width: 52px; height: 52px; background: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; box-shadow: 0 6px 20px rgba(193,127,62,0.4); cursor: pointer; z-index: 999; transition: all 0.15s; }
        .help-button:hover { transform: scale(1.08); box-shadow: 0 8px 24px rgba(193,127,62,0.5); }
        .help-options { position: fixed; bottom: 92px; right: 28px; background: var(--surface); border: 1px solid var(--border); padding: 20px; border-radius: var(--radius); box-shadow: var(--shadow-xl); z-index: 998; min-width: 260px; transform: translateY(10px); opacity: 0; visibility: hidden; transition: all 0.2s ease; }
        .help-options.show { transform: translateY(0); opacity: 1; visibility: visible; }
        .help-options h4 { font-size: 14px; font-weight: 800; color: var(--text); margin-bottom: 5px; }
        .help-options p  { font-size: 12.5px; color: var(--muted); margin-bottom: 14px; }
        .help-options-btns { display: flex; flex-direction: column; gap: 8px; }
        .help-options-btns a { display: flex; align-items: center; gap: 9px; padding: 10px 14px; border-radius: 9px; font-size: 13px; font-weight: 700; color: white; transition: opacity 0.15s; }
        .help-options-btns a:hover { opacity: 0.9; }
        .help-options-btns a.tg { background: #0088cc; }
        .help-options-btns a.wa { background: #25D366; }
        .help-close { position: absolute; top: 12px; right: 14px; font-size: 18px; color: var(--muted2); cursor: pointer; line-height: 1; transition: color 0.15s; }
        .help-close:hover { color: var(--text); }

        /* ── PURCHASE NOTIFICATION ── */
        .purchase-notification { position: fixed; bottom: 24px; left: 24px; max-width: 320px; background: var(--surface); border: 1px solid var(--border); padding: 14px 16px; border-radius: var(--radius); box-shadow: var(--shadow-xl); display: flex; align-items: center; gap: 12px; transform: translateX(-120%); transition: transform 0.45s cubic-bezier(0.68, -0.55, 0.265, 1.55); z-index: 997; border-left: 3px solid var(--accent); }
        .purchase-notification.show { transform: translateX(0); }
        .notification-img { width: 48px; height: 48px; border-radius: 9px; object-fit: cover; border: 1px solid var(--border); flex-shrink: 0; }
        .notification-content { flex: 1; min-width: 0; }
        .notification-text { font-size: 13px; color: var(--text); line-height: 1.4; margin-bottom: 3px; }
        .notification-time { font-size: 11px; color: var(--muted2); }
        .notification-close { font-size: 18px; color: var(--muted2); cursor: pointer; line-height: 1; flex-shrink: 0; transition: color 0.15s; }
        .notification-close:hover { color: var(--text); }

        /* ── POPUP ── */
        .popup-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); display: flex; justify-content: center; align-items: center; z-index: 500; opacity: 0; visibility: hidden; transition: all 0.25s; }
        .popup-overlay.show { opacity: 1; visibility: visible; }
        .popup-box { background: var(--surface); border-radius: var(--radius-lg); width: 90%; max-width: 420px; overflow: hidden; box-shadow: var(--shadow-xl); transform: scale(0.95); transition: transform 0.25s; }
        .popup-overlay.show .popup-box { transform: scale(1); }
        .popup-head { background: var(--accent); color: white; padding: 16px 20px; font-size: 13px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; text-align: center; }
        .popup-img img { width: 100%; display: block; }
        .popup-foot { padding: 20px; background: var(--surface2); }
        .popup-foot marquee { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 14px; display: block; }
        .popup-btn-join { display: block; width: 100%; padding: 12px; border-radius: 10px; background: black; color: white; text-align: center; font-weight: 700; font-size: 13.5px; margin-bottom: 14px; transition: opacity 0.15s; }
        .popup-btn-join:hover { background: gray; }
        .popup-foot-controls { display: flex; justify-content: space-between; align-items: center; }
        .popup-hide { display: flex; align-items: center; gap: 7px; font-size: 13px; color: var(--muted); cursor: pointer; }
        .popup-hide input[type="checkbox"] { appearance: none; width: 16px; height: 16px; border: 2px solid var(--border); border-radius: 4px; background: var(--surface); cursor: pointer; position: relative; transition: all 0.15s; }
        .popup-hide input[type="checkbox"]:checked { background: var(--accent); border-color: var(--accent); }
        .popup-hide input[type="checkbox"]:checked::after { content: ''; position: absolute; top: 0px; left: 3px; width: 5px; height: 8px; border: 2px solid white; border-top: none; border-left: none; transform: rotate(45deg); }
        .popup-close-btn { padding: 8px 18px; background: var(--bg); border: 1px solid var(--border); border-radius: 8px; font-size: 13px; font-weight: 700; color: var(--muted); cursor: pointer; transition: all 0.15s; font-family: var(--font); }
        .popup-close-btn:hover { background: var(--surface2); color: var(--text); }

        /* ── PARTICLES ── */
        #particles-container { position: fixed; width: 100%; height: 100%; top: 0; left: 0; z-index: -1; pointer-events: none; }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
        .animate-up { animation: fadeUp 0.45s ease both; }
        .delay-1 { animation-delay: 0.05s; } .delay-2 { animation-delay: 0.1s; }
        .delay-3 { animation-delay: 0.15s; } .delay-4 { animation-delay: 0.2s; }

        /* ── EMPTY STATE ── */
        .empty-state { grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: var(--muted); background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
        .empty-state i { font-size: 36px; opacity: 0.2; display: block; margin-bottom: 10px; }
        .empty-state p { font-size: 14px; }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            .steps-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .header-nav { display: none; }
            .hamburger-menu { display: flex; }
            .stat-row { grid-template-columns: 1fr; gap: 12px; }
            .footer-grid { grid-template-columns: 1fr; gap: 28px; }
            .steps-grid { grid-template-columns: repeat(2, 1fr); }
            .product-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .page-content { padding: 20px 0 48px; }
            .purchase-notification { max-width: 260px; bottom: 14px; left: 14px; }
            .help-button { bottom: 20px; right: 20px; width: 46px; height: 46px; font-size: 18px; }
            .help-options { right: 14px; bottom: 78px; min-width: auto; left: 14px; }
        }
        @media (max-width: 480px) {
            .steps-grid { grid-template-columns: 1fr 1fr; }
            .product-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .footer-bottom { flex-direction: column; gap: 8px; text-align: center; }
            .footer-bottom-links { justify-content: center; }
        }
    </style>
</head>
<body>

<!-- ══════════════════════════════════════════
     LOADER — letakkan tepat setelah <body>
══════════════════════════════════════════ -->
<div id="site-loader">
    <div class="loader-corner tl"></div>
    <div class="loader-corner tr"></div>
    <div class="loader-corner bl"></div>
    <div class="loader-corner br"></div>

    <div class="loader-logo">
        <img src="assets/images/logo.jpg" alt="Logo">
    </div>

    <div class="loader-brand" id="loaderBrand"></div>

    <div class="loader-underline"></div>

    <div class="loader-tagline">Tempatnya Dunia Digital</div>

    <div class="loader-bar-wrap">
        <div class="loader-bar"></div>
    </div>

    <div class="loader-dots">
        <span></span><span></span><span></span>
    </div>
</div>

<div id="particles-container"></div>

<!-- Header -->
<header class="site-header">
    <div class="container header-container">
        <div class="header-brand">
            <div class="header-logo-mark"><img src="assets/images/logo.jpg" alt="Logo"></div>
            <span class="header-brand-text">WARUNGERIK STORE</span>
        </div>
        <nav class="header-nav">
            <a href="index.php" class="active"><i class="fas fa-home"></i> Beranda</a>
            <a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a>
            <a href="testimoni.php"><i class="fas fa-comment-dots"></i> Testimoni</a>
            <a href="cek_pesanan.php"><i class="fas fa-search"></i> Cek Pesanan</a>
            <a href="request_produk.php"><i class="fas fa-inbox"></i> Request</a>
        </nav>
        <button class="hamburger-menu" id="hamburger-toggle" aria-label="Buka Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- Side Menu Mobile -->
<nav class="side-menu" id="side-menu">
    <ul>
        <li><a href="index.php"><i class="fas fa-home" style="width:18px;margin-right:8px;color:var(--accent)"></i> Beranda</a></li>
        <li><a href="tentang.php"><i class="fas fa-info-circle" style="width:18px;margin-right:8px;color:var(--accent)"></i> Tentang</a></li>
        <li><a href="testimoni.php"><i class="fas fa-comment-dots" style="width:18px;margin-right:8px;color:var(--accent)"></i> Testimoni</a></li>
        <li><a href="cek_pesanan.php"><i class="fas fa-search" style="width:18px;margin-right:8px;color:var(--accent)"></i> Cek Pesanan</a></li>
        <li><a href="request_produk.php"><i class="fas fa-inbox" style="width:18px;margin-right:8px;color:var(--accent)"></i> Request Produk</a></li>
    </ul>
</nav>
<div class="menu-overlay" id="menu-overlay"></div>

<!-- Announcement -->
<div class="announcement-bar">
    <div class="container">
        <div class="announcement-inner">
            <span class="announcement-label">INFO</span>
            <p class="announcement-text">Selamat Datang di <span style="color: var(--accent);">WARUNGERIKSTORE</span></p>
        </div>
    </div>
</div>

<!-- Main Content -->
<main class="page-content">
    <div class="container">

        <!-- Banner Slider -->
        <div class="banner-wrap animate-up">
            <div class="swiper banner-slider">
                <div class="swiper-wrapper">
                    <div class="swiper-slide"><img src="assets/images/banner.png" alt="Promo"></div>
                    <div class="swiper-slide"><img src="assets/images/banner.jpg" alt="Promo"></div>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>

        <!-- Promo Section -->
        <div class="section-block animate-up delay-1">
            <div class="section-header">
                <h2 class="section-title">Promo <span>Spesial</span> Untukmu</h2>
                <span class="section-badge"><i class="fas fa-fire" style="margin-right:4px;"></i>Hot Deal</span>
            </div>
            <div class="product-grid">
                <?php
                $query_diskon = "SELECT produk.*, kategori.nama_kategori FROM produk 
                                 LEFT JOIN kategori ON produk.kategori_id = kategori.id 
                                 WHERE diskon_persen > 0 ORDER BY diskon_persen DESC LIMIT 30";
                $result_diskon = mysqli_query($koneksi, $query_diskon);
                if (mysqli_num_rows($result_diskon) > 0):
                    while ($row = mysqli_fetch_assoc($result_diskon)):
                        $nama = htmlspecialchars($row['nama_produk']);
                        $harga_asli = $row['harga'];
                        $diskon = $row['diskon_persen'];
                        $harga_final = $harga_asli - ($harga_asli * $diskon / 100);
                ?>
                <a href="bayar.php?id=<?= $row['id'] ?>">
                    <div class="product-card">
                        <div class="product-img-wrap">
                            <img src="assets/images/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= $nama ?>">
                            <span class="discount-ribbon">-<?= $diskon ?>%</span>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($row['nama_kategori'])): ?>
                                <span class="card-category"><?= htmlspecialchars($row['nama_kategori']) ?></span>
                            <?php endif; ?>
                            <h3 class="card-name"><?= $nama ?></h3>
                            <div class="card-price-row">
                                <span class="price-final">Rp <?= number_format($harga_final, 0, ',', '.') ?></span>
                                <span class="price-original">Rp <?= number_format($harga_asli, 0, ',', '.') ?></span>
                            </div>
                            <?php if (!empty($row['jumlah_terjual']) && $row['jumlah_terjual'] > 0): ?>
                            <div class="card-sold"><i class="fas fa-fire fire-icon"></i> <?= number_format($row['jumlah_terjual']) ?> Terjual</div>
                            <?php endif; ?>
                            <?= stok_badge($row) ?>
                        </div>
                    </div>
                </a>
                <?php endwhile; else: ?>
                    <p class="empty-state"><i class="fas fa-tag"></i>Saat ini belum ada produk promo.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- All Products -->
        <?php
        $kategori_filter_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : null;
        $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
        $judul_produk = "Semua Produk";
        $query_produk = "SELECT produk.*, kategori.nama_kategori FROM produk LEFT JOIN kategori ON produk.kategori_id = kategori.id";
        $conditions = []; $params = []; $types = "";
        if (!empty($search_term)) {
            $conditions[] = "nama_produk LIKE ?";
            $params[] = "%" . $search_term . "%"; $types .= "s";
            $judul_produk = "Hasil Pencarian: '" . htmlspecialchars($search_term) . "'";
        }
        if ($kategori_filter_id) {
            $conditions[] = "produk.kategori_id = ?";
            $params[] = $kategori_filter_id; $types .= "i";
            if (empty($search_term)) {
                $stmt_kat = $koneksi->prepare("SELECT nama_kategori FROM kategori WHERE id = ?");
                $stmt_kat->bind_param("i", $kategori_filter_id); $stmt_kat->execute();
                if ($row_kat = $stmt_kat->get_result()->fetch_assoc()) $judul_produk = htmlspecialchars($row_kat['nama_kategori']);
            }
        }
        $query_produk .= !empty($conditions) ? " WHERE " . implode(" AND ", $conditions) . " ORDER BY nama_produk ASC" : " ORDER BY RAND()";
        $stmt_produk = $koneksi->prepare($query_produk);
        if (!empty($params)) $stmt_produk->bind_param($types, ...$params);
        $stmt_produk->execute();
        $result_semua_produk = $stmt_produk->get_result();
        ?>

        <div class="section-block animate-up delay-2">
            <div class="search-wrap">
                <form action="index.php" method="GET" class="search-form">
                    <input type="search" name="search" placeholder="Cari produk apa saja..."
                           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" aria-label="Cari Produk">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <!-- Category Filter -->
            <div class="cat-filter-wrap">
                <div class="cat-filter-label">Filter Kategori</div>
                <div class="cat-scroll-wrapper">
                    <button class="cat-arrow left hidden" id="catArrowLeft" aria-label="Geser kiri">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="cat-scroll" id="cat-scroll">
                        <a href="index.php" class="cat-btn <?= !$kategori_filter_id ? 'active' : '' ?>">
                            <i class="fas fa-th"></i> Semua
                        </a>
                        <a href="https://warungerik.com/attic" target="_blank" class="cat-btn attic-btn">
                            ATTIC MLBB
                        </a>
                        <?php
                        $kategori_list = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                        while ($kat = mysqli_fetch_assoc($kategori_list)):
                        ?>
                        <a href="index.php?kategori=<?= $kat['id'] ?>"
                           class="cat-btn <?= ($kategori_filter_id == $kat['id']) ? 'active' : '' ?>">
                            <?= htmlspecialchars($kat['nama_kategori']) ?>
                        </a>
                        <?php endwhile; ?>
                    </div>
                    <button class="cat-arrow right" id="catArrowRight" aria-label="Geser kanan">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <div class="section-header">
                <h2 class="section-title" id="product-list-title"><?= $judul_produk ?></h2>
            </div>

            <div id="product-grid-container" class="product-grid">
                <?php if ($result_semua_produk->num_rows > 0):
                    while ($row = $result_semua_produk->fetch_assoc()):
                        $nama = htmlspecialchars($row['nama_produk']);
                        $harga_asli = $row['harga'];
                        $diskon = $row['diskon_persen'];
                        $punya_diskon = $diskon > 0;
                        $harga_final = $punya_diskon ? $harga_asli - ($harga_asli * $diskon / 100) : $harga_asli;
                ?>
                <a href="bayar.php?id=<?= $row['id'] ?>">
                    <div class="product-card">
                        <div class="product-img-wrap">
                            <img src="assets/images/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= $nama ?>">
                            <?php if ($punya_diskon): ?><span class="discount-ribbon">-<?= $diskon ?>%</span><?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($row['nama_kategori'])): ?>
                                <span class="card-category"><?= htmlspecialchars($row['nama_kategori']) ?></span>
                            <?php endif; ?>
                            <h3 class="card-name"><?= $nama ?></h3>
                            <div class="card-price-row">
                                <?php if ($punya_diskon): ?>
                                    <span class="price-final">Rp <?= number_format($harga_final, 0, ',', '.') ?></span>
                                    <span class="price-original">Rp <?= number_format($harga_asli, 0, ',', '.') ?></span>
                                <?php else: ?>
                                    <span class="price-normal">Rp <?= number_format($harga_asli, 0, ',', '.') ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($row['jumlah_terjual']) && $row['jumlah_terjual'] > 0): ?>
                            <div class="card-sold"><i class="fas fa-fire fire-icon"></i> <?= number_format($row['jumlah_terjual']) ?> Terjual</div>
                            <?php endif; ?>
                            <?= stok_badge($row) ?>
                        </div>
                    </div>
                </a>
                <?php endwhile; else: ?>
                    <div class="empty-state"><i class="fas fa-box-open"></i><p>Tidak ada produk yang ditemukan.</p></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Testimonials -->
        <?php
        $query_testi = "SELECT * FROM testimoni WHERE status = 'approved' ORDER BY tanggal_submit DESC LIMIT 10";
        $result_testi = mysqli_query($koneksi, $query_testi);
        if ($result_testi && mysqli_num_rows($result_testi) > 0):
        ?>
        <div class="section-block animate-up delay-3">
            <div class="section-header">
                <h2 class="section-title">Apa Kata <span>Mereka?</span></h2>
                <a href="testimoni.php" class="btn-outline">Lihat Semua <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="swiper testimoni-swiper">
                <div class="swiper-wrapper">
                    <?php while ($row = mysqli_fetch_assoc($result_testi)): ?>
                    <div class="swiper-slide" style="height: auto;">
                        <div class="testimoni-card">
                            <div class="testimoni-stars">
                                <?php for ($i = 0; $i < 5; $i++):
                                    echo $i < $row['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                endfor; ?>
                            </div>
                            <p class="testimoni-text">"<?= htmlspecialchars($row['testimoni']) ?>"</p>
                            <div class="testimoni-author">— <?= htmlspecialchars($row['nama']) ?></div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <div class="swiper-pagination" style="position:relative;margin-top:16px;"></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Why Us -->
        <div class="section-block animate-up delay-4">
            <div class="section-header">
                <h2 class="section-title">Kenapa <span>Warungerik</span>?</h2>
            </div>
            <div class="stat-row">
                <div class="stat-card">
                    <div class="stat-icon amber"><i class="fas fa-tags"></i></div>
                    <div class="stat-content"><h3>Harga Termurah</h3><p>Berbagai pilihan voucher game dengan harga terbaik dan kompetitif.</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-bolt"></i></div>
                    <div class="stat-content"><h3>Proses Instan 24 Jam</h3><p>Pembelian cepat, mudah, aman, dan dapat dipercaya kapan saja.</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-credit-card"></i></div>
                    <div class="stat-content"><h3>Pembayaran Lengkap</h3><p>Tersedia berbagai metode pembayaran tanpa perlu khawatir.</p></div>
                </div>
            </div>
        </div>

        <!-- How to Order -->
        <div class="section-block">
            <div class="section-header">
                <h2 class="section-title">Cara Mudah <span>Memesan</span></h2>
            </div>
            <div class="steps-grid">
                <div class="step-card">
                    <span class="step-num">1</span>
                    <div class="step-icon"><i class="fas fa-cart-shopping"></i></div>
                    <h4>Pilih Produk</h4>
                    <p>Pilih produk dari daftar lalu klik untuk melanjutkan ke halaman pembelian.</p>
                </div>
                <div class="step-card">
                    <span class="step-num">2</span>
                    <div class="step-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                    <h4>Isi Data & Bayar</h4>
                    <p>Isi data dan pilih metode pembayaran yang paling nyaman bagimu.</p>
                </div>
                <div class="step-card">
                    <span class="step-num">3</span>
                    <div class="step-icon"><i class="fas fa-circle-check"></i></div>
                    <h4>Pembayaran Berhasil</h4>
                    <p>Setelah bayar, kamu akan mendapat konfirmasi dan detail pesanan.</p>
                </div>
                <div class="step-card">
                    <span class="step-num">4</span>
                    <div class="step-icon"><i class="fas fa-truck-fast"></i></div>
                    <h4>Produk Dikirim</h4>
                    <p>Kami langsung memproses dan mengirimkan produk sesuai data kamu.</p>
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
                    <p class="footer-brand-desc">Panel penyedia layanan topup games terbaik #1 Indonesia, dengan harga termurah dan proses super instan.</p>
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
                <a href="testimoni.php">Testimoni</a>
                <a href="tentang.php">Tentang</a>
                <a href="legal.php">Kebijakan</a>
                <a href="cek_pesanan.php">Cek Pesanan</a>
            </div>
        </div>
    </div>
</footer>

<!-- Popup -->
<div id="info-popup" class="popup-overlay">
    <div class="popup-box">
        <div class="popup-head">Informasi</div>
        <div class="popup-img"><img src="assets/images/chwa.jpg" alt="Info"></div>
        <div class="popup-foot">
            <marquee behavior="scroll" direction="left">INFORMASI SEPUTAR PRODUK DAN PROMO HANYA DI SALURAN RESMI KAMI.</marquee>
            <a href="cek_pesanan.php" target="_blank" class="popup-btn-join">
                <i class="fa-brands fa-square-whatsapp" style="margin-right:7px;"></i> Join Sekarang
            </a>
            <div class="popup-foot-controls">
                <label class="popup-hide">
                    <input type="checkbox" id="hide-popup-check"> Sembunyikan
                </label>
                <button id="close-popup-btn" class="popup-close-btn">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Purchase Notification -->
<div id="purchase-notification" class="purchase-notification">
    <img id="notification-img" src="" alt="Produk" class="notification-img">
    <div class="notification-content">
        <p id="notification-text" class="notification-text"></p>
        <small id="notification-time" class="notification-time"></small>
    </div>
    <span class="notification-close">&times;</span>
</div>

<!-- Help Button -->
<a href="#" class="help-button" id="helpBtn" aria-label="Bantuan"><i class="fas fa-question"></i></a>
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
/* ══════════════════════════════════════════
   LOADER SCRIPT — jalan sebelum DOM ready
══════════════════════════════════════════ */
(function () {
    const brand  = document.getElementById('loaderBrand');
    const loader = document.getElementById('site-loader');
    if (!brand || !loader) return;

    const word    = 'WARUNGERIK';
    const accents = new Set([0, 9]); // W dan K = warna emas

    word.split('').forEach((ch, i) => {
        const span = document.createElement('span');
        span.className = 'l-char' + (accents.has(i) ? ' accent' : '');
        span.textContent = ch;
        span.style.animationDelay = (0.28 + i * 0.075) + 's';
        brand.appendChild(span);
    });

    /* Sembunyikan loader setelah ~2.7 detik */
    setTimeout(() => {
        loader.classList.add('hidden');
        setTimeout(() => loader.remove(), 950);
    }, 4000);
})();

/* ══════════════════════════════════════════
   MAIN SCRIPTS
══════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {

    // — Swiper: Banner —
    new Swiper('.banner-slider', {
        loop: true,
        autoplay: { delay: 3500, disableOnInteraction: false },
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
    });

    // — Swiper: Testimoni —
    new Swiper('.testimoni-swiper', {
        loop: true, autoplay: { delay: 4000, disableOnInteraction: false },
        slidesPerView: 1, spaceBetween: 16,
        pagination: { el: '.testimoni-swiper .swiper-pagination', clickable: true },
        breakpoints: { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
    });

    // — Swiper: Services —
    new Swiper('.service-swiper', {
        loop: true, autoplay: { delay: 3000, disableOnInteraction: false },
        pagination: { el: '.service-swiper .swiper-pagination', clickable: true },
        slidesPerView: 1, spaceBetween: 16,
        breakpoints: { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
    });

    // — Particles —
    if (typeof tsParticles !== 'undefined') {
        tsParticles.load('particles-container', {
            particles: {
                number: { value: 50, density: { enable: true, value_area: 900 } },
                color: { value: "#c17f3e" }, shape: { type: "circle" },
                opacity: { value: 0.15, random: true }, size: { value: 2.5, random: true },
                links: { enable: true, distance: 130, color: "#c17f3e", opacity: 0.08, width: 1 },
                move: { enable: true, speed: 1.2, outModes: { default: "bounce" } }
            },
            interactivity: {
                events: { onHover: { enable: true, mode: "grab" }, resize: true },
                modes: { grab: { distance: 120, links: { opacity: 0.2 } } }
            }
        });
    }

    // — Mobile Menu —
    const hamburger = document.getElementById('hamburger-toggle');
    const sideMenu  = document.getElementById('side-menu');
    const overlay   = document.getElementById('menu-overlay');
    function toggleMenu() {
        hamburger.classList.toggle('is-active');
        sideMenu.classList.toggle('is-active');
        overlay.classList.toggle('is-active');
    }
    hamburger?.addEventListener('click', toggleMenu);
    overlay?.addEventListener('click', toggleMenu);

    // — Help Button —
    const helpBtn      = document.getElementById('helpBtn');
    const helpOptions  = document.getElementById('helpOptions');
    const closeHelpBtn = document.getElementById('closeHelpBtn');
    helpBtn?.addEventListener('click', e => { e.preventDefault(); helpOptions.classList.toggle('show'); });
    closeHelpBtn?.addEventListener('click', () => helpOptions.classList.remove('show'));
    window.addEventListener('click', e => {
        if (!helpBtn?.contains(e.target) && !helpOptions?.contains(e.target)) helpOptions?.classList.remove('show');
    });

    // — Card Observer —
    function initCardObserver() {
        const cards = document.querySelectorAll('.product-grid > a .product-card, .product-grid > .product-card');
        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    entry.target.style.transitionDelay = (i % 4 * 60) + 'ms';
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08 });
        cards.forEach(c => observer.observe(c));
    }
    initCardObserver();

    // — Category scroll drag —
    const catScroll = document.getElementById('cat-scroll');
    let isDown = false, startX, scrollLeft;
    catScroll?.addEventListener('mousedown', e => {
        isDown = true; catScroll.classList.add('is-dragging');
        startX = e.pageX - catScroll.offsetLeft; scrollLeft = catScroll.scrollLeft;
    });
    catScroll?.addEventListener('mouseleave', () => { isDown = false; catScroll.classList.remove('is-dragging'); });
    catScroll?.addEventListener('mouseup',    () => { isDown = false; catScroll.classList.remove('is-dragging'); });
    catScroll?.addEventListener('mousemove',  e => {
        if (!isDown) return; e.preventDefault();
        catScroll.scrollLeft = scrollLeft - (e.pageX - catScroll.offsetLeft - startX);
        updateArrows();
    });

    // — Category arrows —
    const arrowLeft  = document.getElementById('catArrowLeft');
    const arrowRight = document.getElementById('catArrowRight');
    function updateArrows() {
        if (!catScroll || !arrowLeft || !arrowRight) return;
        arrowLeft.classList.toggle('hidden', catScroll.scrollLeft <= 4);
        arrowRight.classList.toggle('hidden', catScroll.scrollLeft + catScroll.clientWidth >= catScroll.scrollWidth - 4);
    }
    arrowLeft?.addEventListener('click',  () => { catScroll.scrollBy({ left: -220, behavior: 'smooth' }); setTimeout(updateArrows, 320); });
    arrowRight?.addEventListener('click', () => { catScroll.scrollBy({ left:  220, behavior: 'smooth' }); setTimeout(updateArrows, 320); });
    catScroll?.addEventListener('scroll', updateArrows);
    setTimeout(updateArrows, 100);

    // — Search AJAX —
    const searchForm   = document.querySelector('.search-form');
    const productGrid  = document.getElementById('product-grid-container');
    const productTitle = document.getElementById('product-list-title');
    const categoryBtns = document.querySelectorAll('.cat-btn:not(.attic-btn)');
    searchForm?.addEventListener('submit', e => {
        e.preventDefault();
        const term = searchForm.querySelector('input[type="search"]').value.trim();
        if (!productGrid) return;
        productGrid.style.opacity = '0.4';
        fetch(`get_products.php?search=${encodeURIComponent(term)}`)
            .then(r => r.text()).then(html => {
                productGrid.innerHTML = html; productGrid.style.opacity = '1';
                if (productTitle) productTitle.textContent = term ? `Hasil: '${term}'` : 'Semua Produk';
                window.history.pushState({}, '', `index.php?search=${encodeURIComponent(term)}`);
                categoryBtns.forEach(b => b.classList.remove('active'));
                initCardObserver();
            }).catch(() => productGrid.style.opacity = '1');
    });

    // — Category AJAX —
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            if (!productGrid) return;
            productGrid.style.opacity = '0.4';
            fetch(btn.href.replace('index.php', 'get_products.php'))
                .then(r => r.text()).then(html => {
                    productGrid.innerHTML = html; productGrid.style.opacity = '1';
                    if (productTitle) productTitle.textContent = btn.textContent.trim();
                    window.history.pushState({}, '', btn.href);
                    categoryBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    initCardObserver();
                }).catch(() => productGrid.style.opacity = '1');
        });
    });

    // — Purchase Notification —
    const notification = document.getElementById('purchase-notification');
    if (notification) {
        const notifImg   = document.getElementById('notification-img');
        const notifText  = document.getElementById('notification-text');
        const notifTime  = document.getElementById('notification-time');
        const notifClose = document.querySelector('.notification-close');
        let queue = [], idx = 0, intervalId;
        function showNext() {
            if (!queue.length) return;
            const d = queue[idx];
            notifImg.src = `assets/images/${d.gambar}`;
            notifText.innerHTML = `<strong>${d.nama_pelanggan}</strong> baru saja membeli <strong>${d.nama_produk}</strong>`;
            notifTime.textContent = "Beberapa saat yang lalu";
            notification.classList.add('show');
            setTimeout(() => notification.classList.remove('show'), 5000);
            idx = (idx + 1) % queue.length;
        }
        notifClose?.addEventListener('click', () => { notification.classList.remove('show'); clearInterval(intervalId); });
        setTimeout(() => {
            fetch('get_latest_purchase.php').then(r => r.json()).then(data => {
                if (data?.length) { queue = data; showNext(); intervalId = setInterval(showNext, 8000); }
            }).catch(() => {});
        }, 5000);
    }

    // — Info Popup —
    const popup         = document.getElementById('info-popup');
    const closePopupBtn = document.getElementById('close-popup-btn');
    const hideCheck     = document.getElementById('hide-popup-check');
    if (popup && closePopupBtn && hideCheck) {
        const hideUntil = localStorage.getItem('popupHiddenUntil');
        if (!hideUntil || Date.now() > parseInt(hideUntil)) popup.classList.add('show');
        function closePopup() {
            if (hideCheck.checked) localStorage.setItem('popupHiddenUntil', (Date.now() + 5 * 60 * 60 * 1000).toString());
            popup.classList.remove('show');
        }
        closePopupBtn.addEventListener('click', closePopup);
        popup.addEventListener('click', e => { if (e.target === popup) closePopup(); });
    }

});
</script>
</body>
</html>