<?php

include_once "config.php";
include_once "functions.php";

$secretlocal = "tyututyuytuytuytut";

$status = 0;
$txid = $_GET['txid'];
$value = $_GET['value'];
$status = $_GET['status'];
$addr = $_GET['addr'];
$secret = $_GET['secret'];

if (empty($txid) || empty($value) || empty($addr) || empty($secret)) {
    exit();
}

if ($secret != $secretlocal) {
    exit();
}

if ($txid == 'WarningThisIsAGeneratedTestPaymentAndNotARealBitcoinTransaction') {
    $txid = 'WarningThisIsATestTransaction - ' . $addr;
} else {
    $txid = $txid . " - " . $addr;
}

$sql = "INSERT INTO `payments` (`txid`, `value`, `addr`, `status`) VALUES ('$txid', '$value', '$addr', '$status')";
mysqli_query($conn, $sql);

$code = getCode($addr);
$price = getInvoicePrice($code);

$price = USDtoBTC($price);
$price = $price * 100000000;

$total_amount_paid_satoshi = getTotalInvoincePayments($addr);
if ($total_amount_paid_satoshi >= round($price)) {
    updateInvoiceStatus($code, 2);
    exit();
}

if ($status < 0) {
    exit();
}

if ($value >= round($price)) {
    updateInvoiceStatus($code, $status);
    if ($status == 2) {
        $invoice = getInvoice($addr);
        createOrder($invoice, getInvoiceIp($addr));
    }
} else {
    updateInvoiceStatus($code, -2);
}
