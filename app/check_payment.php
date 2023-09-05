<?php

include_once "config.php";
include_once "functions.php";

$payments = getUnconfirmedPayments();

foreach ($payments as $payment) {

    $addr = getAddressFromPayment($payment['txid']);

    $api_url_payment_check = "https://www.blockonomics.co/api/tx_detail?txid=" . $payment['txid'];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url_payment_check);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $blockonomics_api_key));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $output = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($output, true);

    if ($json['status'] == "Confirmed") {
        updatePaymentStatus($payment['txid'], 2);
        echo "Payment confirmed for " . $payment['txid'] . "<br>";
    } else {
        echo "Payment not confirmed for " . $payment['txid'] . "<br>";
    }
    sleep(1);
}
