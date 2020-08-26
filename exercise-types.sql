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

-- Zrzucanie danych dla tabeli hermes.exercise_categories: ~4 rows (około)
DELETE FROM `exercise_categories`;
/*!40000 ALTER TABLE `exercise_categories` DISABLE KEYS */;
INSERT INTO `exercise_categories` (`category_id`, `parent_id`, `name`) VALUES
	(1, NULL, 'Masa ciała'),
	(2, NULL, 'Sztanga'),
	(3, NULL, 'Hantle'),
	(8, NULL, 'Maszyna'),
	(9, NULL, 'Kettlebelle'),
	(10, NULL, 'Wyciąg');
/*!40000 ALTER TABLE `exercise_categories` ENABLE KEYS */;

-- Zrzucanie danych dla tabeli hermes.exercise_types: ~8 rows (około)
DELETE FROM `exercise_types`;
/*!40000 ALTER TABLE `exercise_types` DISABLE KEYS */;
INSERT INTO `exercise_types` (`type_id`, `exercise_type`, `exercise_type_en`, `category_id`, `show_duration`, `show_reps`, `show_weight`) VALUES
	(1, 'Przysiad (sztanga)', 'Back squat', 2, 0, 1, 1),
	(2, 'Uginanie przedramion (hantle)', 'Biceps curls', 3, 0, 1, 1),
	(3, 'Plank', 'Plank', 1, 1, 0, 0),
	(6, 'Wyciskanie na ławce płaskiej (sztanga)', 'Bench press', 2, 0, 1, 1),
	(8, 'Wiosłowanie (T-bar)', 'Rowing (T-bar)', 2, 0, 1, 1),
	(9, 'Wiosłowanie (sztanga)', 'Rowing (barbell)', 2, 0, 1, 1),
	(10, 'Wznosy bokiem (hantle)', 'Lateral raises', 3, 0, 1, 1),
	(12, 'Prostowanie przedramion (wyciąg)', 'Triceps pushdown', 10, 0, 1, 1),
	(13, 'Allahy', 'Cable crunch', 10, 0, 1, 1),
	(14, 'Martwy ciąg klasyczny (sztanga)', 'Deadlift', 2, 0, 1, 1),
	(15, 'Wyciskanie żołnierskie', 'Overhead press', 2, 0, 1, 1),
	(16, 'Podciąganie (nadchwyt)', 'Pull-ups', 1, 0, 1, 1),
	(17, 'Podciąganie (nadchwyt, szeroki chwyt)', 'Pull-ups (wide grip)', 1, 0, 1, 1),
	(19, 'Podciąganie (nadchwyt, wąski chwyt)', 'Pull-ups (close grip)', 1, 0, 1, 1),
	(20, 'Podciąganie (podchwyt)', 'Chin-ups', 1, 0, 1, 1),
	(21, 'Podciąganie (podchwyt, szeroki chwyt)', 'Chin-ups (wide grip)', 1, 0, 1, 1),
	(22, 'Podciąganie (podchwyt, wąski chwyt)', 'Chin-ups (close grip)', 1, 0, 1, 1),
	(23, 'Podciąganie (chwyt neutralny)', 'Neutral grip pull-ups', 1, 0, 1, 1),
	(24, 'Podciąganie (chwyt neutralny, szeroki)', 'Neutral grip pull-ups (wide grip)', 1, 0, 1, 1),
	(25, 'Podciąganie (chwyt neutralny, wąski)', 'Neutral grip pull-ups (close grip)', 1, 0, 1, 1),
	(27, 'Wyciskanie wąsko na ławce płaskiej (sztanga)', 'Bench press (close grip)', 2, 0, 1, 1),
	(28, 'Wspięcia na palce stojąc', 'Calf raises', 1, 0, 1, 1),
	(29, 'Hip thrust', 'Hip thrust', 2, 0, 1, 1),
	(30, 'Zakroki (hantle)', 'Step forward lunges (dumbbells)', 3, 0, 1, 1),
	(32, 'Zakroki (sztanga)', 'Step forward lunges (barbell)', 2, 0, 1, 1),
	(33, 'Odwodziciele', 'Hip abdcutor machine', 8, 0, 1, 1),
	(34, 'Brzuszki', 'Crunches', 1, 0, 1, 0),
	(37, 'Skręt tułowia siedząc (kettlebell)', 'Russian twist (kettlebell)', 9, 0, 1, 1),
	(38, 'Przyciąganie liny z wyciągu górnego do twarzy', 'Face pull', 10, 0, 1, 1),
	(39, 'Rozpiętki na ławce skośnej (hantle)', 'Flyes (dumbbell)', 3, 0, 1, 1),
	(40, 'Przysiad bułgarski (sztanga)', 'Bulgarian squat (barbell)', 2, 0, 1, 1),
	(41, 'Przysiad bułgarski (hantle)', 'Bulgarian squat (dumbbells)', 3, 0, 1, 1),
	(42, 'Wyciskanie francuskie (hantel, stojąc)', 'Triceps extension (dumbbell, standing)', 3, 0, 1, 1),
	(43, 'Wyciskanie francuskie (hantel, leżąc)', 'Triceps extension (dumbbell, lying)', 3, 0, 1, 1),
	(44, 'Wyciskanie francuskie (hantel, siedząc)', 'Triceps extension (dumbbell, sitting)', 3, 0, 1, 1),
	(45, 'Wyciskanie francuskie (sztanga, siedząc)', 'Triceps extension (barbell, sitting)', 2, 0, 1, 1),
	(46, 'Wyciskanie francuskie (sztanga, leżąc)', 'Triceps extension (barbell, lying)', 2, 0, 1, 1),
	(47, 'Wyciskanie francuskie (sztanga, stojąc)', 'Triceps extension (barbell, sitting)', 2, 0, 1, 1),
	(49, 'Przyciąganie liny z wyciągu dolnego do twarzy', 'Face pull (from ground to chin)', 10, 0, 1, 1),
	(50, 'Uginanie przedramion (wyciąg)', 'Biceps cable curls', 10, 0, 1, 1);
/*!40000 ALTER TABLE `exercise_types` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
