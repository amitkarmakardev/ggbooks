<?php

require "settings/bootstrap.php";

$benchmarks = [];

processArguments($argv);

validateISBNParts($start, $end);


switch ($option) {
    case "--CLASSIFY":
        generateClassifyDataFromDatabase();
        break;
    default:
        echo "Invalid option $option";
        break;
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
    generateBookDetails($isbn10);
    generateClassifyDetails($isbn10);
}
}
