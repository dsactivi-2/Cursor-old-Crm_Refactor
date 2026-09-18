<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
include("src/dbConfig.php");
spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});
set_exception_handler("ErrorHandler::handleException");

$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$uri = explode("/", $uri);
$endpoint = $uri[3];
$method = $_SERVER["REQUEST_METHOD"];

$database = new Database($db_host, $db_name, $db_user, $db_pass);

$gateway = new Gateway($database);

$controller = new Controller($gateway);

$controller->processRequest($method, $endpoint);