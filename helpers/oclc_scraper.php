<?php

function generateClassifyDataFromDatabase(){

    $query = "SELECT isbn10 from book_details where isbn10 NOT IN (SELECT isbn10 from summary_classify)";
    $result = executeQuery($query);

    $isbn_array = $result->fetchAll(PDO::FETCH_ASSOC);

    foreach($isbn_array as $key => $value){
        generateClassifyDetails($value['isbn10']);
    }

}

function generateClassifyDetails($isbn10)
{
    $table = 'summary_classify';
    $summary_classify_data = [];
    $benchmarks = [];
    $th_classify = [];
    $td_classify = [];

    echo PHP_EOL . "Classify: $isbn10" . PHP_EOL;
    echo "-----------------------------------------".PHP_EOL;

    if (checkIfExistsInDB('summary_classify', 'isbn10', $isbn10)) {
        echo "Record already exists in summary_classify for ISBN $isbn10" . PHP_EOL;
        return;
    }

    $summary_classify_data['isbn10'] = $isbn10;

    $url = 'http://classify.oclc.org/classify2/ClassifyDemo?search-standnum-txt=' . $isbn10 . '&startRec=0';

    $start = startBenchMarking();
    $htmlContent = getHtmlContent($url);
    $benchmarks['Get html data'] = stopBenchmarking($start);

    $dom = new simple_html_dom();
    $dom = $dom->load($htmlContent);
    $start = startBenchMarking();

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
        $benchmarks['Scrape data'] = stopBenchmarking($start);

        if (count($class_summary_td_classify) > 0) {
            if (isset($th_classify[5])) {
                $summary_classify_data['oclc'] = $td_classify[5];

            }
            if (isset($class_summary_td_classify[0])) {
                $summary_classify_data['ddc'] = $class_summary_td_classify[0];

            }
            if (isset($class_summary_td_classify[1])) {
                $summary_classify_data['class_number_ddc '] = $class_summary_td_classify[1];
            }
            if (isset($class_summary_td_classify[2])) {
                $summary_classify_data['holdings_ddc '] = $class_summary_td_classify[2];
            }

            if (isset($class_summary_td_classify[3])) {
                $summary_classify_data['links_ddc '] = $class_summary_td_classify[3];
            }

            if (isset($class_summary_td_classify[4])) {
                $summary_classify_data['lcc '] = $class_summary_td_classify[4];
            }

            if (isset($class_summary_td_classify[5])) {
                $summary_classify_data['class_number_lcc '] = $class_summary_td_classify[5];
            }

            if (isset($class_summary_td_classify[6])) {
                $summary_classify_data['holdings_lcc '] = $class_summary_td_classify[6];
            }

            if (isset($class_summary_td_classify[7])) {
                $summary_classify_data['links_lcc '] = $class_summary_td_classify[7];
            }
        }
    }

    $start = startBenchMarking();
    insertToDB($table, $summary_classify_data);
    for ($k = 0; $k < count($th_classify); $k++) {
        $column_name = str_replace(' ', '', trim($th_classify[$k]));
        $result = executeQuery("SHOW COLUMNS FROM `summary_classify` LIKE '$column_name'");
        $result = $result->fetchAll();
        if ($result == false) {
            executeQuery("ALTER TABLE summary_classify ADD COLUMN $column_name TEXT");
        }
        executeQuery("UPDATE summary_classify set $column_name = '$td_classify[$k]'WHERE isbn10 = '$isbn10'");
    }
    $benchmarks['Insert to database'] = stopBenchmarking($start);

    printBenchmark($benchmarks);

}




