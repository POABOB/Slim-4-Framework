CREATE DATABASE IF NOT EXISTS Example;
USE Example;
CREATE TABLE IF NOT EXISTS `Example`.`Users` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `name` VARCHAR(64) NOT NULL, `password` VARCHAR(64) NOT NULL ,  PRIMARY KEY (`id`)) ENGINE = InnoDB;

-- FOR TEST
CREATE DATABASE IF NOT EXISTS Example_test;
USE Example_test;
CREATE TABLE IF NOT EXISTS `Example_test`.`Users` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `name` VARCHAR(64) NOT NULL, `password` VARCHAR(64) NOT NULL ,  PRIMARY KEY (`id`)) ENGINE = InnoDB;