<?php
global $config;
require "settings/bootstrap.php";
$benchmarks = [];
processArguments($argv);
switch (strtoupper($config['option'])) {
    case "-C":
        generateClassifyDataFromDatabase();
        break;
    case "-G":
        generateAll();
        break;
    default:
        die("Invalid option: ".$config['option'].PHP_EOL);
}

function generateAll()
{
    global $config;

    validateISBNParts($config['start'], $config['limit']);

    for ($isbn_part = intval($config['start']); $isbn_part <= intval($config['limit']); $isbn_part++) {
        $isbn10 = generateISBN10($isbn_part);
        generateBookDetails($isbn10);
        generateClassifyDetails($isbn10);
    }
}
