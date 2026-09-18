<?php
//Error log enabled
ini_set('display_errors', 0);
ini_set('error_log', 'error_log');
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'env.php';

if($envConfig->APP_ENV == "development")
{
    ini_set("log_errors", 1);
}

//Site url echo - vjerovatno ne treba nikad (2.2.2024 emir)
function getSiteUrl() {
  Global $envConfig;
  echo $envConfig->JOIN_URL;
}

//Site url return
function getSiteUrlr() {
  Global $envConfig;
  return $envConfig->JOIN_URL;
}

function getCRMUrl() {
  Global $envConfig;
  echo $envConfig->CRM_URL;
}

function getCRMUrlr() {
  Global $envConfig;
  return $envConfig->CRM_URL;
}

?>
