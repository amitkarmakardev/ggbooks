<?php

function startBenchMarking(){
    return microtime(true);
}

function stopBenchmarking($start){
    $end = microtime(true);
    return $end-$start;
}