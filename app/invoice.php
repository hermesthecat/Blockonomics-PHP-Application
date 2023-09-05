<?php

include_once "config.php";
include_once "functions.php";

if (!isset($_GET['code'])) {
    exit();
}

// check if invoice status is paid or not

if (isset($_GET['code'])) {
    $code = mysqli_escape_string($conn, $_GET['code']);
    $status = getStatus($code);
    if ($status == 2) {
        $paid_status = true;
        $address = getAddressFromInvoice($code);
    } else {
        $paid_status = false;
        $address = getAddress($code);
        $status = getStatus($code);
        $price = getInvoicePrice($code);
    }
}

$product = getInvoiceProduct($code);

$total_amount_paid_satoshi = getTotalInvoincePayments($address);

$price_satoshi = convertToSatoshiFromBTC(round(USDtoBTC($price), 8));

$missing_amount_satoshi = $price_satoshi - $total_amount_paid_satoshi;

$missing_amount_btc = formatBTC(convertToBTCFromSatoshi($missing_amount_satoshi));

$total_amount_paid_btc = formatBTC(convertToBTCFromSatoshi($total_amount_paid_satoshi));

$price_btc = formatBTC(convertToBTCFromSatoshi($price_satoshi));

$statusval = $status;

$info = "";
if ($status == 0) {
    $status = "<span style='color: orangered' id='status'>PENDING</span>";
    $info = "<p>You payment has been received. Invoice will be marked paid on two blockchain confirmations.</p>";
} else if ($status == 1) {
    $status = "<span style='color: orangered' id='status'>PENDING</span>";
    $info = "<p>You payment has been received. Invoice will be marked paid on two blockchain confirmations.</p>";
} else if ($status == 2) {
    $status = "<span style='color: green' id='status'>PAID</span>";
} else if ($status == -1) {
    $status = "<span style='color: red' id='status'>UNPAID</span>";
} else if ($status == -2) {
    $status = "<span style='color: red' id='status'>Missing amount. Please complete payment amount.<br>
    Price Amount: $price_btc<br>
    Total Payment Amount: $total_amount_paid_btc<br>
    Missing Payment Amount: $missing_amount_btc</span>";
} else {
    $status = "<span style='color: red' id='status'>Error, expired payment link.</span>";
}


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
    <link rel="stylesheet" href="css/style.css?<?php $random = rand(100000000, 999999999);
                                                echo $random; ?>">
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
                </ul>

            </div>
        </div>
    </nav>

    <!-- Invoice -->

    <main>
        <div class="row text-center">
            <h2 style="width:100%;">Invoice Code: <?php echo $code ?></h2>

            <?php if ($paid_status) : ?>
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                </svg>
            <?php endif; ?>

            <?php if (!$paid_status) : ?>
                <p style="display:block;width:100%;">Please pay <?php echo round(USDtoBTC($price), 8); ?> BTC to address: <span id="address"><?php echo $address; ?></span></p>
                <?php
                // QR code generation using google apis
                $cht = "qr";
                $chs = "300x300";
                $chl = $address;
                $choe = "UTF-8";

                $qrcode = 'https://chart.googleapis.com/chart?cht=' . $cht . '&chs=' . $chs . '&chl=' . $chl . '&choe=' . $choe;
                ?>
                <div class="qr-hold">
                    <img src="<?php echo $qrcode ?>" alt="My QR code" style="width:250px;">
                </div>
            <?php endif; ?>

            <p style="display:block;width:100%;">Status: <?php echo $status; ?></p>
            <?php echo $info; ?>
            <div id="info"></div>
            <h4 style="width:100%;margin-top: 20px;">Product: <?php echo getProduct($product); ?></h4>
            <p style="display:block;width:100%;"><?php echo getDescription($product); ?></p>
        </div>

        <hr>

        <div class="row text-center">
            <h2 style="width:100%;">Payments for Invoince: <?php echo $code ?></h2>
            <?php
            $sql = "SELECT * FROM `payments` WHERE addr = '$address' ORDER BY `id` DESC";
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
                            <th scope="col">Invoice Code</th>
                            <th scope="col">TXID</th>
                            <th scope="col">Address</th>
                            <th scope="col">Amount Paid</th>
                            <th scope="col">Status</th>
                            <th scope="col">Check</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <th><?php echo $code; ?></th>
                                <td><?php echo $row['txid']; ?></td>
                                <td><?php echo $row['addr']; ?></td>
                                <td><?php echo formatBTC(convertToBTCFromSatoshi($row['value'])); ?></td>
                                <td><?php
                                    $status = $row['status'];
                                    if ($status == 0) {
                                        $status = "<span style='color: orangered' id='status'>Payment received, Awaiting Confirmation.</span>";
                                    } else if ($status == 1) {
                                        $status = "<span style='color: orangered' id='status'>Payment received, Awaiting Confirmation.</span>";
                                    } else if ($status == 2) {
                                        $status = "<span style='color: green' id='status'>Payment Confirmed.</span>";
                                    } else if ($status == -1) {
                                        $status = "<span style='color: red' id='status'>Unpaid.</span>";
                                    } else if ($status == -2) {
                                        $status = "<span style='color: red' id='status'>Missing amount. Please complete payment amount.</span>";
                                    } else {
                                        $status = "<span style='color: red' id='status'>Error, expired payment link.</span>";
                                    }
                                    echo $status;
                                    ?></td>
                                <td><a target="_blank" href="https://www.blockonomics.co/api/tx?txid=<?php echo $row['txid'] ?>&addr=<?php echo $row['addr'] ?>">Check</a>
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
