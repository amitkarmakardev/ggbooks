<?php

function startBenchMarking(){
    return microtime(true);
}

function stopBenchmarking($start){
    $end = microtime(true);
    return $end-$start;
}

function printBenchmark($isbn, $benchmark)
{
    echo PHP_EOL. $isbn . PHP_EOL . "--------------" . PHP_EOL;
    foreach ($benchmark as $key => $value) {
        echo str_pad($key, 25) . "--> ". round($value, 4) .' seconds' . PHP_EOL;
    }
}