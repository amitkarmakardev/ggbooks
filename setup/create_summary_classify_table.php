<?php
$config  = require "../settings/config.php";

require "../helpers/database_functions.php";

$sql = "CREATE TABLE IF NOT EXISTS `summary_classify` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `oclc` int(20) DEFAULT NULL,
  'isbn10' text NOT NULL,
  `ddc` text DEFAULT NULL,
  `class_number_ddc` text DEFAULT NULL,
  `holdings_ddc` text DEFAULT NULL,
  `links_ddc` text DEFAULT NULL,
  `lcc` text DEFAULT NULL,
  `class_number_lcc` text DEFAULT NULL,
  `holdings_lcc` text DEFAULT NULL,
  `links_lcc` text DEFAULT NULL,
  `title` text DEFAULT NULL,
  `author` text DEFAULT NULL,
  `formats` text DEFAULT NULL,
  `editions` text DEFAULT NULL,
  `translator` text DEFAULT NULL,
  `illustrator` text DEFAULT NULL,
  `editor` text DEFAULT NULL,
  `engraver` text DEFAULT NULL,
  `compiler` text DEFAULT NULL,
  `reporter` text DEFAULT NULL,
  `artist` text DEFAULT NULL,
  `publisher` text DEFAULT NULL,
  `printer` text DEFAULT NULL,
  `other` text DEFAULT NULL,
  `contributor` text DEFAULT NULL,
  `creator` text DEFAULT NULL,
  `redactor` text DEFAULT NULL,
  `bookseller` text DEFAULT NULL,
  `dedicatee` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci";

$resultset = executeQuery($sql);