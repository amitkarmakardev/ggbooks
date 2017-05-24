<?php

require 'settings.php';
require 'kint-master/Kint.class.php';
require 'generateISBN.php';
require 'XMLParser.php';
require 'simple_html_dom.php';

$first = readline("Enter starting ISBN no: ");
$last = readline("Enter end ISBN no: ");

if (strlen($first) < 10 && strlen($last) < 10) {
    die("Please provide 10 or 13 digit ISBN nos");
}

$isbnarray = makeISBNarray($first, $last);

foreach ($isbnarray as $sfbisbn) {

    if (!checkPageExists($sfbisbn)) {
        d("{$sfbisbn} book does not exist");
        continue;
    }

    $book_data = [];
    $pageContent = searchISBN($sfbisbn);

    $dom = new simple_html_dom();
    $dom->load($pageContent);

    foreach ($dom->find("#metadata_content_table tr[class='metadata_row']") as $element) {

        $label = trim($element->children(0)->plaintext);
        $value = processString($element->children(1)->plaintext);

        switch (strtoupper($label)) {
            case "TITLE":
                $book_data['title'] = processString($value);
                break;
            case "AUTHOR":
                $book_data['author'] = $value;
                break;
            case "EDITION":
                $book_data['edition'] = $value;
                break;
            case "PUBLISHER":
                $book_data['publisher'] = processString(explode(",", $value)[0]);
                $book_data["publish_year"] = processString(explode(",", $value)[1]);
                break;
            case "ISBN":
                $isbn10 = trim(explode(",", $value)[0]);
                $isbn13 = trim(explode(",", $value)[1]);
                $book_data['isbn10'] = $isbn10;
                $book_data['isbn13'] = $isbn13;
                break;
            case "LENGTH":
                $book_data['page_length'] = $value;
                break;
            case "SUBJECTS":
                $book_data['subjects'] = $value;
                break;
            default:
                break;
        }

    }

    $price = str_replace('Buy eBook - ', '', getaccessinfo($dom));
    if (strtoupper($price) != "GET PRINT BOOK") {
        $book_data['price'] = $price;
    }

    d($book_data);

//    insertToDB($book_data);

}

function checkPageExists($isbn_string)
{
    $exists = false;

    $handle = curl_init(formURL($isbn_string));

    curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);

    /* Get the HTML or whatever is linked in $url. */
    curl_exec($handle);

    /* Check for 404 (file not found). */
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    if ($httpCode == 200) {
        $exists = true;
    }
    curl_close($handle);

    return $exists;

}


function processString($data)
{
    return str_replace(array("\n", "\r"), '', html_entity_decode(strip_tags(trim($data))));
}

function formURL($isbn_string)
{
    // Google Books URL Of Book with given ISBN
    $url = "https://books.google.com/books?vid=ISBN*isbn_is_here*&hl=en";
    return str_replace("*isbn_is_here*", $isbn_string, $url);
}

// Function that takes ISBN as parameter, returns Google Books Page content of the given book.
function searchISBN($isbn_string)
{
    // Page content of Google Books URL of the book
    $page_content = file_get_contents(formURL($isbn_string));
    return $page_content;

}


function getaccessinfo($dom)
{
    return processString($dom->find('#gb-get-book-content', 0)->plaintext);
}
