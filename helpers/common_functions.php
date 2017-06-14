<?php

function processArguments($options)
{
    global $config;

    if(array_key_exists('o', $options)){
        $config['option'] = $options['o'];
    }else{
        pDie("Please provide an option: generate / classify");
    }
    if(array_key_exists('s', $options)){
        $config['start'] = $options['s'];
    }
    if(array_key_exists('l', $options)){
        $config['limit'] = $options['l'];
    }
    if(array_key_exists('i', $options)){
        $config['default_ip'] = $options['i'];
    }
    if(array_key_exists('d', $options)){
        $config['db_credentials']['mysql_db'] = $options['d'];
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

function pDie($statement){
     die($statement.PHP_EOL);
}