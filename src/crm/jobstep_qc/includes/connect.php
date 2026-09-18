<?php
require_once 'env.php';

Global $db;
$db = new PDO("mysql:host=$envConfig->DB_HOST;port=$envConfig->DB_PORT;dbname=$envConfig->DB_DATABASE", $envConfig->DB_USER, $envConfig->DB_PASSWORD);



$db->query('set character_set_client=utf8');
$db->query('set character_set_connection=utf8');
$db->query('set character_set_results=utf8');
$db->query('set character_set_server=utf8');

?>
