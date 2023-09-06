<?php

include_once "config.php";

function createInvoiceId()
{
    global $conn;

    $invoice_id = rand(100000000, 999999999);

    $sql = "SELECT * FROM `invoices` WHERE `invoice_id` = '$invoice_id'";

    $result = mysqli_query($conn, $sql);
    $count = mysqli_num_rows($result);

    if ($count > 0) {
        createInvoiceId();
    } else {
        return $invoice_id;
    }
}

function createPaymentId()
{
    global $conn;

    $payment_id = rand(100000000, 999999999);

    $sql = "SELECT * FROM `payments` WHERE `payment_id` = '$payment_id'";

    $result = mysqli_query($conn, $sql);
    $count = mysqli_num_rows($result);

    if ($count > 0) {
        createPaymentId();
    } else {
        return $payment_id;
    }
}

function createOrderId()
{
    global $conn;

    $order_id = rand(100000000, 999999999);

    $sql = "SELECT * FROM `orders` WHERE `order_id` = '$order_id'";

    $result = mysqli_query($conn, $sql);
    $count = mysqli_num_rows($result);

    if ($count > 0) {
        createOrderId();
    } else {
        return $order_id;
    }
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function generateAddress()
{
    global $apikey;
    global $url;

    $options = array(
        'http' => array(
            'header'  => 'Authorization: Bearer ' . $apikey,
            'method'  => 'POST',
            'content' => '',
            'ignore_errors' => true
        )
    );

    $context = stream_context_create($options);
    $contents = file_get_contents($url . "new_address", false, $context);
    $object = json_decode($contents);

    file_put_contents('new_address.json', $contents);

    if (isset($object->address)) {
        $btc_address = $object->address;
    } else {
        $btc_address = $http_response_header[0] . "\n" . $contents;
    }

    return $btc_address;
}

function createInvoice($product_id, $invoice_price)
{
    global $conn;

    $invoice_id = createInvoiceId();
    $invoice_satoshi = convertToSatoshiFromBTC(round(USDtoBTC($invoice_price), 8));
    $btc_address = generateAddress();
    $invoice_status = -1;
    $buyer_ip = getIp();

    $sql = "INSERT INTO `invoices` (`invoice_id`, `btc_address`, `invoice_price`, `invoice_status`, `product_id`,`buyer_ip`, `invoice_satoshi`) VALUES ('$invoice_id', '$btc_address', '$invoice_price', '$invoice_status', '$product_id', '$buyer_ip', '$invoice_satoshi')";

    mysqli_query($conn, $sql);

    return $invoice_id;
}

function getProduct($product_id)
{
    global $conn;

    $sql = "SELECT * FROM `products` WHERE `id` = '$product_id'";
    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        return $row['product_name'];
    }
}

function getPrice($product_id)
{
    global $conn;

    $sql = "SELECT * FROM `products` WHERE `id` = '$product_id'";

    $result = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        return $row['product_price'];
    }
}

function getAddress($invoice_id)
{
    global $conn;

    $sql = "SELECT * FROM `invoices` WHERE `invoice_id` = '$invoice_id'";

    $result = mysqli_query($conn, $sql);
    $btc_address = "Error, try again getAddress";

    while ($row = mysqli_fetch_assoc($result)) {
        $btc_address = $row['btc_address'];
    }

    return $btc_address;
}

function getStatus($invoice_id)
{
    global $conn;

    $sql = "SELECT * FROM `invoices` WHERE `invoice_id` = '$invoice_id'";

    $result = mysqli_query($conn, $sql);
    $invoice_status = "Error, try again getStatus";

    while ($row = mysqli_fetch_assoc($result)) {
        $invoice_status = $row['invoice_status'];
    }

    return $invoice_status;
}

function getInvoiceProduct($invoice_id)
{
    global $conn;

    $sql = "SELECT * FROM `invoices` WHERE `invoice_id` = '$invoice_id'";

    $result = mysqli_query($conn, $sql);
    $product_id = "Error, try again getInvoiceProduct";

    while ($row = mysqli_fetch_assoc($result)) {
        $product_id = $row['product_id'];
    }

    return $product_id;
}

function getInvoicePrice($invoice_id)
{
    global $conn;

    $sql = "SELECT * FROM `invoices` WHERE `invoice_id` = '$invoice_id'";

    $result = mysqli_query($conn, $sql);
    $invoice_price = "Error, try again getInvoicePrice";

    while ($row = mysqli_fetch_assoc($result)) {
        $invoice_price = $row['invoice_price'];
    }

    return $invoice_price;
}

function GetInvoiceId($btc_address)
{
    global $conn;

    $sql = "SELECT * FROM `invoices` WHERE `btc_address` = '$btc_address'";

    $result = mysqli_query($conn, $sql);
    $invoice_id = "Error, try again GetInvoiceId";

    while ($row = mysqli_fetch_assoc($result)) {
        $invoice_id = $row['invoice_id'];
    }

    return $invoice_id;
}

function getDescription($product_id)
{
    global $conn;

    $sql = "SELECT * FROM `products` WHERE `id` = '$product_id'";

    $result = mysqli_query($conn, $sql);
    $product_description = "Error, try again getDescription";

    while ($row = mysqli_fetch_assoc($result)) {
        $product_description = $row['product_description'];
    }

    return $product_description;
}

function updateInvoiceStatus($invoice_id, $invoice_status)
{
    global $conn;

    $sql = "UPDATE `invoices` SET `invoice_status` = '$invoice_status' WHERE `invoice_id` = '$invoice_id'";

    mysqli_query($conn, $sql);
}

function getBTCPrice($currency)
{
    $content = file_get_contents("https://www.blockonomics.co/api/price?currency=" . $currency);
    $content = json_decode($content);
    $price = $content->price;

    return $price;
}

function BTCtoUSD($amount)
{
    $price = getBTCPrice("USD");

    return $amount * $price;
}

function USDtoBTC($amount)
{
    $price = getBTCPrice("USD");

    return $amount / $price;
}

function getInvoice($btc_address)
{
    global $conn;

    $sql = "SELECT * FROM `invoices` WHERE `btc_address` = '$btc_address'";

    $result = mysqli_query($conn, $sql);
    $invoice_id = "Error, try again getInvoice";

    while ($row = mysqli_fetch_assoc($result)) {
        $invoice_id = $row['invoice_id'];
    }

    return $invoice_id;
}

function getIp()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $buyer_ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $buyer_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $buyer_ip = $_SERVER['REMOTE_ADDR'];
    }

    return $buyer_ip;
}

function createOrder($invoice_id, $buyer_ip, $order_id)
{
    global $conn;

    $sql = "INSERT INTO `orders` (`invoice_id`, `buyer_ip`, `order_id`) VALUES ('$invoice_id', '$buyer_ip', '$order_id')";

    mysqli_query($conn, $sql);
}

function getInvoiceIp($btc_address)
{
    global $conn;

    $sql = "SELECT * FROM `invoices` WHERE `btc_address` = '$btc_address'";

    $result = mysqli_query($conn, $sql);
    $buyer_ip = "Error, try again getInvoiceIp";

    while ($row = mysqli_fetch_assoc($result)) {
        $buyer_ip = $row['buyer_ip'];
    }

    return $buyer_ip;
}

function convertToBTCFromSatoshi($satoshi)
{
    $ToBTCFromSatoshi = $satoshi / 100000000;

    return $ToBTCFromSatoshi;
}

function convertToSatoshiFromBTC($btc)
{
    $ToSatoshiFromBTC = $btc * 100000000;

    return $ToSatoshiFromBTC;
}

function formatBTC($BTC_value)
{
    if (empty($BTC_value)) return ('0.00 BTC (No Payment)');

    $formatBTC = sprintf('%.8f', $BTC_value);
    $formatBTC = rtrim($formatBTC, '0') . ' BTC';

    return $formatBTC;
}

function getTotalInvoicePayments($btc_address)
{
    global $conn;

    $payment_sql = "SELECT * FROM `payments` WHERE `btc_address` = '$btc_address'";

    $payment_result = mysqli_query($conn, $payment_sql);
    $total_payments = 0;

    while ($payment_row = mysqli_fetch_assoc($payment_result)) {
        $total_payments = $total_payments + $payment_row['paid_satoshi'];
    }

    return $total_payments;
}

function getAddressFromInvoice($invoice_id)
{
    global $conn;

    $sql = "SELECT * FROM `invoices` WHERE `invoice_id` = '$invoice_id'";

    $result = mysqli_query($conn, $sql);
    $btc_address = "Error, try again getAddressFromInvoice";

    while ($row = mysqli_fetch_assoc($result)) {
        $btc_address = $row['btc_address'];
    }

    return $btc_address;
}

function getAddressFromPayment($payment_txid)
{
    global $conn;

    $sql = "SELECT * FROM `payments` WHERE `payment_txid` = '$payment_txid'";

    $result = mysqli_query($conn, $sql);
    $btc_address = "Error, try again getAddressFromPayment";

    while ($row = mysqli_fetch_assoc($result)) {
        $btc_address = $row['btc_address'];
    }

    return $btc_address;
}

function getUnconfirmedPayments()
{
    global $conn;

    $sql = "SELECT * FROM `payments` WHERE `payment_status` != '2'";

    $result = mysqli_query($conn, $sql);
    $payments = array();

    while ($row = mysqli_fetch_assoc($result)) {
        array_push($payments, $row);
    }

    return $payments;
}

function updatePaymentStatus($payment_txid, $payment_status)
{
    global $conn;

    $sql = "UPDATE `payments` SET `payment_status` = '$payment_status' WHERE `payment_txid` = '$payment_txid'";

    mysqli_query($conn, $sql);
}

function getInvoiceSatoshi($invoice_id)
{
    global $conn;

    $sql = "SELECT * FROM `invoices` WHERE `invoice_id` = '$invoice_id'";

    $result = mysqli_query($conn, $sql);
    $invoice_satoshi = "Error, try again getInvoiceSatoshi";

    while ($row = mysqli_fetch_assoc($result)) {
        $invoice_satoshi = $row['invoice_satoshi'];
    }

    return $invoice_satoshi;
}

function getPaymentId($btc_address)
{
    global $conn;

    $sql = "SELECT * FROM `payments` WHERE `btc_address` = '$btc_address'";

    $result = mysqli_query($conn, $sql);
    $payment_id = "Error, try again getPaymentId";

    while ($row = mysqli_fetch_assoc($result)) {
        $payment_id = $row['payment_id'];
    }

    return $payment_id;
}

function getOrderId($invoice_id)
{
    global $conn;

    $sql = "SELECT * FROM `orders` WHERE `invoice_id` = '$invoice_id'";

    $result = mysqli_query($conn, $sql);
    $order_id = "Error, try again getOrderId";

    while ($row = mysqli_fetch_assoc($result)) {
        $order_id = $row['order_id'];
    }

    return $order_id;
}

function checkInvoicePaidSatoshi($invoice_id)
{
    global $conn;

    $sql = "SELECT * FROM `payments` WHERE `invoice_id` = '$invoice_id' AND `payment_status` = '2'";

    $result = mysqli_query($conn, $sql);
    $total_paid_satoshi = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $total_paid_satoshi = $total_paid_satoshi + $row['paid_satoshi'];
    }

    $invoice_satoshi = getInvoiceSatoshi($invoice_id);

    if ($total_paid_satoshi >= $invoice_satoshi) {
        return true;
    } else {
        return false;
    }
}

function checkInvoicePaid($invoice_id)
{
    global $conn;

    $sql = "SELECT * FROM `payments` WHERE `invoice_id` = '$invoice_id' AND `payment_status` != '2'";

    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        return false;
    } else {
        return true;
    }
}

function compareInvoicePriceWithUpdatedPrice($invoice_id)
{
    global $conn;

    $check_checkInvoicePaid = checkInvoicePaid($invoice_id);

    if ($check_checkInvoicePaid) {
        return;
    } else {
        $sql = "SELECT * FROM `invoices` WHERE invoice_id = '$invoice_id'";

        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $invoice_price = $row['invoice_price'];
            $invoice_satoshi = $row['invoice_satoshi'];
        }

        $get_updated_satoshi = convertToSatoshiFromBTC(round(USDtoBTC($invoice_price), 8));

        if ($get_updated_satoshi != $invoice_satoshi) {
            $sql = "UPDATE `invoices` SET `invoice_satoshi` = '$get_updated_satoshi' WHERE `invoice_id` = '$invoice_id'";

            mysqli_query($conn, $sql);
        }
    }
}

function InvoiceDetailStatusToText($invoice_status, $invoice_id, $price_btc, $total_amount_paid_btc, $missing_amount_btc)
{
    $info = "";
    if ($invoice_status == 0) {
        $invoice_status = "<span style='color: orangered' id='status'>PENDING</span>";
        $info = "<p>You payment has been received. Invoice will be marked paid on two blockchain confirmations.</p>";
    } else if ($invoice_status == 1) {
        $invoice_status = "<span style='color: orangered' id='status'>PENDING</span>";
        $info = "<p>You payment has been received. Invoice will be marked paid on two blockchain confirmations.</p>";
    } else if ($invoice_status == 2) {
        $check_invoice_paid_status = checkInvoicePaid($invoice_id);
        if ($check_invoice_paid_status) {
            $invoice_status = "<span style='color: green' id='status'>PAID</span>";
            $info = "<p>Thank you for your payment. All payments are confirmed.</p>";
        } else {
            $invoice_status = "<span style='color: orangered' id='status'>Payment received, Awaiting Confirmation.</span>";
            $info = "<p>You payment has been received. Invoice will be marked paid when all payments are confirmed.</p>";
        }
    } else if ($invoice_status == -1) {
        $invoice_status = "<span style='color: red' id='status'>UNPAID</span>";
    } else if ($invoice_status == -2) {
        $invoice_status = "<span style='color: red' id='status'>Missing amount. Please complete payment amount.<br>
        Price Amount: <b>$price_btc</b><br>
        Total Payment Amount: <b>$total_amount_paid_btc</b><br>
        Missing Payment Amount: <b>$missing_amount_btc</b></span>";
    } else {
        $invoice_status = "<span style='color: red' id='status'>Error, expired payment link.</span>";
    }
    echo "<p style='display:block;width:100%;'>Status: $invoice_status</p>";
    echo "<div id='info'>$info</div>";
}

function InvoiceDetailPaymentStatusToText($payment_status)
{
    if ($payment_status == 0) {
        $payment_status = "<span style='color: orangered' id='status'>Payment received, Awaiting Confirmation.</span>";
    } else if ($payment_status == 1) {
        $payment_status = "<span style='color: orangered' id='status'>Payment received, Partially Confirmed.</span>";
    } else if ($payment_status == 2) {
        $payment_status = "<span style='color: green' id='status'>Payment Confirmed.</span>";
    }
    echo $payment_status;
}

function InvoiceStatusToText($invoice_status, $invoice_id)
{
    if ($invoice_status == 0) {
        $invoice_status = "<span style='color: orangered' id='status'>Payment received, Awaiting Confirmation.</span>";
    } else if ($invoice_status == 1) {
        $invoice_status = "<span style='color: orangered' id='status'>Payment received, Awaiting Confirmation.</span>";
    } else if ($invoice_status == 2) {
        $check_invoice_paid_status = checkInvoicePaid($invoice_id);
        if ($check_invoice_paid_status) {
            $invoice_status = "<span style='color: green' id='status'>Payment Confirmed.</span>";
        } else {
            $invoice_status = "<span style='color: orangered' id='status'>Payment received, Awaiting Confirmation.</span>";
        }
    } else if ($invoice_status == -1) {
        $invoice_status = "<span style='color: red' id='status'>Unpaid.</span>";
    } else if ($invoice_status == -2) {
        $invoice_status = "<span style='color: red' id='status'>Missing amount.</span>";
    } else {
        $invoice_status = "<span style='color: red' id='status'>Expired.</span>";
    }

    echo $invoice_status;
}
