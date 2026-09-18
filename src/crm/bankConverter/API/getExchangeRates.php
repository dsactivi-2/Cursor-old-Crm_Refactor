<?php
include_once 'functions.php';
include_once 'vendor/autoload.php';

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

echo json_encode(getExchangeRates());
