<?php
set_time_limit(0);
ini_set('memory_limit', '10024M');
error_reporting(E_ALL);
ini_set('display_errors',1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require 'kint-master/Kint.class.php';
require 'generateISBN.php';
//Open a new connection to the MySQL server
// Set debugging to true/false
$DEBUG = true;

//Set up mySQL username & password
$mysql_user = 'root';
$mysql_pw = 'DdumYRQSHU';
$mysql_host = 'localhost';
$mysql_db = 'CSV_DB';
//

foreach ($argv as $value) {
	echo "$value\n";
	d(gettype($value));
}

$isbnarray = makeISBNarray (999999500,999999999);
d($isbnarray);

$resultar = array();

$isbnarray = array("143571590X", "1449365833", "1491904992", "0596006306");

foreach ($isbnarray as $sfbisbn) {
	// Print Array
	d($sfbisbn);
$htmlresult = getbiblioinfo(searchISBN($sfbisbn));
$htmlinarray = XMLParser::HTMLtoArrayViaJSON($htmlresult);
d($htmlresult, $htmlinarray);
$result[$sfbisbn][] = $htmlresult;
$result[$sfbisbn][] = getpreviewinfo(searchISBN($sfbisbn));
$result[$sfbisbn][] = getaccessinfo(searchISBN($sfbisbn));
d($result);
//9780007131945, 9786050913866, 978-1-891830-69-3,9780007322596

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
function convertHTMLtabletoarray ($html) {
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
	foreach($Detail as $sNodeDetail) 
	{
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

function getbiblioinfo($pagec) {
	$start = '<table id="metadata_content_table">';
	$end = '</table>';
	$result = get_string_between($pagec, $start, $end);
	$result = $start . $result . $end;
	return $result;
}

function getpreviewinfo($pagec) {
	$result = strpos($pagec, "Preview this book");
	return $result;
}

function getaccessinfo($pagec) {
	$result = get_string_between($pagec, '"gb-get-book-content">', '</a>');
	return $result;
}

/* courtesy from http://stackoverflow.com/questions/5696412/get-substring-between-two-strings-php accessed 12Feb17 1201hrs */
function get_string_between($string, $start, $end){
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

if(!strpos($page_content, "hl=en</code> was not found on this server.  <ins>That’s all we know.</ins>")) // If ISBN is valid & Book found
{
	// Book Title
	$start=strpos($page_content,"<meta name=\"title\" content=\"") +28;
	$finish=strpos($page_content,"\"/><meta name=\"description\"");
	$temp = substr($page_content, $start,$finish-$start);
	$bookArray["title"] =  $temp;


	// Author
	$start=strpos($page_content,"<title>") +7;
	$finish=strpos($page_content,"</title>");
	$temp = substr($page_content, $start,$finish-$start);


	$dataArray = explode("-",$temp);
	if(sizeof($dataArray)==3)
	{

		$bookArray["author"] = $dataArray[1];
	}
	else if(sizeof($dataArray)==4)
	{

		$bookArray["author"] = $dataArray[2];
	}
	else
	{
		$bookArray["author"] = "";
	}





	// Page count
	$start=strpos($page_content,"pages</span></td></tr><tr")-6;
	$finish=strpos($page_content,"pages</span></td></tr><tr");
	$temp = substr($page_content, $start,$finish-$start);
	$temp =str_replace("ltr>", "", $temp);
	$temp =str_replace("tr>", "", $temp);
	$temp =str_replace("r>", "", $temp);
	$temp =str_replace(">", "", $temp);
	$bookArray["page"] = trim($temp);


	// Publisher & Date
	$start=strpos($page_content,"<span dir=ltr>Publisher</span></td><td class=\"metadata_value\"><span dir=ltr>")+76;
	$finish=strpos($page_content,"</span></td></tr><tr class=\"metadata_row\"><td class=\"metadata_label\"><span dir=ltr>ISBN");
	$temp = substr($page_content, $start,$finish-$start);
	$bookArray["publisher"] = trim($dataArray[0]);
	$bookArray["date"] = substr(trim($dataArray[1]),0,4);

	// Volume ID
	$start=strpos($page_content,"?id=")+4;
	$finish=strpos($page_content,"\"/><meta property=");
	$temp = substr($page_content, $start,$finish-$start);
	$bookArray["id"] = trim($temp);

	// ISBN10, ISBN13
	$start=strpos($page_content,"ISBN</span></td><td class=\"metadata_value\"><span dir=ltr>")+57;
	$finish=strpos($page_content,"</span></td></tr><tr class=\"metadata_row\"><td class=\"metadata_label\"><span dir=ltr>Length");
	$temp = substr($page_content, $start,$finish-$start);
	$dataArray = explode(",",$temp);

	$bookArray["isbn10"]="";
	$bookArray["isbn13"]="";

	for($i=0; $i<sizeof($dataArray); $i++)
	{
		if(strlen(trim($dataArray[$i])) == 10)
			$bookArray["isbn10"] = trim($dataArray[$i]);

		if(strlen(trim($dataArray[$i])) == 13)
			$bookArray["isbn13"] = trim($dataArray[$i]);
	}

	// Preview Link
	if(strpos($page_content, "<div class=\"bookcover\"><a href=\""))
		$bookArray["preview"] = "https://books.google.com.tr/books?id=".$bookArray["id"]."&printsec=frontcover#v=onepage&q&f=false";
	else
		$bookArray["preview"] = "";
}

else // If ISBN is invalid or Book not found
{
	$bookArray["title"] =  "";
	$bookArray["author"] = "";
	$bookArray["publisher"] = "";
	$bookArray["date"] = "";
	$bookArray["id"] ="";
	$bookArray["isbn10"]="";
	$bookArray["isbn13"]="";
	$bookArray["preview"] = "";

}

	return $bookArray;

}


function executeSQL ($dbQuery1) {
                        global $mysql_host, $mysql_user, $mysql_pw, $mysql_db;
			$mysqli = new mysqli($mysql_host, $mysql_user, $mysql_pw, $mysql_db);

			//$mysqli = new mysqli('localhost','root','root','OCLC', '3306', '/Applications/MAMP/tmp/mysql/mysql.sock');
			//Output any connection error
			if ($mysqli->connect_error) {
   				die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
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
			echo (DEBUG ? "<><><><><>".strlen($dbQuery1): "");
			return $result;
    	}


class XMLParser {
	/**
 * Convert XML to an Array
 *
 * @param string  $XML
 * @return array
 */
 
static function XMLtoArraySimpler ($XML)
{
 $xml_parser = xml_parser_create();
    xml_parse_into_struct($xml_parser, $XML, $vals);
    xml_parser_free($xml_parser);
return $vals;
}

static function XMLtoArray($XML)
{
    $xml_parser = xml_parser_create();
    xml_parse_into_struct($xml_parser, $XML, $vals);
    xml_parser_free($xml_parser);
    // wyznaczamy tablice z powtarzajacymi sie tagami na tym samym poziomie
    $_tmp='';
    foreach ($vals as $xml_elem) {
        $x_tag=$xml_elem['tag'];
        $x_level=$xml_elem['level'];
        $x_type=$xml_elem['type'];
        if ($x_level!=1 && $x_type == 'close') {
            if (isset($multi_key[$x_tag][$x_level]))
                $multi_key[$x_tag][$x_level]=1;
            else
                $multi_key[$x_tag][$x_level]=0;
        }
        if ($x_level!=1 && $x_type == 'complete') {
            if ($_tmp==$x_tag)
                $multi_key[$x_tag][$x_level]=1;
            $_tmp=$x_tag;
        }
    }
    // jedziemy po tablicy
    foreach ($vals as $xml_elem) {
        $x_tag=$xml_elem['tag'];
        $x_level=$xml_elem['level'];
        $x_type=$xml_elem['type'];
        if ($x_type == 'open')
            $level[$x_level] = $x_tag;
        $start_level = 1;
        $php_stmt = '$xml_array';
        if ($x_type=='close' && $x_level!=1)
            $multi_key[$x_tag][$x_level]++;
        while ($start_level < $x_level) {
            $php_stmt .= '[$level['.$start_level.']]';
            if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
                $php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
            $start_level++;
        }
        $add='';
        if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete')) {
            if (!isset($multi_key2[$x_tag][$x_level]))
                $multi_key2[$x_tag][$x_level]=0;
            else
                $multi_key2[$x_tag][$x_level]++;
            $add='['.$multi_key2[$x_tag][$x_level].']';
        }
        if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes', $xml_elem)) {
            if ($x_type == 'open')
                $php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
            else
                $php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
            eval($php_stmt_main);
        }
        if (array_key_exists('attributes', $xml_elem)) {
            if (isset($xml_elem['value'])) {
                $php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                eval($php_stmt_main);
            }
            foreach ($xml_elem['attributes'] as $key=>$value) {
                $php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
                eval($php_stmt_att);
            }
        }
    }
    //d ($xml_array);
    return $xml_array;
}	

/**
 * COURTERSY:
 * xml2array() will convert the given XML text to an array in the XML structure. 
 * Link: http://www.bin-co.com/php/scripts/xml2array/ 
 * Arguments : $contents - The XML text 
 *                $get_attributes - 1 or 0. If this is 1 the static function will get the attributes as well as the tag values - this results in a different array structure in the return value.
 *                $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array sturcture. For 'tag', the tags are given more importance.
 * Return: The parsed XML in an array form. Use print_r() to see the resulting array structure. 
 * Examples: $array =  xml2array(file_get_contents('feed.xml')); 
 *              $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute')); 
 */ 
static function xml2array($contents, $get_attributes=1, $priority = 'tag') { 
    if(!$contents) return array(); 

    if(!function_exists('xml_parser_create')) { 
        //print "'xml_parser_create()' static function not found!"; 
        return array(); 
    } 

    //Get the XML parser of PHP - PHP must have this module for the parser to work 
    $parser = xml_parser_create(''); 
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss 
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); 
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
    xml_parse_into_struct($parser, trim($contents), $xml_values); 
    xml_parser_free($parser); 

    if(!$xml_values) return;//Hmm... 

    //Initializations 
    $xml_array = array(); 
    $parents = array(); 
    $opened_tags = array(); 
    $arr = array(); 

    $current = &$xml_array; //Refference 

    //Go through the tags. 
    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array 
    foreach($xml_values as $data) { 
        unset($attributes,$value);//Remove existing values, or there will be trouble 

        //This command will extract these variables into the foreach scope 
        // tag(string), type(string), level(int), attributes(array). 
        extract($data);//We could use the array by itself, but this cooler. 

        $result = array(); 
        $attributes_data = array(); 
         
        if(isset($value)) { 
            if($priority == 'tag') $result = $value; 
            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode 
        } 

        //Set the attributes too. 
        if(isset($attributes) and $get_attributes) { 
            foreach($attributes as $attr => $val) { 
                if($priority == 'tag') $attributes_data[$attr] = $val; 
                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr' 
            } 
        } 

        //See tag status and do the needed. 
        if($type == "open") {//The starting of the tag '<tag>' 
            $parent[$level-1] = &$current; 
            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag 
                $current[$tag] = $result; 
                if($attributes_data) $current[$tag. '_attr'] = $attributes_data; 
                $repeated_tag_index[$tag.'_'.$level] = 1; 

                $current = &$current[$tag]; 

            } else { //There was another element with the same tag name 

                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array 
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
                    $repeated_tag_index[$tag.'_'.$level]++; 
                } else {//This section will make the value an array if multiple tags with the same name appear together
                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                    $repeated_tag_index[$tag.'_'.$level] = 2; 
                     
                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                        $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
                        unset($current[$tag.'_attr']); 
                    } 

                } 
                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1; 
                $current = &$current[$tag][$last_item_index]; 
            } 

        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />' 
            //See if the key is already taken. 
            if(!isset($current[$tag])) { //New Key 
                $current[$tag] = $result; 
                $repeated_tag_index[$tag.'_'.$level] = 1; 
                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data; 

            } else { //If taken, put all things inside a list(array) 
                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array... 

                    // ...push the new element into that array. 
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
                     
                    if($priority == 'tag' and $get_attributes and $attributes_data) { 
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
                    } 
                    $repeated_tag_index[$tag.'_'.$level]++; 

                } else { //If it is not an array... 
                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                    $repeated_tag_index[$tag.'_'.$level] = 1; 
                    if($priority == 'tag' and $get_attributes) { 
                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                             
                            $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
                            unset($current[$tag.'_attr']); 
                        } 
                         
                        if($attributes_data) { 
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
                        } 
                    } 
                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken 
                } 
            } 

        } elseif($type == 'close') { //End of tag '</tag>' 
            $current = &$parent[$level-1]; 
        } 
    } 
     
    return($xml_array); 
}

static function XMLtoArrayViaJSON ($xml) {
		d($xml);
		$ob= simplexml_load_string($xml);
		d($ob);
		$json  = json_encode($ob);
		$configData = json_decode($json, true);
	return $configData;	
}

static function HTMLtoArrayViaJSON ($xml) {
		d($xml);
		$domd = new DOMDOcument;
		$indicator = $domd->loadHTML($xml);
		d($indicator, $domd);
		$json  = json_encode($domd);
		$configData = json_decode($json, true);
		d($json, $configData);
	return $configData;	
}

}






	
?>