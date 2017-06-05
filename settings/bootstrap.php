<?php
set_time_limit(0);
ini_set('memory_limit', '1024M');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'libs/kint-master/Kint.class.php';
require 'libs/simple_html_dom.php';

require 'helpers/benchmark.php';
require 'helpers/database_functions.php';
require 'helpers/gbooks_scraper.php';
require 'helpers/url_functions.php';
require 'helpers/code_generator.php';
require 'helpers/oclc_scraper.php';
require 'helpers/common_functions.php';

$config = require "settings/config.php";