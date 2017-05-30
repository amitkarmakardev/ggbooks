<?php

function startBenchMarking(){
    return microtime(true);
}

function stopBenchmarking($start){
    $end = microtime(true);
    return $end-$start;
}

function printBenchmark($benchmark)
{
    foreach ($benchmark as $key => $value) {
        echo str_pad($key, 25) . "--> ". round($value, 4) .' seconds' . PHP_EOL;
    }
}