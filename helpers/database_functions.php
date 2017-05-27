<?php

require "settings/database_credentials.php";

function getConnection()
{
    global $mysql_host, $mysql_user, $mysql_pw, $mysql_db;

    try {
        $conn = new PDO("mysql:host=$mysql_host;dbname=$mysql_db;charset=utf8mb4", $mysql_user, $mysql_pw);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    return $conn;
}

function checkIfExists($isbn)
{
    global $table_name;

    $exists = false;

    $conn = getConnection();

    try {
        $sql = $conn->prepare("SELECT COUNT(*) AS `total` FROM {$table_name} WHERE isbn10 = :isbn10");
        $sql->execute(array(':isbn10' => $isbn));
        $result = $sql->fetchObject();
        if ($result->total > 0) {
            $exists = true;
        }

    } catch (PDOException $exception) {
        echo $exception->getMessage() . PHP_EOL;
    }

    $conn = null;

    return $exists;
}

function insertToDB($book_details)
{
    global $table_name;

    $name_part = '';
    $value_part = '';

    $conn = getConnection();

    foreach ($book_details as $key => $value) {
        $name_part = $name_part . $key . ",";
        $value_part = $value_part . "'" . addslashes($value) . "',";
    }

    $name_part = trim($name_part, ",");
    $value_part = trim($value_part, ",");

    $sql = "INSERT INTO {$table_name} (" . $name_part . ") SELECT * FROM (SELECT {$value_part}) AS tmp WHERE NOT EXISTS ( SELECT isbn10 FROM {$table_name} WHERE isbn10 = '{$book_details['isbn10']}' ) LIMIT 1;";

    try {
        $conn->exec($sql);
    } catch (PDOException $e) {
        echo $sql . PHP_EOL . $e->getMessage();
    }

    $conn = null;
}
