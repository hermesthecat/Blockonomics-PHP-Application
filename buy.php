<?php

include_once "config.php";
include_once "functions.php";

if (!isset($_GET['id'])) {
    exit();
}
$product_id = mysqli_real_escape_string($conn, $_GET['id']);

$invoice_price = getPrice($product_id);

$buyer_ip = getIp();

$order_id = createOrderId();

$invoice_id = createInvoice($product_id, $invoice_price);

$order_create = createOrder($invoice_id, $buyer_ip, $order_id);

echo "<script>window.location='invoice.php?invoice_id=" . $invoice_id . "'</script>";
