<?php

set_time_limit(0);
ini_set('memory_limit', '10024M');
error_reporting(E_ALL);
ini_set('display_errors', 1);
//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require 'kint-master/Kint.class.php';
require 'generateISBN.php';
require 'XMLParser.php';
require 'simple_html_dom.php';

//Open a new connection to the MySQL server
// Set debugging to true/false
$DEBUG = true;

//Set up mySQL username & password
$mysql_user = 'root';
$mysql_pw = 'DdumYRQSHU';
$mysql_host = 'localhost';
$mysql_db = 'CSV_DB';
//

//foreach ($argv as $value) {
//	echo "$value\n";
//	d(gettype($value));
//}

//$isbnarray = makeISBNarray (999999500,999999999);
//d($isbnarray);

$resultar = array();

$isbnarray = array("0199394466");
//$isbnarray = array("143571590X", "1449365833", "1491904992", "0596006306");

foreach ($isbnarray as $sfbisbn) {

    $book_data = [];
    $htmlContent = getbiblioinfo(searchISBN($sfbisbn));
    file_put_contents('test.html', $htmlContent);

    $dom = new simple_html_dom();
    $dom->load($htmlContent);

    foreach ($dom->find("tr[class='metadata_row']") as $element) {

        $label = trim($element->children(0)->plaintext);
        $value = processString($element->children(1)->plaintext);

        switch (strtoupper($label)) {
            case "TITLE":
                $book_data['Author'] = processString($value);
                break;
            case "AUTHOR":
                $book_data['Author'] = $value;
                break;
            case "EDITION":
                $book_data['Edition'] = $value;
                break;
            case "PUBLISHER":
                $book_data['publisher'] = $value;
                break;
            case "ISBN":
                $isbn10 = trim(explode(",", $value)[0]);
                $isbn13 = trim(explode(",", $value)[1]);
                $isbn_array = ['isbn10' => $isbn10, 'isbn13' => $isbn13];
                $book_data['isbn'] = $isbn_array;
                break;
            case "LENGTH":
                $book_data['length'] = $value;
                break;
            case "SUBJECTS":
                $book_data['subjects'] = $value;
                break;
            default:
                break;
        }

    }

    d($book_data);

    //9780007131945, 9786050913866, 978-1-891830-69-3,9780007322596

}


function processString($data)
{
    return str_replace(array("\n", "\r"), '', html_entity_decode(strip_tags(trim($data))));
}

// Function that takes ISBN as parameter, returns Google Books Page content of the given book.
function searchISBN($isbn_string)
{

    // Google Books URL Of Book with given ISBN
    $url = "https://books.google.com/books?vid=ISBN*isbn_is_here*&hl=en";
    $url = str_replace("*isbn_is_here*", $isbn_string, $url);

    // Page content of Google Books URL of the book
    $page_content = file_get_contents($url);
    return $page_content;

}

/* courtesy: https://www.codeproject.com/Tips/1074174/Simple-Way-to-Convert-HTML-Table-Data-into-PHP-Arr retrieved 28Feb17 0150hrs */
function convertHTMLtabletoarray($html)
{
    $DOM = new DOMDocument();
    $DOM->loadHTML($html);

    $Header = $DOM->getElementsByTagName('th');
    $Detail = $DOM->getElementsByTagName('td');

    /*#Get header name of the table
	foreach($Header as $NodeHeader) 
	{
		$aDataTableHeaderHTML[] = trim($NodeHeader->textContent);
	}
	//print_r($aDataTableHeaderHTML); die(); */

    //#Get row data/detail table without header name as key
    $i = 0;
    $j = 0;
    foreach ($Detail as $sNodeDetail) {
        $aDataTableDetailHTML[$j][] = trim($sNodeDetail->textContent);
        $i = $i + 1;
        $j = $i % count($aDataTableHeaderHTML) == 0 ? $j + 1 : $j;
    }
    //print_r($aDataTableDetailHTML); die();

    /*//#Get row data/detail table with header name as key and outer array index as row number
    for($i = 0; $i < count($aDataTableDetailHTML); $i++)
    {
        for($j = 0; $j < count($aDataTableHeaderHTML); $j++)
        {
            $aTempData[$i][$aDataTableHeaderHTML[$j]] = $aDataTableDetailHTML[$i][$j];
        }
    }
    $aDataTableDetailHTML = $aTempData; unset($aTempData);
    return $aDataTableDetailHTML; */

}

function getbiblioinfo($pagec)
{
    $start = '<table id="metadata_content_table">';
    $end = '</table>';
    $result = get_string_between($pagec, $start, $end);
    $result = $start . $result . $end;
    return $result;
}

function getpreviewinfo($pagec)
{
    $result = strpos($pagec, "Preview this book");
    return $result;
}

function getaccessinfo($pagec)
{
    $result = get_string_between($pagec, '"gb-get-book-content">', '</a>');
    return $result;
}

/* courtesy from http://stackoverflow.com/questions/5696412/get-substring-between-two-strings-php accessed 12Feb17 1201hrs */
function get_string_between($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}


// Function that scrapes books data from give Google Books URL page content.
function scrape_book_page($page_content)
{
    $bookArray = array();

    if (!strpos($page_content, "hl=en</code> was not found on this server.  <ins>That’s all we know.</ins>")) // If ISBN is valid & Book found
    {
        // Book Title
        $start = strpos($page_content, "<meta name=\"title\" content=\"") + 28;
        $finish = strpos($page_content, "\"/><meta name=\"description\"");
        $temp = substr($page_content, $start, $finish - $start);
        $bookArray["title"] = $temp;


        // Author
        $start = strpos($page_content, "<title>") + 7;
        $finish = strpos($page_content, "</title>");
        $temp = substr($page_content, $start, $finish - $start);


        $dataArray = explode("-", $temp);
        if (sizeof($dataArray) == 3) {

            $bookArray["author"] = $dataArray[1];
        } else if (sizeof($dataArray) == 4) {

            $bookArray["author"] = $dataArray[2];
        } else {
            $bookArray["author"] = "";
        }


        // Page count
        $start = strpos($page_content, "pages</span></td></tr><tr") - 6;
        $finish = strpos($page_content, "pages</span></td></tr><tr");
        $temp = substr($page_content, $start, $finish - $start);
        $temp = str_replace("ltr>", "", $temp);
        $temp = str_replace("tr>", "", $temp);
        $temp = str_replace("r>", "", $temp);
        $temp = str_replace(">", "", $temp);
        $bookArray["page"] = trim($temp);


        // Publisher & Date
        $start = strpos($page_content, "<span dir=ltr>Publisher</span></td><td class=\"metadata_value\"><span dir=ltr>") + 76;
        $finish = strpos($page_content, "</span></td></tr><tr class=\"metadata_row\"><td class=\"metadata_label\"><span dir=ltr>ISBN");
        $temp = substr($page_content, $start, $finish - $start);
        $bookArray["publisher"] = trim($dataArray[0]);
        $bookArray["date"] = substr(trim($dataArray[1]), 0, 4);

        // Volume ID
        $start = strpos($page_content, "?id=") + 4;
        $finish = strpos($page_content, "\"/><meta property=");
        $temp = substr($page_content, $start, $finish - $start);
        $bookArray["id"] = trim($temp);

        // ISBN10, ISBN13
        $start = strpos($page_content, "ISBN</span></td><td class=\"metadata_value\"><span dir=ltr>") + 57;
        $finish = strpos($page_content, "</span></td></tr><tr class=\"metadata_row\"><td class=\"metadata_label\"><span dir=ltr>Length");
        $temp = substr($page_content, $start, $finish - $start);
        $dataArray = explode(",", $temp);

        $bookArray["isbn10"] = "";
        $bookArray["isbn13"] = "";

        for ($i = 0; $i < sizeof($dataArray); $i++) {
            if (strlen(trim($dataArray[$i])) == 10)
                $bookArray["isbn10"] = trim($dataArray[$i]);

            if (strlen(trim($dataArray[$i])) == 13)
                $bookArray["isbn13"] = trim($dataArray[$i]);
        }

        // Preview Link
        if (strpos($page_content, "<div class=\"bookcover\"><a href=\""))
            $bookArray["preview"] = "https://books.google.com.tr/books?id=" . $bookArray["id"] . "&printsec=frontcover#v=onepage&q&f=false";
        else
            $bookArray["preview"] = "";
    } else // If ISBN is invalid or Book not found
    {
        $bookArray["title"] = "";
        $bookArray["author"] = "";
        $bookArray["publisher"] = "";
        $bookArray["date"] = "";
        $bookArray["id"] = "";
        $bookArray["isbn10"] = "";
        $bookArray["isbn13"] = "";
        $bookArray["preview"] = "";

    }

    return $bookArray;

}


function executeSQL($dbQuery1)
{
    global $mysql_host, $mysql_user, $mysql_pw, $mysql_db;
    $mysqli = new mysqli($mysql_host, $mysql_user, $mysql_pw, $mysql_db);

    //$mysqli = new mysqli('localhost','root','root','OCLC', '3306', '/Applications/MAMP/tmp/mysql/mysql.sock');
    //Output any connection error
    if ($mysqli->connect_error) {
        die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
    }

    //MySqli Insert Query
    //$mysqli->query( 'SET @@global.max_allowed_packet = ' . strlen( $dbQuery1 ) + 1024 );
    d($dbQuery1);
    $result = $mysqli->query($dbQuery1);
    print $mysqli->error;

    d($result);
    if (!$result) {
        printf("%s\n", $mysqli->error);
    }
    echo(DEBUG ? "<><><><><>" . strlen($dbQuery1) : "");
    return $result;
}


?>