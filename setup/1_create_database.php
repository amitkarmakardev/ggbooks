<?php

$config  = require "../settings/config.php";
require "../helpers/database_functions.php";

$sql = "CREATE DATABASE ggbooks CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";

$resultset = executeQuery($sql);