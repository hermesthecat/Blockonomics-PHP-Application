<?php

include_once "config.php";
include_once "functions.php";

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
    <link rel="stylesheet" href="css/style.css">
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
        <div class="row">
            <h1 style="width:100%;">Previous purchases</h1>
            <?php
            $ip = getIp();
            //$sql = "SELECT * FROM `orders` WHERE `ip`='$ip' ORDER BY `id` DESC";
            $sql = "SELECT * FROM `invoices` ORDER BY `id` DESC";
            $result = mysqli_query($conn, $sql);
            // Check number of orders
            if (mysqli_num_rows($result) == 0) {
                // No previous orders
            ?>
                <p>No previous orders.</p>
            <?php
            } else {
            ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Invoice Code</th>
                            <th scope="col">Address</th>
                            <th scope="col">Status</th>
                            <th scope="col">Price</th>
                            <th scope="col">Amount Paid</th>
                            <th scope="col">Product</th>
                            <th scope="col">IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <th scope="row"><?php echo $row['code']; ?></th>
                                <td><a href="invoice.php?code=<?php echo $row['code']; ?>"><?php echo $row['address']; ?></a></td>
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
                                <td><?php echo '$' . $row['price']; ?></td>
                                <td><?php echo formatBTC(convertToBTCFromSatoshi(getTotalInvoincePayments($row['address']))); ?></td>
                                <td><?php echo getProduct(getInvoiceProduct($row['code'])) ?></td>
                                <td><?php echo $row['ip']; ?></td>
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


    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</body>

</html>