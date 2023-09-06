<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

$apikey     = "";
$url        = "https://www.blockonomics.co/api/";

$options = array(
    'http' => array(
        'header'  => 'Authorization: Bearer ' . $apikey,
        'method'  => 'POST',
        'content' => '',
        'ignore_errors' => true
    )
);

$conn = mysqli_connect("localhost", "root", "", "database_name");
