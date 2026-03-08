<?php
require_once dirname(__FILE__) . '/vendor/autoload.php';

$server_key = 'Mid-server-1X3gM-HhoCS6ZtWgj3MtKH0B'; 
$client_key = 'Mid-client-5nab2QjYEUaI7fTV'; 


\Midtrans\Config::$serverKey = $server_key;
\Midtrans\Config::$isProduction = true; 
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;
?>