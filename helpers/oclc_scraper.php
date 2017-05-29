<?php

function generateClassifyDetails($isbn10)
{
    echo PHP_EOL . "Classify: $isbn10".PHP_EOL;


    $url = 'http://classify.oclc.org/classify2/ClassifyDemo?search-standnum-txt=' . $isbn10 . '&startRec=0';

    if (checkPageExists($url) == false) {
        if (checkIfExistsInDB('summary_classify', 'isbn10', $isbn10) == false) {
            executeQuery("INSERT INTO summary_classify (isbn10) VALUES($isbn10)");
        }
        return;
    }

    $shdom = new simple_html_dom();

    $htmlContent = getHtmlContent($url);

    $dom = $shdom->load($htmlContent);

    $summary_exists_classify = $dom->getElementById('[id=itemsummary]');

    if ($summary_exists_classify != '') {
        $table_classify = $dom->getElementById('[class=itemSummary]');

        $td_classify = array();
        $th_classify = array();

        foreach ($table_classify->find('dd') as $cell_classify) {
            // push the cell's text to the array
            $td_classify[] = processString($cell_classify->plaintext);
        }

        foreach ($table_classify->find('dt') as $cell_heading_classify) {
            // push the cell's text to the array
            $th_classify[] = trim(strtr($cell_heading_classify->plaintext, array(':' => '', '/' => '_')));
        }

        $class_summary_classify = $dom->getElementById('[id=classSummaryData]')->childNodes(1);
        $class_summary_td_classify = array();
        foreach ($class_summary_classify->find('tr') as $tr_summary_classify) {
            foreach ($tr_summary_classify->find('td') as $class_cell_classify) {
                // push the cell's text to the array
                $class_summary_td_classify[] = processString($class_cell_classify->plaintext);
            }
        }

        if (count($class_summary_td_classify) > 0) {

            $classify_query = "UPDATE summary_classify set ";

            if (isset($th_classify[5])) {
                $classify_query .= "oclc='" . $td_classify[5] . "',";

            }

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

            $classify_query .= "WHERE isbn10 = " . $isbn10 . "";

            executeQuery($classify_query);

        }
        for ($k = 0; $k < count($th_classify); $k++) {
            $check = executeQuery("SHOW COLUMNS FROM `summary_classify` LIKE '$th_classify[$k]'");
            ddd($check);
//                mysqli_query($con, "ALTER IGNORE TABLE summary_classify ADD " . $th_classify[$k] . " TEXT(2000);");
        }
//            mysqli_query($con, "UPDATE summary_classify set " . $th_classify[$k] . "='" . $td_classify[$k] . "' WHERE OCLC = '" . $isbn10 . "'");
    }

}


