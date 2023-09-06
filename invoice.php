<?php

include_once "config.php";
include_once "functions.php";

if (!isset($_GET['invoice_id'])) {
    exit();
}

if (isset($_GET['invoice_id'])) {
    $invoice_id = mysqli_escape_string($conn, $_GET['invoice_id']);
    $invoice_status = getStatus($invoice_id);
    if ($invoice_status == 2) {
        $paid_status = true;
        $btc_address = getAddressFromInvoice($invoice_id);
    } else {
        $paid_status = false;
        $btc_address = getAddress($invoice_id);
    }


    $check_invoice_paid_status = checkInvoicePaid($invoice_id);
    if ($check_invoice_paid_status) {
        $check_mark = "#73AF55";
    } else {
        $check_mark = "#dbbb48";
    }
}

$check_invoice_price = compareInvoicePriceWithUpdatedPrice($invoice_id);

$invoice_price = getInvoicePrice($invoice_id);

$product_id = getInvoiceProduct($invoice_id);

$invoice_satoshi = getInvoiceSatoshi($invoice_id);

$total_amount_paid_satoshi = getTotalInvoicePayments($btc_address);

$price_satoshi = $invoice_satoshi;

$missing_amount_satoshi = $price_satoshi - $total_amount_paid_satoshi;

$missing_amount_btc = formatBTC(convertToBTCFromSatoshi($missing_amount_satoshi));

$total_amount_paid_btc = formatBTC(convertToBTCFromSatoshi($total_amount_paid_satoshi));

$price_btc = formatBTC(convertToBTCFromSatoshi($price_satoshi));

$payment_id = getPaymentId($btc_address);

$order_id = getOrderId($invoice_id);

$statusval = $invoice_status;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitcoin store</title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css?<?php echo rand(100000000, 999999999); ?>">
</head>

<body>

    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="row">
            <a class="navbar-brand" href="index.php">Bitcoin Example</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="index.php">Store <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="invoices.php">Invoices</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="orders.php">Orders</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="payments.php">Payments</a>
                    </li>
                </ul>

            </div>
        </div>
    </nav>

    <!-- Invoice -->

    <main>
        <div class="row text-center align-items-center justify-content-center">
            <h2 style="width:100%;">Invoice ID: <?php echo $invoice_id ?></h2>

            <?php if ($paid_status) : ?>
                <div class="w4rAnimated_checkmark">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                        <circle class="path circle" fill="none" stroke="<?php echo $check_mark; ?>" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1" />
                        <polyline class="path check" fill="none" stroke="<?php echo $check_mark; ?>" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 " />
                    </svg>
                </div>
                <br> <br>
            <?php endif; ?>

            <?php if (!$paid_status) : ?>
                <p style="display:block;width:100%;">Please pay <b><?php echo $price_btc; ?> </b> to address: <b><span id="address"><?php echo $btc_address; ?></b></span></p>
                <?php
                $cht = "qr";
                $chs = "300x300";
                $chl = $btc_address;
                $choe = "UTF-8";
                $qrcode = 'https://chart.googleapis.com/chart?cht=' . $cht . '&chs=' . $chs . '&chl=' . $chl . '&choe=' . $choe;
                ?>
                <div class="qr-hold text-center">
                    <img src="<?php echo $qrcode ?>" alt="My QR code" style="width:250px;">
                </div>
            <?php endif; ?>

            <?php InvoiceDetailStatusToText($invoice_status, $invoice_id, $price_btc, $total_amount_paid_btc, $missing_amount_btc); ?>

            <h4 style="width:100%;margin-top: 20px;">Product: <?php echo getProduct($product_id); ?></h4>
            <p style="display:block;width:100%;"><?php echo getDescription($product_id); ?></p>
        </div>

        <hr>

        <div class="row text-center">
            <h2 style="width:100%;">Payments for Invoice: <?php echo $invoice_id ?></h2>
            <?php
            $sql = "SELECT * FROM `payments` WHERE btc_address = '$btc_address' ORDER BY `id` DESC";
            $result = mysqli_query($conn, $sql);
            if (!$result || mysqli_num_rows($result) < 1) {
            ?>
                <p>No payment.</p>
            <?php
            } else {
            ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Payment ID</th>
                            <th scope="col">Invoice ID</th>
                            <th scope="col">Order ID</th>
                            <th scope="col">TXID</th>
                            <th scope="col">Wallet</th>
                            <th scope="col">Amount Paid</th>
                            <th scope="col">Status</th>
                            <th scope="col">Recieve</th>
                            <th scope="col">Update</th>
                            <th scope="col">Check</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <th><?php echo $row['payment_id']; ?></th>
                                <th><?php echo $invoice_id; ?></th>
                                <th><?php echo $order_id; ?></th>
                                <td><?php echo $row['payment_txid']; ?></td>
                                <td><?php echo $row['btc_address']; ?></td>
                                <td><?php echo formatBTC(convertToBTCFromSatoshi($row['paid_satoshi'])); ?></td>
                                <td><?php InvoiceDetailPaymentStatusToText($row['payment_status']); ?></td>
                                <td><?php echo $row['created_at']; ?></td>
                                <td><?php echo $row['updated_at']; ?></td>
                                <td><a target="_blank" href="https://www.blockonomics.co/api/tx?txid=<?php echo $row['payment_txid'] ?>">Check</a>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            <?php
            }
            ?>
        </div>
    </main>

    <?php if (!$paid_status) : ?>
        <script>
            var status = <?php echo $statusval; ?>

            if (status < 2) {

                var addr = document.getElementById("address").innerHTML;
                var timestamp = Math.floor(Date.now() / 1000) - 5;
                var wsuri2 = "wss://www.blockonomics.co/payment/" + addr + "?timestamp=" + timestamp;
                var socket = new WebSocket(wsuri2, "protocolOne")

                socket.onmessage = function(event) {
                    response = JSON.parse(event.data);
                    if (response.status > status)
                        setTimeout(function() {
                            window.location = window.location
                        }, 1000);
                }
            }
        </script>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</body>

</html>
