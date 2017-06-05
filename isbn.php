<?php

require "settings/bootstrap.php";
$benchmarks = [];
processArguments($argv);
validateISBNParts($config['start'], $config['end']);

switch ($option) {
    case "-C":
        generateClassifyDataFromDatabase();
        break;
    case "-G":
        generateDataClassify();
        break;
    default:
        echo "Invalid option $option";
        break;
}

function generateDataClassify()
{
    for ($isbn_part = intval($config['start']); $isbn_part <= intval($config['limit']); $isbn_part++) {
        $isbn10 = generateISBN10($isbn_part);
        generateBookDetails($isbn10);
        generateClassifyDetails($isbn10);
    }
}
