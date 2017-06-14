<?php

global $config;

require "settings/bootstrap.php";
$config = require "settings/config.php";


if (count($argv) > 1) {
    $config['db_credentials']['mysql_db'] = trim($argv[1]);
}

$sql = "SHOW DATABASES LIKE '{$config['db_credentials']['mysql_db']}'";

$result = executeQuery("SHOW DATABASES LIKE '{$config['db_credentials']['mysql_db']}'")->fetchObject();

if($result->Database == $config['db_credentials']['mysql_db']){
    pLog("Database {$config['db_credentials']['mysql_db']} already exists");
    die();
}
// Create database
require __DIR__."/database/create_database.php";

foreach (glob(__DIR__ . "/migrations/*.php") as $filename) {
    $sql = require $filename;
    executeQuery($sql);
}