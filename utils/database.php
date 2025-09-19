<?php
require_once "env-loader.php";

$host_data= $_ENV['DB_HOST'].":".$_ENV['DB_PORT'].";dbname=".$_ENV['DB_NAME'];
$username= $_ENV['DB_USER'];
$password= $_ENV['DB_PASSWORD'];

try{
    $connexion = new PDO("mysql:host=$host_data",$username,$password);
}catch(PDOException $e){
    die($e->getMessage());
}
?>