<?php

include("src/dbConfig.php");
include("src/RequestResponseLoggerMiddleware.php");

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

$requestResponseLogger = new RequestResponseLoggerMiddleware($database);

// Log the request before processing
$requestData = $requestResponseLogger->logRequest();

$responseData = $controller->processRequest($method, $endpoint);

// Log the response after processing
$requestResponseLogger->logResponse($requestData, $responseData);