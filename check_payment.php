<?php

set_time_limit(0);

include_once "config.php";
include_once "functions.php";

$payments = getUnconfirmedPayments();

if (empty($payments)) {
    echo "No payments to check";
    exit();
}

echo "<hr>";

foreach ($payments as $payment) {

    echo "Checking payment for " . $payment['payment_txid'] . "<br>";

    $api_url_payment_check = "https://www.blockonomics.co/api/tx_detail?txid=" . $payment['payment_txid'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url_payment_check);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $apikey));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $output = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($output, true);

    if (!empty($json['status']) && $json['status'] == "Confirmed") {
        updatePaymentStatus($payment['payment_txid'], 2);
        echo "<b>Payment confirmed</b> for " . $payment['payment_txid'] . "<br>";
        echo "<hr>";
    } else if (empty($json['status'])) {
        echo "<b>Payment TXID does not exist </b> for " . $payment['payment_txid'] . "<br>";
        echo "<hr>";
    } else {
        echo "<b>Payment not confirmed</b> for " . $payment['payment_txid'] . "<br>";
        echo "<hr>";
    }
}
