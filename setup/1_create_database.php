<?php

$config  = require "../settings/config.php";
require "../helpers/database_functions.php";

global $config;

$db_credentials = $config['db_credentials'];

$mysql_host = $db_credentials['mysql_host'];
$mysql_db = $db_credentials['mysql_db'];
$mysql_user = $db_credentials['mysql_user'];
$mysql_pw = $db_credentials['mysql_pw'];

$sql = "CREATE DATABASE ggbooks CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";


try {
    $conn = new PDO("mysql:host=$mysql_host", $mysql_user, $mysql_pw);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // use exec() because no results are returned
    $conn->exec($sql);
    echo "Database created successfully".PHP_EOL;
    }
catch(PDOException $e)
    {
    echo $sql . "<br>" . $e->getMessage();
    }

$conn = null;