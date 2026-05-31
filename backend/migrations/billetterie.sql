-- phpMyAdmin SQL Dump
-- version 5.x
-- https://www.phpmyadmin.net/
--
-- Base de données : `your_database_name`
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table : ticket_category
-- --------------------------------------------------------

DROP TABLE IF EXISTS `ticket_category`;
CREATE TABLE `ticket_category` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_TICKET_CATEGORY_SLUG` (`slug`)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table : ticket
-- --------------------------------------------------------

DROP TABLE IF EXISTS `ticket`;
CREATE TABLE `ticket` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `image_id` INT DEFAULT NULL,
  `category_id` INT DEFAULT NULL,
  `title` VARCHAR(180) NOT NULL,
  `opponent` VARCHAR(180) NOT NULL,
  `match_date` DATETIME NOT NULL,
  `venue` VARCHAR(180) DEFAULT NULL,
  `price` DOUBLE NOT NULL,
  `description` LONGTEXT DEFAULT NULL,
  `stock` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_97A0ADA43DA5256D` (`image_id`),
  KEY `IDX_97A0ADA412469DE2` (`category_id`)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Contraintes de clés étrangères
-- --------------------------------------------------------

ALTER TABLE `ticket`
  ADD CONSTRAINT `FK_97A0ADA43DA5256D`
  FOREIGN KEY (`image_id`) REFERENCES `media` (`id`)
  ON DELETE SET NULL;

ALTER TABLE `ticket`
  ADD CONSTRAINT `FK_97A0ADA412469DE2`
  FOREIGN KEY (`category_id`) REFERENCES `ticket_category` (`id`)
  ON DELETE SET NULL;

-- --------------------------------------------------------
-- Données initiales
-- --------------------------------------------------------

INSERT INTO `ticket_category` (`name`, `slug`) VALUES
('Domicile', 'domicile'),
('Extérieur', 'exterieur');

COMMIT;
