<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// locationHandler.php
include '../config/dataBaseConnect.php';
include './locationHelpers.php';


if (isset($_GET['action']) && $_GET['action'] === 'getCountries') {
    echo json_encode(getCountries($connection));
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'getStates' && isset($_GET['country_id'])) {
    $countryId = $_GET['country_id'];
    echo json_encode(getStates($connection, $countryId));
    exit;
}


?>