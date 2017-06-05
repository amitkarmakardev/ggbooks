<?php

function processArguments($argv)
{
    global $config;
    if(count($argv) < 2 || count($argv) == 3 || count($argv) > 5){
        die("Wrong no of inputs" .PHP_EOL);        
    }
    if (count($argv) == 2) {
        $config['option'] = $argv[1];
    }
    if (count($argv) > 3) {
        $config['option'] = $argv[1];
        $config['start'] = $argv[2];
        $config['limit'] = $argv[3];
        if (count($argv) > 4) {
            $config['outbound_ip'] = $argv[4];
        }
    }
}

function validateISBNParts($start, $limit)
{
    if (strlen($start) == 9 && strlen($limit) == 9) {
        if (intval($start) > intval($limit)) {
            die("Start must be smaller than lilmit".PHP_EOL);
        }
    } else {
        die("ISBN part should be 9 digits long".PHP_EOL);
    }
}