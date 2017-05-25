CREATE DATABASE ggbooks CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `book_details` (
    `id` int NOT NULL AUTO_INCREMENT,
    `title` text DEFAULT NULL,
    `author` text DEFAULT NULL,
    `edition` text DEFAULT NULL,
    `publisher` text DEFAULT NULL,
    `publish_year` text DEFAULT NULL,
    `isbn10` varchar(10) NOT NULL UNIQUE,
    `isbn13` varchar(13) DEFAULT NULL,
    `page_length` varchar(20) DEFAULT NULL,
    `subjects` text DEFAULT NULL,
    `price` varchar(50) DEFAULT NULL,
    `sample` BOOLEAN DEFAULT 0,
    PRIMARY KEY(ID)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;