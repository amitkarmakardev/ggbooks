<?php

function genearteBookDetails($isbn_no)
{
    $benchmarks = [];

    if (checkIfExists($isbn_no)){
        return;
    }
    
    $start = startBenchMarking();
    if (!checkPageExists($isbn_no)) {
        // echo("ISBN{$isbn_no}  - does not exist");
        $benchmarks['Page does not exist'] = stopBenchmarking($start);
        printBenchmark($isbn_no, $benchmarks);
        return;
    }
    $benchmarks['Page exists'] = stopBenchmarking($start);
    $book_data = array();
    $start = startBenchMarking();
    $pageContent = searchISBN($isbn_no);
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
    $price = str_replace('Buy eBook - ', '', getaccessinfo($dom));
    if (strtoupper($price) != "GET PRINT BOOK") {
        $book_data['price'] = $price;
    }
    $benchmarks['Scrape data'] = stopBenchmarking($start);
    $start = startBenchMarking();
    insertToDB($book_data);
    $benchmarks['Insert to database'] = stopBenchmarking($start);
    printBenchmark($isbn_no, $benchmarks);
}

function checkPageExists($isbn_string)
{
    $exists = false;
    $handle = curl_init(formURL($isbn_string));
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    /* Get the HTML or whatever is linked in $url. */
    curl_exec($handle);
    /* for 404 (file not found). */
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    if ($httpCode != 404) {
        $exists = true;
    }
    curl_close($handle);
    return $exists;
}


function processString($data)
{
    $data = preg_replace( "/\r|\n/", " ", $data);
    $data = strip_tags(trim($data));
    $data = html_entity_decode($data, ENT_QUOTES);
    $data = html_entity_decode($data);
    return $data;
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
    global $outbound_ip;
    // Bound outgoing ip
    $context = stream_context_create(array('socket'=>array('bindto'=> $outbound_ip.':0')));
    // Page content of Google Books URL of the book
    $page_content = file_get_contents(formURL($isbn_string), null, $context);
    return $page_content;
}


function getaccessinfo($dom)
{
    return processString($dom->find('#gb-get-book-content', 0)->plaintext);
}

function printBenchmark($isbn, $benchmark)
{

    echo PHP_EOL. $isbn . PHP_EOL . "--------------" . PHP_EOL;
    foreach ($benchmark as $key => $value) {
        echo str_pad($key, 25) . "--> ". $value . PHP_EOL;
    }
}
