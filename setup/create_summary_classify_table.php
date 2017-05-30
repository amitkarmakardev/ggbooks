<?php
$config  = require "../settings/config.php";

require "../helpers/database_functions.php";

$sql = "CREATE TABLE summary_classify (
          id int(20) AUTO_INCREMENT,
          isbn10 varchar(50),
          oclc varchar(50),
          ddc text,
          class_number_ddc text,
          holdings_ddc text,
          links_ddc text,
          lcc text,
          class_number_lcc text,
          holdings_lcc text,
          links_lcc text,
          PRIMARY KEY  (id)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci";

if(executeQuery($sql)){
    echo "Table summary_classify created successfully";
}