-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Wersja serwera:               10.4.10-MariaDB - mariadb.org binary distribution
-- Serwer OS:                    Win64
-- HeidiSQL Wersja:              10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Zrzut struktury tabela hermes.routines
CREATE TABLE IF NOT EXISTS `routines` (
  `routine_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(255) COLLATE utf8_polish_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`routine_id`),
  KEY `FK_routines_users` (`user_id`),
  CONSTRAINT `FK_routines_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

-- Zrzut struktury tabela hermes.routine_exercise_types
CREATE TABLE IF NOT EXISTS `routine_exercise_types` (
  `routine_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `routine_id` int(11) NOT NULL,
  `type_id` smallint(6) NOT NULL DEFAULT 0,
  `sets` smallint(6) NOT NULL DEFAULT 0,
  UNIQUE KEY `routine_id_type_id` (`routine_id`,`type_id`),
  KEY `order` (`routine_type_id`),
  KEY `FK_routine_exercise_types_exercise_types` (`type_id`),
  CONSTRAINT `FK_routine_exercise_types_exercise_types` FOREIGN KEY (`type_id`) REFERENCES `exercise_types` (`type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_routine_exercise_types_routines` FOREIGN KEY (`routine_id`) REFERENCES `routines` (`routine_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
