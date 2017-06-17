<?php

$sql = "CREATE TABLE `book_details` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `isbn10` VARCHAR(10) NOT NULL,
        `isbn13` VARCHAR(13) NOT NULL,
        `title` TEXT DEFAULT NULL,
        `author` TEXT DEFAULT NULL,
        `edition` TEXT DEFAULT NULL,
        `publisher` TEXT DEFAULT NULL,
        `publish_year` TEXT DEFAULT NULL,
        `page_length` VARCHAR(20) DEFAULT NULL,
        `subjects` TEXT DEFAULT NULL,
        `price` VARCHAR(50) DEFAULT NULL,
        `sample` BOOLEAN DEFAULT 0,
        `http_response_code` VARCHAR(10) DEFAULT NULL,
        PRIMARY KEY(id)
      ) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci";

return $sql;