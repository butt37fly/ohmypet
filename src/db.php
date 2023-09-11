<?php

$db_host = "localhost";
$db_name = "ohmypet";
$db_username = "root";
$db_password = "";
$db_options = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, 'charset'=>'utf8mb4' ];

try{
  $pdo = new PDO( "mysql:host=$db_host;dbname=$db_name", $db_username, $db_password, $db_options );
  $pdo->exec("SET NAMES utf8mb4");
  
} catch( \Throwable $e){
  echo $e;
}