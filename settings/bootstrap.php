<?php
set_time_limit(0);
ini_set('memory_limit', '1024M');
error_reporting(E_ALL);
ini_set('display_errors', 1);


require __DIR__ . '/../libs/kint-master/Kint.class.php';
require __DIR__ . '/../libs/simple_html_dom.php';
require __DIR__ . '/../libs/Isbn/Isbn.php';
require __DIR__ . '/../libs/Isbn/Hyphens.php';
require __DIR__ . '/../libs/Isbn/Check.php';
require __DIR__ . '/../libs/Isbn/CheckDigit.php';
require __DIR__ . '/../libs/Isbn/Translate.php';
require __DIR__ . '/../libs/Isbn/Validation.php';

require __DIR__ . '/../helpers/benchmark.php';
require __DIR__ . '/../helpers/database_functions.php';
require __DIR__ . '/../helpers/gbooks_scraper.php';
require __DIR__ . '/../helpers/url_functions.php';
require __DIR__ . '/../helpers/oclc_scraper.php';
require __DIR__ . '/../helpers/common_functions.php';

$config = require __DIR__ . "/../settings/config.php";