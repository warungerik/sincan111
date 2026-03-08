<?php
include 'includes/koneksi.php';
include 'config_midtrans.php';

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';

if (empty($order_id)) {
    die("Error: ID Pesanan tidak valid atau tidak ditemukan.");
}

$stmt = $koneksi->prepare(
    "SELECT t.*, p.nama_produk, t.nama_pelanggan, t.wa_pelanggan, t.snap_token, k.nama_kategori
     FROM transaksi t 
     JOIN produk p ON t.produk_id = p.id 
     JOIN kategori k ON p.kategori_id = k.id 
     WHERE t.order_id = ?"
);
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$pesanan = $result->fetch_assoc();

if (!$pesanan) {
    die("Pesanan dengan ID tersebut tidak ditemukan.");
}

$status_class = $pesanan['status_pembayaran'];
if ($status_class == 'settlement' || $status_class == 'success') {
    $status_class = 'success';
} elseif ($status_class == 'pending') {
    $status_class = 'pending';
} else {
    $status_class = 'failed';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pesanan — <?php echo htmlspecialchars($order_id); ?></title>
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="<?php echo \Midtrans\Config::$clientKey; ?>"></script>

    <style>
        /* ═══════════════════════════════════════════
           DESIGN SYSTEM — matching admin panel
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
            --info:          #2563a8;
            --info-bg:       rgba(37,99,168,0.08);
            --info-border:   rgba(37,99,168,0.2);
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

        a { text-decoration: none; color: inherit; }

        /* ═══════════════════════════════════════════
           HEADER
        ═══════════════════════════════════════════ */
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

        .header-back {
            display: flex; align-items: center; gap: 7px;
            padding: 8px 16px; border-radius: 9px;
            border: 1px solid var(--border);
            font-size: 13px; font-weight: 600; color: var(--muted);
            background: var(--surface); transition: all 0.15s;
        }

        .header-back:hover { background: var(--surface2); color: var(--text); }

        /* ═══════════════════════════════════════════
           PAGE LAYOUT
        ═══════════════════════════════════════════ */
        .page-wrap {
            min-height: calc(100vh - 64px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px 60px;
        }

        .page-inner {
            width: 100%;
            max-width: 480px;
        }

        /* ═══════════════════════════════════════════
           MAIN CARD
        ═══════════════════════════════════════════ */
        .main-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            animation: fadeUp 0.45s cubic-bezier(0.16,1,0.3,1) both;
        }

        /* Status-colored top strip */
        .main-card .status-strip {
            height: 4px;
        }

        .main-card.success .status-strip { background: linear-gradient(90deg, var(--success), #4aad78); }
        .main-card.pending .status-strip { background: linear-gradient(90deg, var(--warn), #d97706); }
        .main-card.failed  .status-strip { background: linear-gradient(90deg, var(--danger), #dc2626); }

        /* ═══════════════════════════════════════════
           STATUS HERO
        ═══════════════════════════════════════════ */
        .status-hero {
            padding: 32px 32px 24px;
            text-align: center;
            border-bottom: 1px solid var(--border2);
        }

        /* Status icon */
        .status-icon-wrap {
            position: relative;
            width: 72px; height: 72px;
            margin: 0 auto 18px;
        }

        .status-pulse {
            position: absolute;
            inset: -6px;
            border-radius: 50%;
            border: 2px solid var(--s-color);
            opacity: 0.2;
            animation: pulse 2.5s ease-in-out infinite;
        }

        .status-icon {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: var(--s-bg);
            border: 1.5px solid var(--s-border);
            display: flex; align-items: center; justify-content: center;
            font-size: 28px;
            color: var(--s-color);
            position: relative;
            animation: iconIn 0.5s cubic-bezier(0.34,1.56,0.64,1) 0.2s both;
        }

        /* Per-status variables */
        .main-card.success { --s-color: var(--success); --s-bg: var(--success-bg); --s-border: var(--success-border); }
        .main-card.pending { --s-color: var(--warn);    --s-bg: var(--warn-bg);    --s-border: var(--warn-border);    }
        .main-card.failed  { --s-color: var(--danger);  --s-bg: var(--danger-bg);  --s-border: var(--danger-border);  }

        .status-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text);
            margin-bottom: 6px;
        }

        .status-sub {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.65;
        }

        .status-sub strong { color: var(--text); font-weight: 700; }

        /* Info alert (success only) */
        .info-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 16px;
            padding: 12px 14px;
            background: var(--info-bg);
            border: 1px solid var(--info-border);
            border-radius: 10px;
            font-size: 12.5px;
            color: var(--info);
            text-align: left;
            line-height: 1.6;
        }

        .info-alert i { flex-shrink: 0; margin-top: 2px; font-size: 12px; }

        .info-alert a {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--info-bg);
            border: 1px solid var(--info-border);
            color: var(--info);
            font-weight: 700;
            font-size: 11.5px;
            padding: 3px 10px;
            border-radius: 6px;
            transition: all 0.15s;
            margin-left: 3px;
        }

        .info-alert a:hover { background: var(--info); color: white; }

        /* ═══════════════════════════════════════════
           DETAIL SECTION
        ═══════════════════════════════════════════ */
        .details-section {
            padding: 22px 28px 24px;
            border-bottom: 1px solid var(--border2);
        }

        .section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted2);
            margin-bottom: 12px;
        }

        .detail-rows { display: flex; flex-direction: column; gap: 8px; }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 11px 14px;
            background: var(--surface2);
            border: 1px solid var(--border2);
            border-radius: 10px;
            gap: 12px;
            transition: border-color 0.15s;
        }

        .detail-row:hover { border-color: var(--border); }

        .detail-key {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--muted2);
            flex-shrink: 0;
        }

        .detail-val {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text);
            text-align: right;
            word-break: break-all;
        }

        .detail-val.mono { font-family: var(--mono); font-size: 12.5px; }

        .detail-val.price {
            font-family: var(--mono);
            font-size: 16px;
            font-weight: 700;
            color: var(--s-color);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: capitalize;
        }

        .main-card.success .status-pill { background: var(--success-bg); color: var(--success); border: 1px solid var(--success-border); }
        .main-card.pending .status-pill { background: var(--warn-bg);    color: var(--warn);    border: 1px solid var(--warn-border);    }
        .main-card.failed  .status-pill { background: var(--danger-bg);  color: var(--danger);  border: 1px solid var(--danger-border);  }

        /* Copy order ID button */
        .copy-order-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: none;
            cursor: pointer;
            font-family: var(--mono);
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
            padding: 3px 8px;
            border-radius: 6px;
            transition: all 0.15s;
        }

        .copy-order-btn:hover {
            background: var(--accent-bg);
            color: var(--accent);
        }

        .copy-order-btn i { font-size: 11px; color: var(--muted2); }

        /* ═══════════════════════════════════════════
           ACTIONS
        ═══════════════════════════════════════════ */
        .actions-section {
            padding: 22px 28px 26px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-pay {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: var(--font);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
            box-shadow: 0 2px 8px rgba(193,127,62,0.3);
        }

        .btn-pay:hover {
            background: var(--accent-hover);
            box-shadow: 0 4px 14px rgba(193,127,62,0.4);
            transform: translateY(-1px);
        }

        .btn-home {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            background: var(--surface2);
            color: var(--muted);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-family: var(--font);
            font-size: 13.5px;
            font-weight: 600;
            transition: all 0.15s;
        }

        .btn-home:hover { background: var(--surface); color: var(--text); border-color: var(--border); }

        /* ═══════════════════════════════════════════
           CARD FOOTER
        ═══════════════════════════════════════════ */
        .card-foot {
            padding: 14px 28px 18px;
            border-top: 1px solid var(--border2);
            text-align: center;
            font-size: 12px;
            color: var(--muted2);
        }

        .card-foot a { color: var(--muted2); transition: color 0.15s; }
        .card-foot a:hover { color: var(--accent); }

        /* ═══════════════════════════════════════════
           ANIMATIONS
        ═══════════════════════════════════════════ */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.2; }
            50%       { transform: scale(1.18); opacity: 0.06; }
        }

        @keyframes iconIn {
            from { transform: scale(0.5); opacity: 0; }
            to   { transform: scale(1);   opacity: 1; }
        }

        /* SweetAlert2 light override */
        .swal2-popup {
            font-family: var(--font) !important;
            border-radius: var(--radius) !important;
        }

        /* ═══════════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════════ */
        @media (max-width: 520px) {
            .page-wrap { padding: 24px 14px 48px; align-items: flex-start; }
            .status-hero { padding: 24px 20px 18px; }
            .details-section { padding: 18px 20px 20px; }
            .actions-section { padding: 18px 20px 22px; }
            .card-foot { padding: 12px 20px 16px; }
            .detail-row { flex-direction: column; align-items: flex-start; gap: 4px; }
            .detail-val { text-align: left; }
            .header-back span { display: none; }
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="site-header">
    <div class="header-container">
        <a href="index.php" class="header-brand">
            <div class="header-logo-mark">
                <img src="assets/images/logo.jpg" alt="Logo">
            </div>
            <span class="header-brand-text">WARUNGERIK STORE</span>
        </a>
        <a href="index.php" class="header-back">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Toko</span>
        </a>
    </div>
</header>

<!-- Main -->
<main class="page-wrap">
    <div class="page-inner">
        <div class="main-card <?php echo $status_class; ?>">

            <!-- Colored top strip -->
            <div class="status-strip"></div>

            <!-- STATUS HERO -->
            <div class="status-hero">
                <div class="status-icon-wrap">
                    <div class="status-pulse"></div>
                    <div class="status-icon">
                        <?php if ($status_class === 'success'): ?>
                            <i class="fas fa-check"></i>
                        <?php elseif ($status_class === 'pending'): ?>
                            <i class="fas fa-hourglass-half"></i>
                        <?php else: ?>
                            <i class="fas fa-xmark"></i>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($status_class === 'success'): ?>
                    <div class="status-title">Pembayaran Berhasil</div>
                    <div class="status-sub">
                        Halo <strong><?php echo htmlspecialchars($pesanan['nama_pelanggan']); ?></strong>,<br>
                        Produkmu Telah Dikirim Ke WhatsApp.
                    </div>
                    <div class="info-alert">
                        <i class="fas fa-circle-info"></i>
                        <div>
                            Jika Produk Tidak Dikirim Ke WhatsApp Kalian Cek Manual Ya!
                            <a href="cek_pesanan.php"><i class="fa-solid fa-magnifying-glass"></i> Cek Manual Klik Disini</a></br>Menggunakan ID Pesanan di bawah.
                        </div>
                    </div>

                <?php elseif ($status_class === 'pending'): ?>
                    <div class="status-title">Menunggu Pembayaran</div>
                    <div class="status-sub">Selesaikan pembayaran agar pesanan<br>segera kami proses.</div>

                <?php else: ?>
                    <div class="status-title">Pembayaran Gagal</div>
                    <div class="status-sub">Transaksi tidak berhasil. Silakan coba lagi<br>atau hubungi admin kami.</div>
                <?php endif; ?>
            </div>

            <!-- DETAIL PESANAN -->
            <div class="details-section">
                <div class="section-label">Detail Pesanan</div>
                <div class="detail-rows">

                    <div class="detail-row">
                        <span class="detail-key">ID Pesanan</span>
                        <button class="copy-order-btn" onclick="copyOrderID('<?php echo htmlspecialchars($pesanan['order_id']); ?>')">
                            <?php echo htmlspecialchars($pesanan['order_id']); ?>
                            <i class="far fa-copy"></i>
                        </button>
                    </div>

                    <div class="detail-row">
                        <span class="detail-key">Kategori</span>
                        <span class="detail-val"><?php echo htmlspecialchars($pesanan['nama_kategori']); ?></span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-key">Produk</span>
                        <span class="detail-val"><?php echo htmlspecialchars($pesanan['nama_produk']); ?></span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-key">Status</span>
                        <span class="detail-val">
                            <span class="status-pill">
                                <?php if ($status_class === 'success'): ?>
                                    <i class="fas fa-check-circle"></i>
                                <?php elseif ($status_class === 'pending'): ?>
                                    <i class="fas fa-clock"></i>
                                <?php else: ?>
                                    <i class="fas fa-times-circle"></i>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($pesanan['status_pembayaran']); ?>
                            </span>
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-key">Total Bayar</span>
                        <span class="detail-val price">
                            Rp <?php echo number_format($pesanan['harga'], 0, ',', '.'); ?>
                        </span>
                    </div>

                </div>
            </div>

            <!-- ACTIONS -->
            <div class="actions-section">
                <?php if ($status_class === 'pending' && !empty($pesanan['snap_token'])): ?>
                    <button id="pay-button" class="btn-pay">
                        <i class="fas fa-credit-card"></i> Bayar Sekarang
                    </button>
                <?php endif; ?>

                <a href="index.php" class="btn-home">
                    <i class="fas fa-house"></i> Kembali ke Beranda
                </a>
            </div>

            <!-- CARD FOOTER -->
            <div class="card-foot">
                &copy; <?php echo date('Y'); ?> WARUNGERIK STORE &nbsp;·&nbsp;
                <a href="tentang.php">Butuh bantuan?</a>
            </div>

        </div>
    </div>
</main>

<script>
    function copyOrderID(text) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'ID Tersalin!',
                text: text,
                showConfirmButton: false,
                timer: 1800,
                toast: true,
                position: 'top-end',
                timerProgressBar: true,
                customClass: { popup: 'swal2-popup' }
            });
        }).catch(() => {});
    }

    <?php if ($status_class === 'pending' && !empty($pesanan['snap_token'])): ?>
    document.getElementById('pay-button').onclick = function () {
        snap.pay('<?php echo $pesanan['snap_token']; ?>', {
            onSuccess: function () {
                window.location.href = 'pesanan.php?order_id=<?php echo $pesanan['order_id']; ?>';
            },
            onPending: function () {
                Swal.fire({
                    icon: 'info',
                    title: 'Menunggu Pembayaran',
                    text: 'Selesaikan pembayaran di channel pilihan Anda.',
                    confirmButtonColor: '#c17f3e',
                    customClass: { popup: 'swal2-popup' }
                }).then(() => window.location.reload());
            },
            onError: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Pembayaran Gagal',
                    text: 'Terjadi kesalahan saat memproses pembayaran.',
                    confirmButtonColor: '#b91c1c',
                    customClass: { popup: 'swal2-popup' }
                });
            }
        });
    };
    <?php endif; ?>
</script>
</body>
</html>