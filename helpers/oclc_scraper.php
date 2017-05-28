<?php

include "../libs/simple_html_dom.php";

function getClassifyData($isbn)
{

    $con = mysqli_connect("localhost", "root", "hJ7lRObbpk", "worldcat");

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $html_classify = file_get_contents('http://classify.oclc.org/classify2/ClassifyDemo?search-standnum-txt=' . $isbn . '&startRec=0');

    $dom = new simple_html_dom();

    $html_classify = $dom->load($html_classify);
    $summary_exists_classify = $html_classify->getElementById('[id=itemsummary]');

    if ($summary_exists_classify != '') {
        $table_classify = $html_classify->getElementById('[class=itemSummary]');
        $check_oclc_summary_classify = mysqli_query($con, "SELECT OCLC FROM summary_classify  WHERE OCLC = '" . $isbn . "'");
        if ($check_oclc_summary_classify->num_rows != 0) {
        } else {
            mysqli_query($con, "INSERT INTO summary_classify (OCLC) VALUES('" . $isbn . "')");
        }
        $td_classify = array();
        $th_classify = array();

        foreach ($table_classify->find('dd') as $cell_classify) {
            // push the cell's text to the array
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

            $classify_query .= "WHERE oclc = " . $isbn . "";

            var_dump($classify_query);

            mysqli_query($con, $classify_query);

        }
        /*for ($k = 0; $k < count($th_classify); $k++) {
            $check = mysqli_query($con, "SHOW COLUMNS FROM `summary_classify` LIKE '" . $th_classify[$k] . "'");
            if ($check->num_rows != 0) {


            } else {
                $stmt = mysqli_query($con, "ALTER IGNORE TABLE summary_classify ADD " . $th_classify[$k] . " TEXT(2000);");
                //echo mysqli_error($con);
            }
            mysqli_query($con, "UPDATE summary_classify set " . $th_classify[$k] . "='" . $td_classify[$k] . "' WHERE OCLC = '" . $isbn . "'");
        }*/

    }
    echo "OCLC " . $isbn . " added Successfully !! <br>";

    mysqli_close($con);

}

getClassifyData('946068164');

