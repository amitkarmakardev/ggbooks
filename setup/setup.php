<?php

global $config;

require __DIR__."/../settings/bootstrap.php";
$config = require __DIR__."/../settings/config.php";


if (count($argv) > 1) {
    $config['db_credentials']['mysql_db'] = trim($argv[1]);
}

// Create database
require __DIR__."/database/create_database.php";

foreach (glob(__DIR__ . "/migrations/*.php") as $filename) {
    $sql = require $filename;
    executeQuery($sql);
}