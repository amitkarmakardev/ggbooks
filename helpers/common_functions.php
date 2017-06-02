<?php

function processArguments($argv)
{
    global $config;

    if (count($argv) == 2) {
        $config['option'] = $argv[1];
    }
    if (count($argv) > 3) {
        $config['start'] = $argv[2];
        $config['limit'] = $argv[3];
        if (count($argv) > 4) {
            $config['outbound_ip'] = $argv[4];
        }
    }
    die("Wrong no of inputs" .PHP_EOL);
}

function validateISBNParts($start, $limit)
{
    if (strlen($start) == 9 && strlen($limit)) {
        if (intval($start) > intval($limit)) {
            die("Start must be smaller than lilmit".PHP_EOL);
        }
    }
}
