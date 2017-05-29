<?php

set_time_limit(0);
ini_set('memory_limit', '1024M');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'libs/kint-master/Kint.class.php';
require 'libs/simple_html_dom.php';

require 'helpers/benchmark.php';
require 'helpers/database_functions.php';
require 'helpers/gbooks_scraper.php';
require 'helpers/url_functions.php';
require 'helpers/isbn_generator.php';
require 'helpers/oclc_scraper.php';


// Define global variable
$benchmarks = [];
$config = require "settings/config.php";

$outbound_ip = $config['default_ip'];

if (count($argv) < 2) {
    echo "Too few argument";
    die();
}

$start = $argv[1];
$limit = $argv[2];

if (intval($start) > intval($limit)) {
    echo "Start is greater than end!" . PHP_EOL;
    die();
}

if (count($argv) > 4) {
    $outbound_ip = $argv[3];
}



for ($isbn_part = intval($start); $isbn_part <= intval($limit); $isbn_part++) {

    $isbn10 = generateISBN10($isbn_part);

    $exists = checkIfExistsInDB('book_details', 'isbn10', $isbn10);

    if (!$exists) {
        generateBookDetails($isbn10);
    } else {
        echo "ISBN $isbn10 already exists in the databse" . PHP_EOL;
    }

    generateClassifyDetails($isbn10);

}
