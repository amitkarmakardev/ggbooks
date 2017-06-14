<?php

function processArguments($argv)
{
    global $config;
    if (count($argv) < 2 || count($argv) == 3 || count($argv) > 6) {
        die("Wrong no of inputs" . PHP_EOL);
    }
    if (count($argv) == 2) {
        $config['option'] = $argv[1];
    }
    if (count($argv) > 3) {
        $config['option'] = $argv[1];
        $config['start'] = $argv[2];
        $config['limit'] = $argv[3];
        if (count($argv) > 4) {
            $config['default_ip'] = $argv[4];
            if(count($argv) > 5){
                $config['db_credentials']['mysql_db'] = $argv[5];
            }
        }
    }
}

function validateISBNParts($start, $limit)
{
    if (strlen($start) == 9 && strlen($limit) == 9) {
        if (intval($start) > intval($limit)) {
            die("Start must be smaller than limit" . PHP_EOL);
        }
    } else {
        die("ISBN part should be 9 digits long" . PHP_EOL);
    }
}

function pLog($statement){
    echo $statement.PHP_EOL;
}