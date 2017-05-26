<?php

set_time_limit(0);
ini_set('memory_limit', '2048M');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'libs/kint-master/Kint.class.php';
require 'helpers/generateISBN.php';
require 'helpers/benchmark.php';
require 'libs/simple_html_dom.php';
require 'helpers/database_functions.php';
require "helpers/generateBookDetails.php";

$start = $argv[1];
$limit = $argv[2];
$outbound_ip = $argv[3];

for ($x = $start; $x <= $limit; $x++) {
    //pads string to 9 chars long
    $interimISBN = str_pad($x, 9, '0', STR_PAD_LEFT);
    $isbn_no = make10($interimISBN);
    genearteBookDetails($isbn_no);
}
