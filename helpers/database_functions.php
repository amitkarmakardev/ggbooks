<?php

function getConnection()
{
    global $config;

    $db_credentials = $config['db_credentials'];

    $mysql_host = $db_credentials['mysql_host'];
    $mysql_db = $db_credentials['mysql_db'];
    $mysql_user = $db_credentials['mysql_user'];
    $mysql_pw = $db_credentials['mysql_pw'];

    try {
        $connection = new PDO("mysql:host=$mysql_host;dbname=$mysql_db;charset=utf8mb4", $mysql_user, $mysql_pw, [PDO::ATTR_PERSISTENT => true]);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    } catch (PDOException $exception) {
        echo $exception->getMessage();
    }
}

function checkIfExistsInDB($table, $column, $data)
{
    $exists = false;

    $connection = getConnection();
    try {
        $pstmt = $connection->prepare("SELECT count(*) as total FROM $table WHERE $column = ?");
        $pstmt->execute(array($data));
        $result = $pstmt->fetch(PDO::FETCH_ASSOC);
        if ($result['total'] > 0) {
            $exists = true;
        }
    } catch (PDOException $exception) {
        echo $exception->getMessage() . PHP_EOL;
    }

    return $exists;
}


function insertToDB($table, $data_array)
{

    $connection = getConnection();

    $columns_part = '';
    $value_part = '';

    foreach ($data_array as $key => $value) {
        $columns_part = $columns_part . $key . ",";
        $value_part = $value_part . "?,";
    }

    $columns_part = trim($columns_part, ",");
    $value_part = trim($value_part, ",");

    $sql = "INSERT INTO $table ($columns_part) VALUES ($value_part)";

    $pstmt = $connection->prepare($sql);
    try {
        $pstmt->execute(array_values($data_array));
    } catch (PDOException $exception) {
        echo $exception->getMessage() . PHP_EOL;
    }
}

function executeQuery($query)
{
    $connection = getConnection();
    try {
        return $connection->query($query);
    } catch (PDOException $exception) {
        echo $exception->getMessage() . PHP_EOL;
    }
}