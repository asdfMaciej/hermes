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

-- Zrzucanie danych dla tabeli hermes.albums: ~2 rows (około)
DELETE FROM `albums`;
/*!40000 ALTER TABLE `albums` DISABLE KEYS */;
INSERT INTO `albums` (`album_id`) VALUES
	(1),
	(2),
	(3);
/*!40000 ALTER TABLE `albums` ENABLE KEYS */;

-- Zrzucanie danych dla tabeli hermes.gyms: ~3 rows (około)
DELETE FROM `gyms`;
/*!40000 ALTER TABLE `gyms` DISABLE KEYS */;
INSERT INTO `gyms` (`gym_id`, `album_id`, `type_id`, `name`, `lat`, `long`) VALUES
	(1, 1, 1, 'Olympic Wronki', 52.7176563, 16.3735091),
	(2, 2, 2, 'Street Workout', 52.7128951, 16.3925846),
	(3, 3, 1, 'Dom', 0.0000000, 0.0000000);
/*!40000 ALTER TABLE `gyms` ENABLE KEYS */;

-- Zrzucanie danych dla tabeli hermes.gym_types: ~2 rows (około)
DELETE FROM `gym_types`;
/*!40000 ALTER TABLE `gym_types` DISABLE KEYS */;
INSERT INTO `gym_types` (`type_id`, `gym_type`) VALUES
	(1, 'Indoors'),
	(2, 'Outdoors');
/*!40000 ALTER TABLE `gym_types` ENABLE KEYS */;

-- Zrzucanie danych dla tabeli hermes.oauth_users: ~0 rows (około)
DELETE FROM `oauth_users`;
/*!40000 ALTER TABLE `oauth_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_users` ENABLE KEYS */;

-- Zrzucanie danych dla tabeli hermes.photos: ~2 rows (około)
DELETE FROM `photos`;
/*!40000 ALTER TABLE `photos` DISABLE KEYS */;
INSERT INTO `photos` (`photo_id`, `album_id`, `path`, `date`, `width`, `height`) VALUES
	(1, 1, 'uploads\\img\\olympic.jpg', '2020-03-25 02:59:01', 520, 390),
	(2, 2, 'uploads\\img\\drazki.jpg', '2020-04-12 18:53:18', 1113, 807);
/*!40000 ALTER TABLE `photos` ENABLE KEYS */;

-- Zrzucanie danych dla tabeli hermes.reaction_types: ~1 rows (około)
DELETE FROM `reaction_types`;
/*!40000 ALTER TABLE `reaction_types` DISABLE KEYS */;
INSERT INTO `reaction_types` (`type_id`, `reaction`) VALUES
	(1, 'Biceps');
/*!40000 ALTER TABLE `reaction_types` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
