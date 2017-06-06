<?php

function generateBookDetails($isbn10)
{
    $book_data = [];
    $benchmarks = [];

    echo PHP_EOL . "Generate: $isbn10" . PHP_EOL;
    echo "-----------------------------------------".PHP_EOL;

    $exists = checkIfExistsInDB('book_details', 'isbn10', $isbn10);

    if ($exists) {
        echo "ISBN $isbn10 already exists in the databse" . PHP_EOL;
        return;
    }

    $start = startBenchMarking();

    if (!checkPageExists(formURL($isbn10))) {
        $benchmarks['Page does not exist'] = stopBenchmarking($start);
        $book_data = ['isbn10' => $isbn10];
    } else {
        $benchmarks['Page exists'] = stopBenchmarking($start);

        $start = startBenchMarking();
        $pageContent = searchISBN($isbn10);
        $benchmarks['Load page content'] = stopBenchmarking($start);

        $dom = new simple_html_dom();
        $dom->load($pageContent);

        $start = startBenchMarking();
        foreach ($dom->find("#metadata_content_table tr[class='metadata_row']") as $element) {
            $label = trim($element->children(0)->plaintext);
            $value = processString(utf8_encode($element->children(1)->plaintext));

            switch (strtoupper($label)) {
                case "TITLE":
                    $book_data['title'] = processString($value);
                    break;
                case "AUTHOR":
                    $book_data['author'] = $value;
                    break;
                case "AUTHORS":
                    $book_data['author'] = $value;
                    break;
                case "EDITION":
                    $book_data['edition'] = $value;
                    break;
                case "PUBLISHER":
                    $book_data["publish_year"] = substr(processString($value), -4);
                    $book_data['publisher'] = trim(trim(str_replace($book_data["publish_year"], "", processString($value))), ",");
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
        $price = str_replace('Buy eBook - ', '', getPrice($dom));
        if (strtoupper($price) != "GET PRINT BOOK") {
            $book_data['price'] = $price;
        }
        $benchmarks['Scrape data'] = stopBenchmarking($start);
    }

    $start = startBenchMarking();
    insertToDB('book_details', $book_data);
    $benchmarks['Insert to database'] = stopBenchmarking($start);

    printBenchmark($benchmarks);
}


function formURL($isbn_string)
{
    // Google Books URL Of Book with given ISBN
    $url = "https://books.google.co.in/books?vid=ISBN*isbn_is_here*&hl=en&redir_esc=y";
    return str_replace("*isbn_is_here*", $isbn_string, $url);
}

// Function that takes ISBN as parameter, returns Google Books Page content of the given book.
function searchISBN($isbn_string)
{
    $url = formURL($isbn_string);
    return getHtmlContent($url);
}


function getPrice($dom)
{
    return processString($dom->find('#gb-get-book-content', 0)->plaintext);
}
