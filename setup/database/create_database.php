<?php

$db_credentials = $config['db_credentials'];

$sql = "CREATE DATABASE `{$db_credentials['mysql_db']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";

try {
    $conn = new PDO("mysql:host={$db_credentials['mysql_host']}", $db_credentials['mysql_user'], $db_credentials['mysql_pw']);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // use exec() because no results are returned
    $conn->exec($sql);
} catch (PDOException $e) {
    echo $sql . PHP_EOL . $e->getMessage();
}

$conn = null;