<?php

include_once "config.php";
include_once "functions.php";

$secretlocal = "your_secret";
$payment_status = 0;

$payment_txid = $_GET['txid'];
$paid_satoshi = $_GET['value'];
$payment_status = $_GET['status'];
$btc_address = $_GET['addr'];
$secret = $_GET['secret'];

$payment_id = createPaymentId();
$invoice_id = GetInvoiceId($btc_address);
$order_id = getOrderId($invoice_id);

if (empty($payment_txid) || empty($paid_satoshi) || empty($btc_address) || empty($secret)) {
    exit();
}

if ($secret != $secretlocal) {
    exit();
}

if ($payment_txid == 'WarningThisIsAGeneratedTestPaymentAndNotARealBitcoinTransaction') {
    $payment_txid = 'WarningThisIsATestTransaction - ' . $payment_id;
} else {
    $payment_txid = $payment_txid . " - " . $btc_address;
}

$sql = "INSERT INTO `payments` (`payment_txid`, `paid_satoshi`, `btc_address`, `payment_status`, `payment_id`, `invoice_id`, `order_id`) VALUES ('$payment_txid', '$paid_satoshi', '$btc_address', '$payment_status', '$payment_id', '$invoice_id', '$order_id')";
$add_payment = mysqli_query($conn, $sql);

if (!$add_payment) {
    echo mysqli_error($conn);
    exit();
}

$invoice_price = getInvoicePrice($invoice_id);
$invoice_price = USDtoBTC($invoice_price);
$invoice_price = $invoice_price * 100000000;

$total_amount_paid_satoshi = getTotalInvoicePayments($btc_address);
if ($total_amount_paid_satoshi >= round($invoice_price)) {
    updateInvoiceStatus($invoice_id, 2);
    exit();
}

if ($payment_status < 0) {
    exit();
}

if ($paid_satoshi >= round($invoice_price)) {
    updateInvoiceStatus($invoice_id, $payment_status);
} else {
    updateInvoiceStatus($invoice_id, -2);
}
