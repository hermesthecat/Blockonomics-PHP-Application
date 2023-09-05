<?php

include_once "config.php";

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

    if (isset($object->address)) {
        $address = $object->address;
    } else {
        $address = $http_response_header[0] . "\n" . $contents;
    }

    return $address;
}

function createInvoice($product, $price)
{
    global $conn;
    $code = generateRandomString(25);
    $address = generateAddress();
    $status = -1;
    $ip = getIp();
    $sql = "INSERT INTO `invoices` (`code`, `address`, `price`, `status`, `product`,`ip`) VALUES ('$code', '$address', '$price', '$status', '$product', '$ip')";
    mysqli_query($conn, $sql);
    return $code;
}

function getProduct($id)
{
    global $conn;
    $sql = "SELECT * FROM `products` WHERE `id` = '$id'";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        return $row['name'];
    }
}

function getPrice($id)
{
    global $conn;
    $sql = "SELECT * FROM `products` WHERE `id` = '$id'";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        return $row['price'];
    }
}

function getAddress($code)
{
    global $conn;
    $sql = "SELECT * FROM `invoices` WHERE `code` = '$code'";
    $result = mysqli_query($conn, $sql);
    $address = "Error, try again getAddress";
    while ($row = mysqli_fetch_assoc($result)) {
        $address = $row['address'];
    }
    return $address;
}

function getStatus($code)
{
    global $conn;
    $sql = "SELECT * FROM `invoices` WHERE `code` = '$code'";
    $result = mysqli_query($conn, $sql);
    $status = "Error, try again getStatus";
    while ($row = mysqli_fetch_assoc($result)) {
        $status = $row['status'];
    }
    return $status;
}

function getInvoiceProduct($code)
{
    global $conn;
    $sql = "SELECT * FROM `invoices` WHERE `code` = '$code'";
    $result = mysqli_query($conn, $sql);
    $product = "Error, try again getInvoiceProduct";
    while ($row = mysqli_fetch_assoc($result)) {
        $product = $row['product'];
    }
    return $product;
}

function getInvoicePrice($code)
{
    global $conn;
    $sql = "SELECT * FROM `invoices` WHERE `code` = '$code'";
    $result = mysqli_query($conn, $sql);
    $price = "Error, try again getInvoicePrice";
    while ($row = mysqli_fetch_assoc($result)) {
        $price = $row['price'];
    }
    return $price;
}

function GetCode($address)
{
    global $conn;
    $sql = "SELECT * FROM `invoices` WHERE `address` = '$address'";
    $result = mysqli_query($conn, $sql);
    $code = "Error, try again GetCode";
    while ($row = mysqli_fetch_assoc($result)) {
        $code = $row['code'];
    }
    return $code;
}

function getDescription($product)
{
    global $conn;
    $sql = "SELECT * FROM `products` WHERE `id` = '$product'";
    $result = mysqli_query($conn, $sql);
    $description = "Error, try again getDescription";
    while ($row = mysqli_fetch_assoc($result)) {
        $description = $row['description'];
    }
    return $description;
}

function updateInvoiceStatus($code, $status)
{
    global $conn;
    $sql = "UPDATE `invoices` SET `status` = '$status' WHERE `code` = '$code'";
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

function getInvoice($addr)
{
    global $conn;
    $sql = "SELECT * FROM `invoices` WHERE `address` = '$addr'";
    $result = mysqli_query($conn, $sql);
    $invoice = "Error, try again getInvoice";
    while ($row = mysqli_fetch_assoc($result)) {
        $invoice = $row['code'];
    }
    return $invoice;
}

function getIp()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function createOrder($invoice, $ip)
{
    global $conn;

    $sql = "INSERT INTO `orders` (`invoice`, `ip`) VALUES ('$invoice', '$ip')";
    mysqli_query($conn, $sql);
}

function getInvoiceIp($addr)
{
    global $conn;
    $sql = "SELECT * FROM `invoices` WHERE `address` = '$addr'";
    $result = mysqli_query($conn, $sql);
    $ip = "Error, try again getInvoiceIp";
    while ($row = mysqli_fetch_assoc($result)) {
        $ip = $row['ip'];
    }
    return $ip;
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

function getTotalInvoincePayments($addr)
{
    global $conn;
    $payment_sql = "SELECT * FROM `payments` WHERE `addr` = '$addr'";
    $payment_result = mysqli_query($conn, $payment_sql);
    $total_payments = 0;
    while ($payment_row = mysqli_fetch_assoc($payment_result)) {
        $total_payments = $total_payments + $payment_row['value'];
    }
    return $total_payments;
}

// function to get address from invoince
function getAddressFromInvoice($invoice)
{
    global $conn;
    $sql = "SELECT * FROM `invoices` WHERE `code` = '$invoice'";
    $result = mysqli_query($conn, $sql);
    $address = "Error, try again getAddressFromInvoice";
    while ($row = mysqli_fetch_assoc($result)) {
        $address = $row['address'];
    }
    return $address;
}
