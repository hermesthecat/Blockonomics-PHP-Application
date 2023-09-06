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

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

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

    <!-- Products -->
    <main>
        <div class="row text-center align-items-center justify-content-center">
            <div class="product-hold text-center align-items-center justify-content-center">
                <?php
                $sql = "SELECT * FROM `products` ORDER BY `id` DESC";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <div class="product text-center align-items-center justify-content-center">
                        <div class="card" style="width: 95%;margin:0 auto;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['product_name']; ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted">$<?php echo $row['product_price']; ?></h6>
                                <p class="card-text"><?php echo $row['product_description'] ?></p>
                                <a href="buy.php?id=<?php echo $row['id']; ?>" class="card-link">Buy now</a>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</body>

</html>
