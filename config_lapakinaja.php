<?php
// =====================================================
// config_lapakinaja.php
// Konfigurasi LapakinAja — pengganti config_midtrans.php
// =====================================================

define('LAPAKINAJA_API_ID',  '670e5a5c-91f5-4b89-81ca-f293916f54f1');   // Ganti dengan API ID dari dashboard LapakinAja
define('LAPAKINAJA_API_KEY', '91b34dc9-0c62-46d3-9815-80cb1ace50a5');  // Ganti dengan API Key dari dashboard LapakinAja
define('LAPAKINAJA_BASE_URL','https://api.lapakinaja.net');

/**
 * Buat transaksi QRIS baru via LapakinAja
 * @param int    $amount   Nominal pembayaran (sudah termasuk biaya layanan)
 * @param string $order_id ID unik pesanan
 * @return array ['code' => httpCode, 'data' => responseArray]
 */
function lapakinaja_create_transaction($amount, $order_id) {
    $payload = json_encode([
        'amount'   => (int)$amount,
        'order_id' => $order_id,
    ]);

    $ch = curl_init(LAPAKINAJA_BASE_URL . '/api/transaction/create');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'X-API-ID: '  . LAPAKINAJA_API_ID,
            'X-API-KEY: ' . LAPAKINAJA_API_KEY,
        ],
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'data' => json_decode($response, true),
    ];
}