<?php

global $config;

require __DIR__."/settings/bootstrap.php";

$benchmarks = [];

$options = getOpt("o:s:l:i:d:");

processArguments($options);

switch (strtoupper($config['option'])) {
    case "CLASSIFY":
        generateClassifyDataFromDatabase();
        break;
    case "GENERATE":
        generateAll();
        break;
    default:
        die("Invalid option: ".$config['option'].PHP_EOL);
}

function generateAll()
{
    global $config;
    validateISBNParts($config['start'], $config['limit']);
    $isbn = new Isbn\Isbn();
    
    for ($isbn_part = intval($config['start']); $isbn_part <= intval($config['limit']); $isbn_part++) {
        $interimISBN = str_pad($isbn_part, 9, '0', STR_PAD_LEFT);
        $isbn10 = $interimISBN.$isbn->checkDigit->make10($interimISBN);
        $isbn13 = $isbn->translate->to13($isbn10);
        generateBookDetails($isbn10, $isbn13);
        generateClassifyDetails($isbn10, $isbn13);
    }
}