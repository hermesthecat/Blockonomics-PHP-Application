<?php
/*
Callback location, set this in blockonmics merchant page

For testing payments locally, use this:
localhost/bitcoin/check?secret=asecretcode&addr=[ADDRESS]&status=[STATUS CODE]&txid=[TXID]&value=[Amount paid in satoshi]
*/
include_once "../config.php";
include_once "../functions.php";

$secretlocal = "asecretcode"; // Code in the callback, make sure this matches to what youve set

// Get all these values
$status = 0;
$txid = $_GET['txid'];
$value = $_GET['value'];
$status = $_GET['status'];
$addr = $_GET['addr'];
$secret = $_GET['secret'];

// Check all are set
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
//echo $sql;

// Get invoice price
$code = getCode($addr);
$price = getInvoicePrice($code);

// Convert price to satoshi for check
$price = USDtoBTC($price);
$price = $price * 100000000;

// check all invoince payments and if total payment amount is greater than or equal to price, mark as paid
// Get total amount paid
$total_amount_paid_satoshi = getTotalInvoincePayments($addr);
// Check if paid enough
if ($total_amount_paid_satoshi >= round($price)) {
    echo "Paid enough";
    // Update invoice status
    updateInvoiceStatus($code, 2);
    exit();
}

// Expired
if ($status < 0) {
    exit();
}

if ($value >= round($price)) {
    // Update invoice status
    updateInvoiceStatus($code, $status);
    if ($status == 2) {
        // Correct amount paid and fully confirmed
        // Do whatever you want here once payment is correct
        $invoice = getInvoice($addr);
        createOrder($invoice, getInvoiceIp($addr));
    }
} else {
    // Buyer hasnt paid enough
    updateInvoiceStatus($code, -2);
}
