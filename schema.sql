-- --------------------------------------------------------
-- Host:                         localhost
-- Wersja serwera:               10.3.16-MariaDB - mariadb.org binary distribution
-- Serwer OS:                    Win64
-- HeidiSQL Wersja:              10.2.0.5610
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Zrzut struktury bazy danych hermes
CREATE DATABASE IF NOT EXISTS `hermes` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_polish_ci */;
USE `hermes`;

-- Zrzut struktury tabela hermes.albums
CREATE TABLE IF NOT EXISTS `albums` (
  `album_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`album_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

-- Zrzut struktury tabela hermes.exercises
CREATE TABLE IF NOT EXISTS `exercises` (
  `exercise_id` int(11) NOT NULL AUTO_INCREMENT,
  `workout_id` int(11) NOT NULL,
  `type_id` smallint(6) NOT NULL,
  `reps` smallint(6) DEFAULT NULL,
  `weight` smallint(6) DEFAULT NULL,
  `duration` smallint(6) DEFAULT NULL,
  `failure` tinyint(1) NOT NULL,
  PRIMARY KEY (`exercise_id`),
  KEY `FK_exercises_workouts` (`workout_id`),
  KEY `FK_exercises_exercise_types` (`type_id`),
  CONSTRAINT `FK_exercises_exercise_types` FOREIGN KEY (`type_id`) REFERENCES `exercise_types` (`type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_exercises_workouts` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`workout_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

-- Zrzut struktury tabela hermes.exercise_types
CREATE TABLE IF NOT EXISTS `exercise_types` (
  `type_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `exercise_type` varchar(127) COLLATE utf8_polish_ci NOT NULL,
  `show_duration` tinyint(1) NOT NULL DEFAULT 0,
  `show_reps` tinyint(1) NOT NULL DEFAULT 0,
  `show_weight` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

-- Zrzut struktury tabela hermes.gyms
CREATE TABLE IF NOT EXISTS `gyms` (
  `gym_id` int(11) NOT NULL AUTO_INCREMENT,
  `album_id` int(11) NOT NULL,
  `type_id` smallint(6) NOT NULL DEFAULT 0,
  `name` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `lat` decimal(10,7) NOT NULL,
  `long` decimal(11,7) NOT NULL,
  PRIMARY KEY (`gym_id`),
  KEY `FK_gyms_albums` (`album_id`),
  KEY `FK_gyms_gym_types` (`type_id`),
  CONSTRAINT `FK_gyms_albums` FOREIGN KEY (`album_id`) REFERENCES `albums` (`album_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_gyms_gym_types` FOREIGN KEY (`type_id`) REFERENCES `gym_types` (`type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

-- Zrzut struktury tabela hermes.gym_types
CREATE TABLE IF NOT EXISTS `gym_types` (
  `type_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `gym_type` varchar(63) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

-- Zrzut struktury tabela hermes.oauth_access_tokens
CREATE TABLE IF NOT EXISTS `oauth_access_tokens` (
  `access_token` varchar(40) COLLATE utf8_polish_ci NOT NULL,
  `client_id` varchar(80) COLLATE utf8_polish_ci NOT NULL,
  `user_id` varchar(80) COLLATE utf8_polish_ci DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `scope` varchar(4000) COLLATE utf8_polish_ci DEFAULT NULL,
  PRIMARY KEY (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

-- Zrzut struktury tabela hermes.oauth_authorization_codes
CREATE TABLE IF NOT EXISTS `oauth_authorization_codes` (
  `authorization_code` varchar(40) COLLATE utf8_polish_ci NOT NULL,
  `client_id` varchar(80) COLLATE utf8_polish_ci NOT NULL,
  `user_id` varchar(80) COLLATE utf8_polish_ci DEFAULT NULL,
  `redirect_uri` varchar(2000) COLLATE utf8_polish_ci DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `scope` varchar(4000) COLLATE utf8_polish_ci DEFAULT NULL,
  `id_token` varchar(1000) COLLATE utf8_polish_ci DEFAULT NULL,
  PRIMARY KEY (`authorization_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

-- Zrzut struktury tabela hermes.oauth_clients
CREATE TABLE IF NOT EXISTS `oauth_clients` (
  `client_id` varchar(80) COLLATE utf8_polish_ci NOT NULL,
  `client_secret` varchar(80) COLLATE utf8_polish_ci DEFAULT NULL,
  `redirect_uri` varchar(2000) COLLATE utf8_polish_ci DEFAULT NULL,
  `grant_types` varchar(80) COLLATE utf8_polish_ci DEFAULT NULL,
  `scope` varchar(4000) COLLATE utf8_polish_ci DEFAULT NULL,
  `user_id` varchar(80) COLLATE utf8_polish_ci DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

-- Zrzut struktury tabela hermes.oauth_jwt
CREATE TABLE IF NOT EXISTS `oauth_jwt` (
  `client_id` varchar(80) COLLATE utf8_polish_ci NOT NULL,
  `subject` varchar(80) COLLATE utf8_polish_ci DEFAULT NULL,
  `public_key` varchar(2000) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

-- Zrzut struktury tabela hermes.oauth_refresh_tokens
CREATE TABLE IF NOT EXISTS `oauth_refresh_tokens` (
  `refresh_token` varchar(40) COLLATE utf8_polish_ci NOT NULL,
  `client_id` varchar(80) COLLATE utf8_polish_ci NOT NULL,
  `user_id` varchar(80) COLLATE utf8_polish_ci DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `scope` varchar(4000) COLLATE utf8_polish_ci DEFAULT NULL,
  PRIMARY KEY (`refresh_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

-- Zrzut struktury tabela hermes.oauth_scopes
CREATE TABLE IF NOT EXISTS `oauth_scopes` (
  `scope` varchar(80) COLLATE utf8_polish_ci NOT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`scope`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

-- Zrzut struktury tabela hermes.oauth_users
CREATE TABLE IF NOT EXISTS `oauth_users` (
  `username` varchar(80) COLLATE utf8_polish_ci NOT NULL,
  `password` varchar(80) COLLATE utf8_polish_ci DEFAULT NULL,
  `first_name` varchar(80) COLLATE utf8_polish_ci DEFAULT NULL,
  `last_name` varchar(80) COLLATE utf8_polish_ci DEFAULT NULL,
  `email` varchar(80) COLLATE utf8_polish_ci DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT NULL,
  `scope` varchar(4000) COLLATE utf8_polish_ci DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

-- Zrzut struktury tabela hermes.photos
CREATE TABLE IF NOT EXISTS `photos` (
  `photo_id` int(11) NOT NULL AUTO_INCREMENT,
  `album_id` int(11) NOT NULL,
  `path` varchar(255) COLLATE utf8_polish_ci NOT NULL DEFAULT '',
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `width` smallint(6) NOT NULL,
  `height` smallint(6) NOT NULL,
  PRIMARY KEY (`photo_id`),
  KEY `FK_photos_albums` (`album_id`),
  CONSTRAINT `FK_photos_albums` FOREIGN KEY (`album_id`) REFERENCES `albums` (`album_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

-- Zrzut struktury tabela hermes.users
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(32) COLLATE utf8_polish_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `name` varchar(127) COLLATE utf8_polish_ci NOT NULL,
  `register_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) COLLATE utf8_polish_ci NOT NULL DEFAULT 'uploads\\img\\avatars\\default.jpg',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

-- Zrzut struktury tabela hermes.workouts
CREATE TABLE IF NOT EXISTS `workouts` (
  `workout_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `gym_id` int(11) NOT NULL,
  `name` varchar(127) COLLATE utf8_polish_ci NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `added` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`workout_id`),
  KEY `FK_workouts_users` (`user_id`),
  KEY `FK_workouts_gyms` (`gym_id`),
  CONSTRAINT `FK_workouts_gyms` FOREIGN KEY (`gym_id`) REFERENCES `gyms` (`gym_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_workouts_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- Eksport danych został odznaczony.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
