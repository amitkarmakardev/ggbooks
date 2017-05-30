<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr" data-livestyle-extension="available">
<body>
<?php
ini_set('max_execution_time', 0);
include 'connection.php';
include 'simple_html_dom.php';
var_dump($argv[1]);
var_dump($argv[2]);
$j = 10000000000;

$query_summary = "SELECT id FROM summary";
$result_summary = mysqli_query($con, $query_summary);

if (empty($result_summary)) {
    $query = "CREATE TABLE summary (
		                          id int(20) AUTO_INCREMENT,
		                          oclc int(200) NOT NULL,
		                          PRIMARY KEY  (id)
		                          )";
    $result = mysqli_query($con, $query);
}

$query_details = "SELECT id FROM details";
$result_details = mysqli_query($con, $query_details);

if (empty($result_details)) {
    $query = "CREATE TABLE details (
		                          id int(20) AUTO_INCREMENT,
		                          oclc int(20) NOT NULL,
		                          PRIMARY KEY  (id)
		                          )";
    $result = mysqli_query($con, $query);
}

$query_summary_classify = "SELECT id FROM summary_classify";
$result_summary_classify = mysqli_query($con, $query_summary_classify);

if (empty($result_summary_classify)) {
    $query = "CREATE TABLE summary_classify (
		                          id int(20) AUTO_INCREMENT,
		                          oclc int(20) NOT NULL,
		                          ddc TEXT(2000),
		                          class_number_ddc TEXT(2000),
		                          holdings_ddc TEXT(2000),
		                          links_ddc TEXT(2000),
		                          lcc TEXT(2000),
		                          class_number_lcc TEXT(2000),
		                          holdings_lcc TEXT(2000),
		                          links_lcc TEXT(2000),
		                          PRIMARY KEY  (id)
		                          )";
    $result = mysqli_query($con, $query);
}

//for($i = $argv[1];$i < $argv[2];$i++)
for ($i = $argv[1]; $i < $argv[2]; $i++) {
    $html = file_get_html('http://www.worldcat.org/title/rand-mcnally-book-of-favorite-pastimes/oclc/' . $i);
    //$html = $result;
    $check_oclc_summary = mysqli_query($con, "SELECT OCLC FROM summary  WHERE OCLC = '" . $i . "'");
    $check_oclc_details = mysqli_query($con, "SELECT OCLC FROM details  WHERE OCLC = '" . $i . "'");
    if ($check_oclc_summary->num_rows != 0) {
    } else {
        mysqli_query($con, "INSERT INTO summary (OCLC) VALUES('" . $i . "')");
    }
    if ($check_oclc_details->num_rows != 0) {
    } else {
        mysqli_query($con, "INSERT INTO details (OCLC) VALUES('" . $i . "')");
    }

    $summary_exists = $html->getElementById('[id=bibdata]');

    if (count($html->getElementById('[id=bibdata]')) > 0) {
        $table = $html->getElementById('[id=bibdata]')->childNodes(1);
    }

    if (count($html->getElementById('[id=details]')) > 0) {
        $table_details = $html->getElementById('[id=details]')->childNodes(1);
    }
    $td = array();
    $td_details = array();
    $th = array();
    $th_details = array();

    if (isset($table)) {
        foreach ($table->find('tr') as $tr) {


            foreach ($tr->find('td') as $cell) {
                // push the cell's text to the array
                //$td[] = strtr($cell->innertext, array('"' => '\"'));
                //$td[] = $cell->innertext;
                $td[] = $cell->plaintext;
            }

            foreach ($tr->find('th') as $cell_heading) {
                // push the cell's text to the array

                $th[] = trim(strtr($cell_heading->plaintext, array(':' => '', '/' => '_')));
            }
        }
    }

    if (isset($table_details)) {
        foreach ($table_details->find('table') as $table_detail) {

            foreach ($table_detail->find('tr') as $tr_details) {
                foreach ($tr_details->find('td') as $cell_details) {
                    // push the cell's text to the array
                    //$td[] = strtr($cell->innertext, array('"' => '\"'));
                    //$td[] = $cell->innertext;
                    $td_details[] = $cell_details->plaintext;
                }


                foreach ($tr_details->find('th') as $cell_heading_details) {
                    // push the cell's text to the array

                    $th_details[] = trim(strtr($cell_heading_details->plaintext, array(':' => '', '/' => '-')));
                }
            }

        }
    }

    for ($k = 0; $k < count($th); $k++) {
        $check = mysqli_query($con, "SHOW COLUMNS FROM `summary` LIKE '" . $th[$k] . "'");
        if ($check->num_rows != 0) {


        } else {
            $stmt = mysqli_query($con, "ALTER IGNORE TABLE summary ADD " . $th[$k] . " TEXT(2000);");
            //echo mysqli_error($con);
        }
        mysqli_query($con, "UPDATE summary set " . $th[$k] . "='" . $td[$k] . "' WHERE OCLC = '" . $i . "'");
    }
    for ($m = 0; $m < count($th_details); $m++) {
        $check_details = mysqli_query($con, "SHOW COLUMNS FROM `details` LIKE '" . $th_details[$m] . "'");
        if ($check_details->num_rows != 0) {


        } else {
            $stmt = mysqli_query($con, "ALTER IGNORE TABLE details ADD " . $th_details[$m] . " TEXT(2000);");
            //echo mysqli_error($con);
        }
        mysqli_query($con, "UPDATE details set " . $th_details[$m] . "='" . $td_details[$m] . "' WHERE OCLC = '" . $i . "'");
    }
    if ($summary_exists != '') {
    } else {
        $j = 0;

    }
    /*Get Classify Data*/
    $html_classify = file_get_html('http://classify.oclc.org/classify2/ClassifyDemo?search-standnum-txt=' . $i . '&startRec=0');
    $summary_exists_classify = $html_classify->getElementById('[id=itemsummary]');
    //$table_details = $html_classify->getElementById('[id=details]')->childNodes(1);
    //$html = $result;
    if ($summary_exists_classify != '') {
        $table_classify = $html_classify->getElementById('[class=itemSummary]');
        $check_oclc_summary_classify = mysqli_query($con, "SELECT OCLC FROM summary_classify  WHERE OCLC = '" . $i . "'");
        if ($check_oclc_summary_classify->num_rows != 0) {
        } else {
            mysqli_query($con, "INSERT INTO summary_classify (OCLC) VALUES('" . $i . "')");
        }
        $td_classify = array();
        $th_classify = array();

        foreach ($table_classify->find('dd') as $cell_classify) {
            // push the cell's text to the array
            //$td[] = strtr($cell->innertext, array('"' => '\"'));
            //$td[] = $cell->innertext;
            $td_classify[] = $cell_classify->plaintext;
        }

        foreach ($table_classify->find('dt') as $cell_heading_classify) {
            // push the cell's text to the array

            $th_classify[] = trim(strtr($cell_heading_classify->plaintext, array(':' => '', '/' => '_')));
        }

        $class_summary_classify = $html_classify->getElementById('[id=classSummaryData]')->childNodes(1);
        $class_summary_td_classify = array();
        foreach ($class_summary_classify->find('tr') as $tr_summary_classify) {
            foreach ($tr_summary_classify->find('td') as $class_cell_classify) {
                // push the cell's text to the array
                //$td[] = strtr($cell->innertext, array('"' => '\"'));
                //$td[] = $cell->innertext;
                $class_summary_td_classify[] = $class_cell_classify->plaintext;
            }
        }
        if (count($class_summary_td_classify) > 0) {

            $classify_query = "UPDATE summary_classify set ";

            if (isset($class_summary_td_classify[0])) {
                $classify_query .= "ddc='" . $class_summary_td_classify[0] . "',";

            }
            if (isset($class_summary_td_classify[1])) {
                $classify_query .= "class_number_ddc = '" . $class_summary_td_classify[1] . "',";
            }
            if (isset($class_summary_td_classify[2])) {
                $classify_query .= "holdings_ddc = '" . $class_summary_td_classify[2] . "',";
            }

            if (isset($class_summary_td_classify[3])) {
                $classify_query .= " links_ddc = '" . $class_summary_td_classify[3] . "' , ";
            }

            if (isset($class_summary_td_classify[4])) {
                $classify_query .= " lcc = '" . $class_summary_td_classify[4] . "' ,";
            }

            if (isset($class_summary_td_classify[5])) {
                $classify_query .= "  class_number_lcc = '" . $class_summary_td_classify[5] . "' ,";
            }

            if (isset($class_summary_td_classify[6])) {
                $classify_query .= "   holdings_lcc = '" . $class_summary_td_classify[6] . "', ";
            }

            if (isset($class_summary_td_classify[7])) {
                $classify_query .= "   links_lcc = '" . $class_summary_td_classify[7] . "' ";
            }

            $classify_query .= "WHERE oclc = " . $i . "";

            mysqli_query($con, $classify_query);

        }
        for ($k = 0; $k < count($th_classify); $k++) {
            $check = mysqli_query($con, "SHOW COLUMNS FROM `summary_classify` LIKE '" . $th_classify[$k] . "'");
            if ($check->num_rows != 0) {


            } else {
                $stmt = mysqli_query($con, "ALTER IGNORE TABLE summary_classify ADD " . $th_classify[$k] . " TEXT(2000);");
                //echo mysqli_error($con);
            }
            mysqli_query($con, "UPDATE summary_classify set " . $th_classify[$k] . "='" . $td_classify[$k] . "' WHERE OCLC = '" . $i . "'");
        }

    }
    echo "OCLC " . $i . " added Successfully !! <br>";
}
mysqli_close($con);
?>
</body>
</html>