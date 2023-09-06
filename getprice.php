<?php
include_once "functions.php";

$invoice_id = $_REQUEST['invoice_id'];

$price = getInvoicePrice($invoice_id);

$price = USDtoBTC($price);

echo $price * 100000000;
