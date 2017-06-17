<?php

$sql = "CREATE TABLE summary_classify (
          `id` INT(20) AUTO_INCREMENT,
          `isbn10` VARCHAR(10) NOT NULL,
          `isbn13` VARCHAR(13) NOT NULL,
          `oclc` VARCHAR(20) DEFAULT NULL,
          `ddc` TEXT DEFAULT NULL,
          `class_number_ddc` TEXT DEFAULT NULL,
          `holdings_ddc` TEXT DEFAULT NULL,
          `links_ddc` TEXT DEFAULT NULL,
          `lcc` TEXT DEFAULT NULL,
          `class_number_lcc` TEXT DEFAULT NULL,
          `holdings_lcc` TEXT DEFAULT NULL,
          `links_lcc` TEXT DEFAULT NULL,
          `http_response_code` VARCHAR(10) DEFAULT NULL,
          PRIMARY KEY (id)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci";
return $sql;