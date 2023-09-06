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
        <div class="row align-items-center justify-content-center">
            <h1 style="width:100%;">Orders</h1>
            <?php
            $sql = "SELECT * FROM `orders` ORDER BY `id` DESC";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) == 0) {
            ?>
                <p>No orders.</p>
            <?php
            } else {
            ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Order ID</th>
                            <th scope="col">Invoice ID</th>
                            <th scope="col">IP</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <th scope="row"><?php echo $row['order_id']; ?></th>
                                <td><a href="invoice.php?invoice_id=<?php echo $row['invoice_id']; ?>"><?php echo $row['invoice_id']; ?></a></td>
                                <td><?php echo $row['buyer_ip']; ?></td>
                                <td><?php echo $row['created_at']; ?></td>
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