<?php

include_once "config.php";
include_once "functions.php";

if (!isset($_GET['id'])) {
    exit();
}
$id = mysqli_real_escape_string($conn, $_GET['id']);

$price = getPrice($id);

$code = createInvoice($id, $price);

echo "<script>window.location='invoice.php?code=" . $code . "'</script>";
