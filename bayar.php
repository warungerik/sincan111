<?php
include 'includes/koneksi.php';
include 'config_midtrans.php';

$nomor_wa_admin = '6285183129647';
$halaman_utama = 'index.php';
$biaya_layanan_flat   = 700;
$biaya_layanan_persen = 0.007;

$produk = null;
$produk_id = null;
$show_payment_button = false;
$snapToken = null;
$total_harga_server = 0;
$order_id_server = null;
$produk_lainnya = [];
$stok_habis = false;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] <= 0) {
        die("Error: ID Produk tidak valid.");
    }
    $produk_id = (int)$_GET['id'];

    $stmt = $koneksi->prepare("
        SELECT p.*, k.nama_kategori 
        FROM produk p 
        LEFT JOIN kategori k ON p.kategori_id = k.id 
        WHERE p.id = ? AND p.cek_stok = 1
    ");
    $stmt->bind_param("i", $produk_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $produk = $result->fetch_assoc();
    
    if (!$produk) {
        die("Error: Produk tidak ditemukan atau tidak menggunakan sistem stok.");
    }

    if ($produk['stok'] <= 0) {
        $stok_habis = true;
    }

    $stmt_lainnya = $koneksi->prepare("SELECT id, nama_produk, harga, diskon_persen, gambar FROM produk WHERE id != ? AND stok > 0 AND cek_stok = 1 ORDER BY RAND() LIMIT 4");
    $stmt_lainnya->bind_param("i", $produk_id);
    $stmt_lainnya->execute();
    $result_lainnya = $stmt_lainnya->get_result();
    while ($row = $result_lainnya->fetch_assoc()) {
        $produk_lainnya[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['produk_id'], $_POST['nama_pelanggan'], $_POST['email_pelanggan'], $_POST['wa_pelanggan'], $_POST['jumlah']) || empty(trim($_POST['nama_pelanggan'])) || !filter_var(trim($_POST['email_pelanggan']), FILTER_VALIDATE_EMAIL) || empty(trim($_POST['wa_pelanggan'])) || !is_numeric($_POST['jumlah'])) {
        die("Error: Semua data harus diisi dengan format yang benar.");
    }
    
    $produk_id = (int)$_POST['produk_id'];
    $nama_pelanggan = trim($_POST['nama_pelanggan']);
    $email_pelanggan = trim($_POST['email_pelanggan']);
    $wa_pelanggan = trim($_POST['wa_pelanggan']);
    $jumlah = (int)$_POST['jumlah'];
    $kode_voucher = isset($_POST['kode_voucher']) ? trim($_POST['kode_voucher']) : '';

    $stmt = $koneksi->prepare("SELECT * FROM produk WHERE id = ?");
    $stmt->bind_param("i", $produk_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $produk = $result->fetch_assoc();

    if (!$produk) die("Error: Produk tidak ditemukan.");
    if ($jumlah <= 0 || $jumlah > $produk['stok']) die("Error: Jumlah pembelian tidak valid atau melebihi stok ({$produk['stok']}).");

    $harga_asli = $produk['harga'];
    $diskon_persen = $produk['diskon_persen'];
    $harga_satuan_final = ($diskon_persen > 0) ? $harga_asli - ($harga_asli * $diskon_persen / 100) : $harga_asli;
    $subtotal = $harga_satuan_final * $jumlah;
    
    $potongan_voucher = 0;
    if (!empty($kode_voucher)) {
        $stmt_voucher = $koneksi->prepare("SELECT * FROM voucher WHERE kode_voucher = ? AND status = 'aktif' AND (tanggal_kadaluarsa IS NULL OR tanggal_kadaluarsa >= CURDATE())");
        $stmt_voucher->bind_param("s", $kode_voucher);
        $stmt_voucher->execute();
        $result_voucher = $stmt_voucher->get_result();
        if ($voucher = $result_voucher->fetch_assoc()) {
            $potongan_voucher = ($voucher['tipe_diskon'] == 'persen') ? $subtotal * ($voucher['nilai_diskon'] / 100) : $voucher['nilai_diskon'];
            $potongan_voucher = min($potongan_voucher, $subtotal);
        }
    }
    
    $subtotal_setelah_voucher = $subtotal - $potongan_voucher;
    $biaya_layanan = $biaya_layanan_flat + ceil($subtotal_setelah_voucher * $biaya_layanan_persen);
    $total_harga_server = $subtotal_setelah_voucher + $biaya_layanan;
    $order_id_server = 'WARUNGERIK-' . time() . '-' . $produk_id;

    $item_details_midtrans = [['id' => $produk['id'], 'price' => $harga_satuan_final, 'quantity' => $jumlah, 'name' => $produk['nama_produk']]];
    if ($potongan_voucher > 0) {
        $item_details_midtrans[] = ['id' => 'VOUCHER', 'price' => -$potongan_voucher, 'quantity' => 1, 'name' => 'Voucher ' . htmlspecialchars($kode_voucher)];
    }
    $item_details_midtrans[] = ['id' => 'FEE', 'price' => $biaya_layanan, 'quantity' => 1, 'name' => "Biaya Layanan"];

    $stmt_transaksi = $koneksi->prepare("INSERT INTO transaksi (order_id, produk_id, harga, status_pembayaran, nama_pelanggan, email_pelanggan, wa_pelanggan, jumlah) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?)");
    $stmt_transaksi->bind_param("sidsssi", $order_id_server, $produk_id, $total_harga_server, $nama_pelanggan, $email_pelanggan, $wa_pelanggan, $jumlah);
    $stmt_transaksi->execute();

    $params = [
        'transaction_details' => ['order_id' => $order_id_server, 'gross_amount' => $total_harga_server],
        'item_details' => $item_details_midtrans,
        'customer_details' => ['first_name' => $nama_pelanggan, 'email' => $email_pelanggan, 'phone' => $wa_pelanggan],
    ];

    try {
        $snapToken = \Midtrans\Snap::getSnapToken($params);
    } catch (Exception $e) {
        die('Error: Gagal mendapatkan token pembayaran. ' . $e->getMessage());
    }
    
    $stmt_update_token = $koneksi->prepare("UPDATE transaksi SET snap_token = ? WHERE order_id = ?");
    $stmt_update_token->bind_param("ss", $snapToken, $order_id_server);
    $stmt_update_token->execute();

    $show_payment_button = true;
}

$harga_final_tampil = 0;
if ($produk) {
    $harga_final_tampil = ($produk['diskon_persen'] > 0) ? $produk['harga'] - ($produk['harga'] * $produk['diskon_persen'] / 100) : $produk['harga'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $produk ? htmlspecialchars($produk['nama_produk']) : 'Pesan'; ?> - WARUNGERIK STORE</title>
    <?php if ($produk): ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org/",
      "@type": "Product",
      "name": "<?php echo htmlspecialchars($produk['nama_produk']); ?>",
      "image": "https://www.warungerik.com/assets/images/<?php echo htmlspecialchars($produk['gambar']); ?>",
      "description": "<?php echo !empty($produk['deskripsi']) ? htmlspecialchars(strip_tags($produk['deskripsi'])) : 'Beli ' . htmlspecialchars($produk['nama_produk']) . ' dengan harga termurah dan proses instan di WARUNGERIK STORE.'; ?>",
      "sku": "<?php echo $produk_id; ?>",
      "brand": { "@type": "Brand", "name": "WARUNGERIK STORE" },
      "offers": {
        "@type": "Offer",
        "url": "https://www.warungerik.com/bayar.php?id=<?php echo $produk_id; ?>",
        "priceCurrency": "IDR",
        "price": "<?php echo $harga_final_tampil; ?>",
        "priceValidUntil": "<?php echo date('Y-m-d', strtotime('+1 year')); ?>",
        "availability": "<?php echo $stok_habis ? 'https://schema.org/OutOfStock' : 'https://schema.org/InStock'; ?>"
      }
    }
    </script>
    <?php endif; ?>
    <link rel="icon" type="image/png" href="<?php echo ($produk && !empty($produk['gambar'])) ? 'assets/images/' . htmlspecialchars($produk['gambar']) : 'assets/images/favicon.png'; ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>

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
            --info:          #2563a8;
            --info-bg:       rgba(37,99,168,0.08);
            --warn:          #b45309;
            --warn-bg:       rgba(180,83,9,0.08);
            --danger:        #b91c1c;
            --danger-bg:     rgba(185,28,28,0.08);
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

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

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
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: 9px;
            border: 1px solid var(--border);
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
            background: var(--surface);
            transition: all 0.15s;
        }

        .header-back:hover { background: var(--surface2); color: var(--text); border-color: var(--border2); }

        .hamburger-menu {
            display: none;
            flex-direction: column;
            gap: 5px;
            padding: 8px;
            background: none;
            border: 1px solid var(--border);
            border-radius: 9px;
            cursor: pointer;
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

        /* ═══════════════════════════════════════════
           PAGE LAYOUT
        ═══════════════════════════════════════════ */
        .page-wrap {
            padding: 32px 0 64px;
        }

        .page-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 24px;
            align-items: start;
        }

        /* Breadcrumb */
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

        /* ═══════════════════════════════════════════
           CARD BASE
        ═══════════════════════════════════════════ */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .card-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border2);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .card-header-icon.amber { background: var(--accent-bg); color: var(--accent); }
        .card-header-icon.green { background: var(--success-bg); color: var(--success); }
        .card-header-icon.blue  { background: var(--info-bg); color: var(--info); }

        .card-header h2 {
            font-size: 15px;
            font-weight: 800;
            color: var(--text);
        }

        .card-body { padding: 22px; }

        /* ═══════════════════════════════════════════
           PRODUCT INFO (LEFT PANEL)
        ═══════════════════════════════════════════ */
        .product-image-wrap {
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
            border: 1px solid var(--border);
            background: var(--surface2);
            aspect-ratio: 16/9;
        }

        .product-image-wrap img {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }

        .product-image-wrap:hover img { transform: scale(1.03); }

        .product-title-wrap {
            margin-bottom: 16px;
        }

        .product-category-tag {
            display: inline-block;
            background: var(--accent-bg);
            color: var(--accent);
            border: 1px solid var(--accent-border);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 3px 9px;
            border-radius: 99px;
            margin-bottom: 8px;
        }

        .product-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text);
            margin-bottom: 6px;
        }

        .product-meta-row {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .product-price-final {
            font-family: var(--mono);
            font-size: 22px;
            font-weight: 700;
            color: var(--success);
        }

        .product-price-original {
            font-family: var(--mono);
            font-size: 14px;
            color: var(--muted2);
            text-decoration: line-through;
        }

        .product-discount-badge {
            background: var(--danger);
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 99px;
            font-family: var(--mono);
        }

        .product-stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--success-bg);
            color: var(--success);
            font-size: 12px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 99px;
            font-family: var(--mono);
        }

        .product-stock-badge.empty {
            background: var(--danger-bg);
            color: var(--danger);
        }

        .product-desc {
            background: var(--surface2);
            border: 1px solid var(--border2);
            border-radius: 10px;
            padding: 16px;
            font-size: 13.5px;
            color: var(--muted);
            line-height: 1.75;
            white-space: pre-wrap;
            word-break: break-word;
        }

        /* Out of stock */
        .oos-message {
            text-align: center;
            padding: 20px 0;
        }

        .oos-icon {
            width: 64px; height: 64px;
            background: var(--danger-bg);
            color: var(--danger);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            margin: 0 auto 16px;
        }

        .oos-message h3 {
            font-size: 18px;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 8px;
        }

        .oos-message p {
            font-size: 13.5px;
            color: var(--muted);
            margin-bottom: 20px;
        }

        /* ═══════════════════════════════════════════
           ORDER FORM (RIGHT PANEL)
        ═══════════════════════════════════════════ */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .form-label i { color: var(--accent); font-size: 11px; }

        .form-input {
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

        .form-input::placeholder { color: var(--muted2); }

        .form-input:focus {
            border-color: var(--accent-border);
            box-shadow: 0 0 0 3px var(--accent-bg);
        }

        .form-hint {
            font-size: 11.5px;
            color: var(--muted2);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Quantity */
        .qty-wrapper {
            display: inline-flex;
            align-items: center;
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            background: var(--surface);
            transition: border-color 0.15s;
        }

        .qty-wrapper:hover { border-color: var(--accent-border); }

        .qty-btn {
            width: 42px; height: 42px;
            background: var(--surface2);
            border: none;
            font-size: 18px;
            font-weight: 700;
            color: var(--muted);
            cursor: pointer;
            transition: all 0.15s;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .qty-btn:hover { background: var(--accent); color: white; }

        #quantity {
            width: 52px;
            text-align: center;
            border: none;
            border-left: 1px solid var(--border);
            border-right: 1px solid var(--border);
            font-family: var(--mono);
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            background: transparent;
            padding: 0;
            height: 42px;
            outline: none;
        }

        #quantity::-webkit-outer-spin-button,
        #quantity::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        #quantity {
            -moz-appearance: textfield;
        }

        /* Voucher */
        .voucher-row {
            display: flex;
            gap: 8px;
        }

        .voucher-row .form-input { flex: 1; }

        .btn-apply-voucher {
            padding: 0 16px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: var(--font);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .btn-apply-voucher:hover { background: var(--accent-hover); }
        .btn-apply-voucher:disabled { background: var(--muted2); cursor: not-allowed; }

        .voucher-msg {
            margin-top: 8px;
            font-size: 12.5px;
            font-weight: 600;
            padding: 8px 12px;
            border-radius: 8px;
            display: none;
        }

        .voucher-msg.success { background: var(--success-bg); color: var(--success); display: block; }
        .voucher-msg.error   { background: var(--danger-bg); color: var(--danger); display: block; }

        .voucher-channel-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: var(--info-bg);
            border: 1px solid rgba(37,99,168,0.15);
            border-radius: 10px;
            padding: 10px 14px;
            margin-top: 10px;
        }

        .voucher-channel-row span {
            font-size: 12px;
            color: var(--info);
            font-weight: 600;
        }

        .voucher-channel-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: #25D366;
            color: white;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            transition: opacity 0.15s;
        }

        .voucher-channel-btn:hover { opacity: 0.85; }

        /* ═══════════════════════════════════════════
           VOUCHER LIST
        ═══════════════════════════════════════════ */
        .voucher-list-wrap {
            margin-top: 12px;
        }

        .voucher-list-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted2);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .voucher-list-title i { color: var(--accent); }

        .voucher-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .voucher-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: var(--surface);
            border: 1px dashed var(--accent-border);
            border-radius: 9px;
            padding: 9px 13px;
            cursor: pointer;
            transition: all 0.15s;
        }

        .voucher-item:hover {
            background: var(--accent-bg);
            border-color: var(--accent);
        }

        .voucher-item.used {
            opacity: 0.5;
            cursor: not-allowed;
            border-color: var(--border);
        }

        .voucher-item-left {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .voucher-item-code {
            font-family: var(--mono);
            font-size: 12px;
            font-weight: 700;
            color: var(--accent);
            letter-spacing: 0.05em;
        }

        .voucher-item-desc {
            font-size: 11px;
            color: var(--muted);
        }

        .voucher-item-exp {
            font-size: 10px;
            color: var(--muted2);
        }

        .voucher-item-btn {
            font-size: 11px;
            font-weight: 700;
            color: var(--accent);
            background: var(--accent-bg);
            border: 1px solid var(--accent-border);
            border-radius: 6px;
            padding: 4px 10px;
            white-space: nowrap;
            flex-shrink: 0;
            transition: all 0.15s;
        }

        .voucher-item:hover .voucher-item-btn {
            background: var(--accent);
            color: white;
        }

        .voucher-empty {
            font-size: 12px;
            color: var(--muted2);
            text-align: center;
            padding: 10px 0;
        }

        /* Price Breakdown */
        .price-breakdown {
            background: var(--surface2);
            border: 1px solid var(--border2);
            border-radius: 10px;
            padding: 16px;
            margin-top: 16px;
        }

        .price-breakdown-title {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted2);
            margin-bottom: 12px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .price-row .label { color: var(--muted); }

        .price-row .val {
            font-family: var(--mono);
            font-weight: 600;
            color: var(--text);
        }

        .price-row .val.discount { color: var(--success); }

        .price-row-divider {
            border: none;
            border-top: 1px dashed var(--border);
            margin: 10px 0;
        }

        .price-row-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .price-row-total .label {
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
        }

        .price-row-total .val-total {
            font-family: var(--mono);
            font-size: 20px;
            font-weight: 700;
            color: var(--success);
        }

        /* Buttons */
        .btn-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
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
            margin-top: 16px;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            box-shadow: 0 4px 14px rgba(193,127,62,0.4);
            transform: translateY(-1px);
        }

        .btn-secondary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px;
            background: var(--surface);
            color: var(--muted);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-family: var(--font);
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            margin-top: 10px;
            text-align: center;
        }

        .btn-secondary:hover { background: var(--surface2); color: var(--text); }

        .btn-wa {
            background: #25D366;
            box-shadow: 0 2px 8px rgba(37,211,102,0.3);
        }

        .btn-wa:hover {
            background: #1da851;
            box-shadow: 0 4px 14px rgba(37,211,102,0.4);
        }

        .btn-guide {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            width: 100%;
            padding: 10px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-family: var(--font);
            font-size: 13px;
            font-weight: 600;
            color: var(--accent);
            cursor: pointer;
            transition: all 0.15s;
            margin-bottom: 18px;
        }

        .btn-guide:hover { border-color: var(--accent-border); color: var(--accent); background: var(--accent-bg); }

        /* Pay Now (step 2) */
        .pay-now-wrap { text-align: center; }

        .pay-now-wrap p {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 16px;
            line-height: 1.6;
        }

        .order-id-badge {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
            font-family: var(--mono);
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 16px;
            word-break: break-all;
        }

        .order-total-display {
            font-family: var(--mono);
            font-size: 26px;
            font-weight: 700;
            color: var(--success);
            margin-bottom: 20px;
        }

        /* ═══════════════════════════════════════════
           RELATED PRODUCTS
        ═══════════════════════════════════════════ */
        .related-section { margin-top: 40px; }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .section-title span { color: var(--accent); }

        .related-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
        }

        .related-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s;
        }

        .related-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-3px);
            border-color: var(--accent-border);
        }

        .related-card-img {
            aspect-ratio: 4/3;
            overflow: hidden;
            background: var(--surface2);
        }

        .related-card-img img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .related-card:hover .related-card-img img { transform: scale(1.06); }

        .related-card-body { padding: 12px 14px 14px; }

        .related-card-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.35;
        }

        .related-price-final {
            font-family: var(--mono);
            font-size: 13px;
            font-weight: 700;
            color: var(--success);
        }

        .related-price-original {
            font-family: var(--mono);
            font-size: 11px;
            color: var(--muted2);
            text-decoration: line-through;
            margin-right: 4px;
        }

        /* ═══════════════════════════════════════════
           MODALS
        ═══════════════════════════════════════════ */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(4px);
            z-index: 400;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.open {
            display: flex;
            animation: fadeIn 0.2s ease;
        }

        .modal-box {
            background: var(--surface);
            border-radius: var(--radius-lg);
            width: 90%;
            max-width: 480px;
            box-shadow: var(--shadow-xl);
            animation: slideUp 0.3s ease;
            overflow: hidden;
        }

        .modal-head {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-head h3 {
            font-size: 15px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-head h3 i { color: var(--accent); }

        .modal-close {
            width: 30px; height: 30px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--surface2);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
            color: var(--muted);
            cursor: pointer;
            transition: all 0.15s;
        }

        .modal-close:hover { background: var(--danger-bg); color: var(--danger); border-color: rgba(185,28,28,0.2); }

        .modal-body { padding: 22px; }

        .modal-body ol {
            padding-left: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .modal-body ol li {
            font-size: 13.5px;
            color: var(--muted);
            line-height: 1.6;
        }

        .modal-body ol li strong { color: var(--text); }

        .modal-confirm-body { padding: 28px 22px; text-align: center; }

        .modal-confirm-body .confirm-icon {
            width: 56px; height: 56px;
            background: var(--warn-bg);
            color: var(--warn);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            margin: 0 auto 16px;
        }

        .modal-confirm-body h3 {
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .modal-confirm-body p {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.65;
            margin-bottom: 24px;
        }

        .modal-confirm-body p small { color: var(--muted2); }

        .modal-btn-row { display: flex; gap: 10px; }
        .modal-btn-row .btn-primary { margin-top: 0; flex: 1; }
        .modal-btn-row .btn-secondary { margin-top: 0; flex: 1; }

        /* ═══════════════════════════════════════════
           FOOTER
        ═══════════════════════════════════════════ */
        .site-footer {
            background: var(--text);
            color: rgba(255,255,255,0.9);
        }

        .footer-top { padding: 40px 0 32px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 40px; }

        .footer-brand-img { width: 40px; height: 40px; border-radius: 10px; object-fit: cover; margin-bottom: 12px; }
        .footer-brand-name { font-size: 15px; font-weight: 800; color: white; margin-bottom: 6px; }
        .footer-brand-desc { font-size: 12.5px; color: rgba(255,255,255,0.5); line-height: 1.7; margin-bottom: 18px; }
        .footer-socials { display: flex; gap: 8px; flex-wrap: wrap; }

        .social-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: 9px; font-size: 12px;
            font-weight: 700; color: white; transition: all 0.15s;
        }

        .social-btn.tg  { background: rgba(0,136,204,0.25); }
        .social-btn.tg:hover  { background: #0088cc; }
        .social-btn.wa  { background: rgba(37,211,102,0.2); }
        .social-btn.wa:hover  { background: #25D366; }

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
        .help-options p { font-size: 12px; color: var(--muted); margin-bottom: 12px; }
        .help-options-btns { display: flex; flex-direction: column; gap: 7px; }
        .help-options-btns a { display: flex; align-items: center; gap: 8px; padding: 9px 13px; border-radius: 9px; font-size: 13px; font-weight: 700; color: white; transition: opacity 0.15s; }
        .help-options-btns a:hover { opacity: 0.9; }
        .help-options-btns a.tg { background: #0088cc; }
        .help-options-btns a.wa { background: #25D366; }
        .help-close { position: absolute; top: 10px; right: 12px; font-size: 18px; color: var(--muted2); cursor: pointer; }
        .help-close:hover { color: var(--text); }

        /* Animations */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }

        .animate-up { animation: fadeUp 0.45s ease both; }
        .delay-1 { animation-delay: 0.07s; }
        .delay-2 { animation-delay: 0.14s; }

        /* ═══════════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════════ */
        @media (max-width: 900px) {
            .page-grid { grid-template-columns: 1fr; }
            .related-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 768px) {
            .hamburger-menu { display: flex; }
            .header-back span { display: none; }
            .footer-grid { grid-template-columns: 1fr; gap: 24px; }
            .footer-bottom { flex-direction: column; text-align: center; }
            .footer-bottom-links { justify-content: center; flex-wrap: wrap; }
            .help-button { bottom: 18px; right: 18px; }
            .help-options { right: 14px; bottom: 80px; min-width: auto; left: 14px; }
        }

        @media (max-width: 480px) {
            .related-grid { grid-template-columns: repeat(2, 1fr); }
            .modal-btn-row { flex-direction: column; }
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

        <a href="index.php" class="header-back">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Toko</span>
        </a>

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

<!-- Main Content -->
<main class="page-wrap">
    <div class="container">

        <!-- Breadcrumb -->
        <div class="breadcrumb animate-up">
            <a href="index.php"><i class="fas fa-home"></i></a>
            <span class="sep"><i class="fas fa-chevron-right"></i></span>
            <span class="current"><?= $produk ? htmlspecialchars($produk['nama_produk']) : 'Pemesanan' ?></span>
        </div>

        <?php if ($stok_habis): ?>
        <!-- ═══ STOK HABIS ═══ -->
        <div class="page-grid animate-up delay-1">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-icon amber"><i class="fas fa-box-open"></i></div>
                    <h2>Detail Produk</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($produk['gambar'])): ?>
                    <div class="product-image-wrap">
                        <img src="assets/images/<?= htmlspecialchars($produk['gambar']) ?>" alt="<?= htmlspecialchars($produk['nama_produk']) ?>">
                    </div>
                    <?php endif; ?>
                    <div class="product-title-wrap">
                        <?php if (!empty($produk['nama_kategori'])): ?>
                            <span class="product-category-tag"><?= htmlspecialchars($produk['nama_kategori']) ?></span>
                        <?php endif; ?>
                        <h1 class="product-title"><?= htmlspecialchars($produk['nama_produk']) ?></h1>
                        <div class="product-meta-row">
                            <span class="product-price-final">Rp <?= number_format($harga_final_tampil, 0, ',', '.') ?></span>
                            <span class="product-stock-badge empty"><i class="fas fa-times-circle"></i> Stok Habis</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card animate-up delay-2">
                <div class="card-header">
                    <div class="card-header-icon amber"><i class="fas fa-exclamation-triangle"></i></div>
                    <h2>Stok Habis</h2>
                </div>
                <div class="card-body">
                    <div class="oos-message">
                        <div class="oos-icon"><i class="fas fa-box-open"></i></div>
                        <h3>Maaf, Stok Habis</h3>
                        <p>Produk ini sedang tidak tersedia. Silakan hubungi admin untuk pemesanan manual atau cek produk lainnya.</p>
                    </div>
                    <?php
                    $nama_kat = !empty($produk['nama_kategori']) ? $produk['nama_kategori'] : '-';
                    $pesan_wa = "Halo Admin, saya tertarik dengan produk ini namun stoknya habis:\n\n*Produk:* " . $produk['nama_produk'] . "\n*Kategori:* " . $nama_kat . "\n\nApakah bisa saya order secara manual?";
                    $link_wa = "https://wa.me/{$nomor_wa_admin}?text=" . urlencode($pesan_wa);
                    ?>
                    <a href="<?= $link_wa ?>" target="_blank" class="btn-primary btn-wa">
                        <i class="fab fa-whatsapp"></i> Hubungi Admin
                    </a>
                    <a href="<?= $halaman_utama ?>" class="btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Toko
                    </a>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- ═══ NORMAL / PAYMENT ═══ -->
        <div class="page-grid">

            <!-- LEFT: Product Info -->
            <div class="card animate-up delay-1">
                <div class="card-header">
                    <div class="card-header-icon amber"><i class="fas fa-box"></i></div>
                    <h2>Detail Produk</h2>
                </div>
                <div class="card-body">
                    <?php if ($produk && !empty($produk['gambar'])): ?>
                    <div class="product-image-wrap">
                        <img src="assets/images/<?= htmlspecialchars($produk['gambar']) ?>" alt="<?= htmlspecialchars($produk['nama_produk']) ?>">
                    </div>
                    <?php endif; ?>

                    <?php if ($produk): ?>
                    <div class="product-title-wrap">
                        <?php if (!empty($produk['nama_kategori'])): ?>
                            <span class="product-category-tag"><?= htmlspecialchars($produk['nama_kategori']) ?></span>
                        <?php endif; ?>
                        <h1 class="product-title"><?= htmlspecialchars($produk['nama_produk']) ?></h1>
                        <div class="product-meta-row">
                            <span class="product-price-final">Rp <?= number_format($harga_final_tampil, 0, ',', '.') ?></span>
                            <?php if ($produk['diskon_persen'] > 0): ?>
                                <span class="product-price-original">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></span>
                                <span class="product-discount-badge">-<?= $produk['diskon_persen'] ?>%</span>
                            <?php endif; ?>
                            <span class="product-stock-badge">
                                <i class="fas fa-cubes"></i> <?= $produk['stok'] ?> Tersedia
                            </span>
                        </div>
                    </div>

                    <?php if (!empty($produk['deskripsi'])): ?>
                    <div class="product-desc"><?= htmlspecialchars($produk['deskripsi']) ?></div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- RIGHT: Order Form / Pay Button -->
            <div class="animate-up delay-2">

                <?php if (!$show_payment_button && $produk): ?>
                <!-- ── ORDER FORM ── -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon green"><i class="fas fa-shopping-cart"></i></div>
                        <h2>Form Pemesanan</h2>
                    </div>
                    <div class="card-body">

                        <button type="button" id="open-modal-btn" class="btn-guide">
                            <i class="fas fa-info-circle"></i> Lihat Cara Order
                        </button>

                        <form id="payment-form-actual" action="bayar.php" method="POST">
                            <input type="hidden" name="produk_id" value="<?= $produk_id ?>">

                            <!-- Quantity -->
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-shopping-cart"></i> Jumlah Pembelian</label>
                                <div class="qty-wrapper">
                                    <button type="button" class="qty-btn" id="btn-minus">−</button>
                                    <input type="number" id="quantity" name="jumlah" value="1" min="1" max="<?= $produk['stok'] ?>" readonly>
                                    <button type="button" class="qty-btn" id="btn-plus">+</button>
                                </div>
                            </div>

                            <!-- Nama -->
                            <div class="form-group">
                                <label class="form-label" for="nama_pelanggan"><i class="fas fa-user"></i> Nama Lengkap</label>
                                <input type="text" id="nama_pelanggan" name="nama_pelanggan" class="form-input" placeholder="Masukkan nama lengkap" required>
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label class="form-label" for="email_pelanggan"><i class="fas fa-envelope"></i> Alamat Email</label>
                                <input type="email" id="email_pelanggan" name="email_pelanggan" class="form-input" placeholder="nama@email.com" required>
                                <p class="form-hint"><i class="fas fa-info-circle"></i> Bukti pembayaran akan dikirim ke email ini.</p>
                            </div>

                            <!-- WhatsApp -->
                            <div class="form-group">
                                <label class="form-label" for="wa_pelanggan"><i class="fab fa-whatsapp"></i> Nomor WhatsApp</label>
                                <input type="tel" id="wa_pelanggan" name="wa_pelanggan" class="form-input" placeholder="6281234567890" required>
                                <p class="form-hint"><i class="fas fa-exclamation-circle" style="color:var(--warn)"></i> Pastikan nomor aktif — produk dikirim via WhatsApp!</p>
                            </div>

                            <!-- Voucher -->
                            <div class="form-group">
                                <label class="form-label" for="kode_voucher"><i class="fas fa-ticket-alt"></i> Kode Voucher</label>
                                <div class="voucher-row">
                                    <input type="text" id="kode_voucher" name="kode_voucher" class="form-input" placeholder="Masukkan kode voucher">
                                    <button type="button" id="btn-apply-voucher" class="btn-apply-voucher">Pakai</button>
                                </div>
                                <div id="voucher-message" class="voucher-msg"></div>

                                <!-- Info saluran WA -->
                                <div class="voucher-channel-row">
                                    <span><i class="fab fa-whatsapp"></i> Info voucher di Saluran WhatsApp</span>
                                    <a href="https://whatsapp.com/channel/0029VbC6IU42ZjCk8kRLox24" target="_blank" class="voucher-channel-btn">
                                        <i class="fab fa-whatsapp"></i> Gabung
                                    </a>
                                </div>

                                <!-- ═══ DAFTAR VOUCHER AKTIF ═══ -->
                                <div class="voucher-list-wrap" id="voucher-list-wrap">
                                    <div class="voucher-list-title">
                                        <i class="fas fa-tags"></i> Voucher Tersedia
                                    </div>
                                    <div class="voucher-list" id="voucher-list">
                                        <div class="voucher-empty">
                                            <i class="fas fa-spinner fa-spin"></i> Memuat voucher...
                                        </div>
                                    </div>
                                </div>
                                <!-- ═══ END DAFTAR VOUCHER ═══ -->

                            </div>

                            <!-- Price Breakdown -->
                            <div class="price-breakdown">
                                <div class="price-breakdown-title">Rincian Harga</div>
                                <div class="price-row">
                                    <span class="label">Harga Satuan</span>
                                    <span class="val">Rp <span id="harga-satuan"><?= number_format($harga_final_tampil, 0, ',', '.') ?></span></span>
                                </div>
                                <div class="price-row">
                                    <span class="label">Subtotal</span>
                                    <span class="val">Rp <span id="subtotal-text">0</span></span>
                                </div>
                                <div class="price-row" id="voucher-breakdown" style="display:none;">
                                    <span class="label">Potongan Voucher</span>
                                    <span class="val discount">- Rp <span id="potongan-voucher-text">0</span></span>
                                </div>
                                <div class="price-row">
                                    <span class="label">Biaya Layanan</span>
                                    <span class="val">Rp <span id="biaya-layanan-text">0</span></span>
                                </div>
                                <hr class="price-row-divider">
                                <div class="price-row-total">
                                    <span class="label">Total Bayar</span>
                                    <span class="val-total">Rp <span id="total-harga-text">0</span></span>
                                </div>
                            </div>

                            <button type="submit" class="btn-primary">
                                <i class="fas fa-credit-card"></i> Bayar Sekarang
                            </button>
                        </form>

                    </div>
                </div>

                <?php elseif ($show_payment_button): ?>
                <!-- ── PAY NOW ── -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon green"><i class="fas fa-money-bill-wave"></i></div>
                        <h2>Selesaikan Pembayaran</h2>
                    </div>
                    <div class="card-body pay-now-wrap">
                        <p>Pesanan berhasil dibuat. Klik tombol di bawah untuk melanjutkan ke pembayaran.</p>
                        <div class="order-id-badge">
                            <i class="fas fa-receipt" style="margin-right:6px;color:var(--muted2)"></i>
                            <?= htmlspecialchars($order_id_server) ?>
                        </div>
                        <div class="order-total-display">
                            Rp <?= number_format($total_harga_server, 0, ',', '.') ?>
                        </div>
                        <button id="pay-button" class="btn-primary">
                            <i class="fas fa-lock"></i> Bayar Sekarang
                        </button>
                        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="<?= \Midtrans\Config::$clientKey ?>"></script>
                        <script>
                            document.getElementById('pay-button').onclick = function(){
                                snap.pay('<?= $snapToken ?>', {
                                    onSuccess: function(result){ window.location.href = 'pesanan.php?order_id=<?= $order_id_server ?>'; },
                                    onPending: function(result){ window.location.href = 'pesanan.php?order_id=<?= $order_id_server ?>'; },
                                    onError: function(result){ alert("Pembayaran gagal. Silakan coba lagi."); window.location.href = 'bayar.php?id=<?= $produk_id ?>'; }
                                });
                            };
                            document.getElementById('pay-button').click();
                        </script>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($produk_lainnya)): ?>
        <div class="related-section animate-up">
            <div class="section-header">
                <h2 class="section-title">Rekomendasi <span>Lainnya</span></h2>
            </div>
            <div class="related-grid">
                <?php foreach ($produk_lainnya as $p):
                    $h_asli = $p['harga'];
                    $d = $p['diskon_persen'];
                    $h_final = $d > 0 ? $h_asli - ($h_asli * $d / 100) : $h_asli;
                ?>
                <a href="bayar.php?id=<?= $p['id'] ?>" class="related-card">
                    <div class="related-card-img">
                        <img src="assets/images/<?= htmlspecialchars($p['gambar']) ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>">
                    </div>
                    <div class="related-card-body">
                        <div class="related-card-name"><?= htmlspecialchars($p['nama_produk']) ?></div>
                        <div>
                            <?php if ($d > 0): ?>
                                <span class="related-price-original">Rp <?= number_format($h_asli, 0, ',', '.') ?></span>
                            <?php endif; ?>
                            <span class="related-price-final">Rp <?= number_format($h_final, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

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

<!-- Modal: Cara Order -->
<div id="order-guide-modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-head">
            <h3><i class="fas fa-info-circle"></i> Cara Order</h3>
            <span class="modal-close">&times;</span>
        </div>
        <div class="modal-body">
            <ol>
                <li>Pilih <strong>jumlah</strong> produk yang diinginkan.</li>
                <li>Isi <strong>Nama Lengkap</strong>, <strong>Email</strong>, dan <strong>Nomor WhatsApp</strong>.</li>
                <li>Klik <strong>"Lanjutkan ke Pembayaran"</strong> dan selesaikan pembayaran.</li>
                <li>Produk akan <strong>otomatis dikirim</strong> ke nomor WhatsApp setelah pembayaran berhasil.</li>
            </ol>
        </div>
    </div>
</div>

<!-- Modal: Konfirmasi -->
<div id="confirmation-modal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-confirm-body">
            <div class="confirm-icon"><i class="fas fa-exclamation"></i></div>
            <h3>Konfirmasi Data Pesanan</h3>
            <p>Apakah semua data yang Anda masukkan sudah benar?<br><small>Jika data salah, produk yang tidak terkirim bukan tanggung jawab kami.</small></p>
            <div class="modal-btn-row">
                <button id="confirm-yes-btn" class="btn-primary" style="margin-top:0">
                    <i class="fas fa-check-circle"></i> Ya, Lanjutkan
                </button>
                <button id="confirm-no-btn" class="btn-secondary" style="margin-top:0">
                    <i class="fas fa-times-circle"></i> Cek Lagi
                </button>
            </div>
        </div>
    </div>
</div>

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
                number: { value: 40, density: { enable: true, value_area: 900 } },
                color: { value: "#c17f3e" },
                shape: { type: "circle" },
                opacity: { value: 0.12, random: true },
                size: { value: 2.5, random: true },
                links: { enable: true, distance: 130, color: "#c17f3e", opacity: 0.07, width: 1 },
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

    // — Quantity & Price Calc —
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        const hargaSatuanEl   = document.getElementById('harga-satuan');
        const subtotalEl      = document.getElementById('subtotal-text');
        const biayaLayananEl  = document.getElementById('biaya-layanan-text');
        const totalEl         = document.getElementById('total-harga-text');
        const voucherBreakEl  = document.getElementById('voucher-breakdown');
        const potonganEl      = document.getElementById('potongan-voucher-text');
        const voucherInput    = document.getElementById('kode_voucher');
        const voucherBtn      = document.getElementById('btn-apply-voucher');
        const voucherMsg      = document.getElementById('voucher-message');

        const hargaSatuan = parseInt(hargaSatuanEl.innerText.replace(/\./g, ''));
        const maxStok     = parseInt(quantityInput.max);
        const biayaFlat   = 700;
        const biayaPersen = 0.007;
        const kategoriId  = <?= $produk['kategori_id'] ?? 0 ?>;
        let voucher = { tipe: null, nilai: 0 };

        function fmt(n) { return Math.round(n).toLocaleString('id-ID'); }

        function updateTotal() {
            const qty      = parseInt(quantityInput.value);
            const subtotal = hargaSatuan * qty;
            let potongan   = 0;

            if (voucher.tipe) {
                potongan = voucher.tipe === 'persen'
                    ? subtotal * (parseFloat(voucher.nilai) / 100)
                    : parseFloat(voucher.nilai);
                potongan = Math.min(potongan, subtotal);
            }

            voucherBreakEl.style.display = potongan > 0 ? 'flex' : 'none';
            potonganEl.innerText = fmt(potongan);

            const setelah = subtotal - potongan;
            const layanan = biayaFlat + Math.ceil(setelah * biayaPersen);
            const total   = setelah + layanan;

            subtotalEl.innerText     = fmt(subtotal);
            biayaLayananEl.innerText = fmt(layanan);
            totalEl.innerText        = fmt(total);
        }

        document.getElementById('btn-minus')?.addEventListener('click', () => {
            if (parseInt(quantityInput.value) > 1) { quantityInput.value--; updateTotal(); }
        });

        document.getElementById('btn-plus')?.addEventListener('click', () => {
            if (parseInt(quantityInput.value) < maxStok) { quantityInput.value++; updateTotal(); }
        });

        voucherBtn?.addEventListener('click', () => {
            const kode = voucherInput.value.trim();
            if (!kode) return;
            voucherBtn.disabled = true;
            voucherBtn.textContent = '...';
            voucherMsg.textContent = '';
            voucherMsg.className = 'voucher-msg';

            fetch('cek_voucher.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    kode_voucher: kode,
                    cart: [{ kategori_id: kategoriId, harga: hargaSatuan, qty: parseInt(quantityInput.value) }]
                })
            })
            .then(r => r.json())
            .then(data => {
                voucherMsg.className = 'voucher-msg ' + (data.success ? 'success' : 'error');
                voucherMsg.textContent = data.success ? '✓ ' + data.message : '✕ ' + data.message;

                if (data.success) {
                    voucher = { tipe: data.tipe, nilai: data.nilai };
                    voucherInput.readOnly = true;
                    voucherBtn.textContent = 'Oke';
                    // Tandai voucher yang dipakai di list
                    document.querySelectorAll('.voucher-item').forEach(el => {
                        if (el.dataset.kode === kode) {
                            el.classList.add('used');
                            el.querySelector('.voucher-item-btn').textContent = '✓ Dipakai';
                        }
                    });
                } else {
                    voucher = { tipe: null, nilai: 0 };
                    voucherBtn.disabled = false;
                    voucherBtn.textContent = 'Pakai';
                }
                updateTotal();
            })
            .catch(() => {
                voucherMsg.className = 'voucher-msg error';
                voucherMsg.textContent = '✕ Terjadi kesalahan.';
                voucherBtn.disabled = false;
                voucherBtn.textContent = 'Pakai';
            });
        });

        updateTotal();

        // — Load Voucher Aktif —
        fetch('get_voucher_publik.php')
            .then(r => r.json())
            .then(data => {
                const list = document.getElementById('voucher-list');
                if (!data.length) {
                    list.innerHTML = '<div class="voucher-empty">Tidak ada voucher tersedia saat ini</div>';
                    return;
                }
                list.innerHTML = data.map(v => {
                    const diskon = v.tipe_diskon === 'persen'
                        ? `Diskon ${v.nilai_diskon}%`
                        : `Diskon Rp ${parseInt(v.nilai_diskon).toLocaleString('id-ID')}`;
                    const exp = v.tanggal_kadaluarsa
                        ? `Berlaku s/d ${v.tanggal_kadaluarsa}`
                        : 'Berlaku selamanya';
                    return `
                        <div class="voucher-item" data-kode="${v.kode_voucher}" onclick="pakaiVoucher('${v.kode_voucher}')">
                            <div class="voucher-item-left">
                                <span class="voucher-item-code"><i class="fas fa-ticket-alt"></i> ${v.kode_voucher}</span>
                                <span class="voucher-item-desc">${diskon}</span>
                                <span class="voucher-item-exp"><i class="fas fa-clock"></i> ${exp}</span>
                            </div>
                            <span class="voucher-item-btn">Pakai</span>
                        </div>`;
                }).join('');
            })
            .catch(() => {
                const list = document.getElementById('voucher-list');
                if (list) list.innerHTML = '<div class="voucher-empty">Gagal memuat voucher</div>';
            });
    }

    // — Auto-fill & apply voucher dari list —
    window.pakaiVoucher = function(kode) {
        const voucherInput = document.getElementById('kode_voucher');
        const voucherBtn   = document.getElementById('btn-apply-voucher');
        if (voucherInput.readOnly) return;
        voucherInput.value = kode;
        voucherBtn.click();
    };

    // — Modals —
    function openModal(id)  { document.getElementById(id)?.classList.add('open'); }
    function closeModal(id) { document.getElementById(id)?.classList.remove('open'); }

    document.getElementById('open-modal-btn')?.addEventListener('click', () => openModal('order-guide-modal'));

    document.querySelectorAll('.modal-overlay').forEach(mo => {
        const closeBtn = mo.querySelector('.modal-close');
        closeBtn?.addEventListener('click', () => mo.classList.remove('open'));
        mo.addEventListener('click', e => { if (e.target === mo) mo.classList.remove('open'); });
    });

    // — Confirmation before submit —
    const paymentForm  = document.getElementById('payment-form-actual');
    const confirmModal = document.getElementById('confirmation-modal');

    paymentForm?.addEventListener('submit', e => {
        e.preventDefault();
        openModal('confirmation-modal');
    });

    document.getElementById('confirm-yes-btn')?.addEventListener('click', () => paymentForm.submit());
    document.getElementById('confirm-no-btn')?.addEventListener('click', () => closeModal('confirmation-modal'));
});
</script>
</body>
</html>