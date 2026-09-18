<?php
// This is a health check file for the database connection used by New Relic
require_once('includes/env.php');
try {
    $db = new PDO("mysql:dbname=$envConfig->DB_DATABASE;host=$envConfig->DB_HOST", $envConfig->DB_USER, $envConfig->DB_PASSWORD);
    header('HTTP/1.1 200');
} catch (PDOException $e) {
    header('HTTP/1.1 500');
}

?>
