CREATE DATABASE IF NOT EXISTS tp_php;
USE tp_php;

CREATE TABLE publication (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    picture VARCHAR(255) NOT NULL,
    description TEXT NULL,
    datetime VARCHAR(30) NOT NULL,
    is_published TINYINT NOT NULL DEFAULT 1
);